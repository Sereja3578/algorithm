<?php

namespace backend\components;

use yii\helpers\Html;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Application;

trait SearchTrait
{
    use FiltersTrait;

    protected $disableColumns = [];
    protected $enableColumns = [];
    protected $separateBySheets = false;
    protected $showTitle = true;
    protected $showTableTitle = true;
    protected $showPageSummary = true;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $updatePath;

    /**
     * @var View
     */
    private $_view;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $permissionPrefix;

    /**
     * @var null|array
     */
    protected $defaultOrder;

    /**
     * @var bool
     */
    protected $showCustomSheetTitle = false;

    public static $hash = '';

    /**
     * @return bool
     */
    public function getSeparateBySheets()
    {
        return $this->separateBySheets;
    }

    /**
     * @return bool
     */
    public function getShowCustomSheetTitle()
    {
        return $this->showCustomSheetTitle;
    }

    /**
     * @return string
     */
    public function getSheetTitle()
    {
        return Yii::t('export', 'Лист');
    }

    /**
     * @return bool
     */
    public function getShowTitle()
    {
        return $this->showTitle;
    }

    /**
     * @return bool
     */
    public function getShowTableTitle()
    {
        return $this->showTableTitle;
    }

    /**
     * @return bool
     */
    public function getShowPageSummary()
    {
        return $this->showPageSummary;
    }

    /**
     * @return array
     */
    public function getSafeAttributes()
    {
        $attributes = array_flip(array_keys($this->attributes));
        foreach ($this->rules() as $rule) {
            if (is_array($rule[0])) {
                $attributes = array_merge($attributes, array_flip($rule[0]));
            } else {
                $attributes = array_merge($attributes, [$rule[0] => $this->{$rule[0]}]);
            }
        }
        return array_keys($attributes);
    }

    /**
     * @return array
     */
    public function getAfterSummaryColumns()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getDisableColumns()
    {
        return $this->disableColumns;
    }

    /**
     * @return mixed
     */
    public function getEnableColumns()
    {
        return $this->enableColumns;
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        return [];
    }

    /**
     * @param string $attribute
     * @return array
     */
    public function getFilter($attribute)
    {
        $filters = static::getFilters();

        if (isset($filters[$attribute])) {
            return $filters[$attribute];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @var int
     */
    protected $paginationSize = 20;

    /**
     * @var \common\components\ActiveQuery
     */
    protected $query;

    protected $sortRewriteAttributes = false;

    /**
     * @return \common\components\ActiveQuery
     */
    public function getRawQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    protected function getPagination()
    {
        return [
            'pageSize' => $this->getPaginationSize(),
            'pageSizeLimit' => $this->getPaginationSize(),
        ];
    }

    /**
     * @return int
     */
    protected function getPaginationSize()
    {
        return $this->paginationSize;
    }

    /**
     * @return array
     */
    protected function getSort()
    {
        return $this->sort;
    }

    /**
     * @var array
     */
    protected $sort = [];

    /**
     * @var null
     */
    protected $page = null;

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return void
     */
    protected function modifyQuery()
    {

    }

    /**
     * @return ActiveDataProvider
     */
    protected function getDataProvider()
    {
        $this->modifyQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => $this->getPagination(),
        ]);

        $pagination = $dataProvider->getPagination();
        if ($pagination && !Yii::$app instanceof Application) {
            $pagination->setPage($this->getPage());
        }

        if (($modifySort = static::getSort()) !== false) {
            $sort = $dataProvider->getSort();

            foreach ($modifySort as $attribute => $options) {
                if (property_exists($sort, $attribute)) {
                    if (is_array($options)) {
                        if ($this->sortRewriteAttributes) {
                            $sort->{$attribute} = array_merge(
                                $sort->{$attribute},
                                $options
                            );
                        } else {
                            $sort->{$attribute} = ArrayHelper::merge(
                                $sort->{$attribute},
                                $options
                            );
                        }
                    } else {
                        $sort->{$attribute} = $options;
                    }
                }
            }

            if ($this->defaultOrder) {
                $sort->defaultOrder = $this->defaultOrder;
            }

            $dataProvider->setSort($sort);
        } else {
            $dataProvider->setSort(false);
        }

        $dataProvider->refresh();
        $dataProvider->prepare(true);

        return $dataProvider;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        if (is_null($this->page) && Yii::$app instanceof Application) {
            $page = Yii::$app->getRequest()->get('page', null);
            if (!is_null($page)) {
                $this->page = $page - 1;
            }
        }

        return $this->page;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setDefaultOrder($order = null)
    {
        if ($order) {
            if (preg_match('/^([-]?)(.*)/', $order, $matches)) {
                list(, $direction, $column) = $matches;
                $this->defaultOrder = [
                    $column => $direction ? SORT_DESC : SORT_ASC,
                ];
            }
        }

        return $this;
    }

    /**
     * @param null|integer $page
     * @return $this
     */
    public function setPage($page = null)
    {
        if (!is_null($page)) {
            $this->page = $page;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getGridToolbar()
    {
        return [
            $this->getGridReset(),
        ];
    }

    /**
     * @return array
     */
    public function getGridReset()
    {
        $url = ['index'];
        foreach ($this->getPageAttributes() as $index => $attribute) {
            if (is_bool($attribute)) {
                if (!$attribute) {
                    $key = $index;
                    $url[$key] = $this->{$index};
                } else {
                    $key = $this->getShortName() . "[$index]";
                    $url[$key] = $this->{$index};
                }
                continue;
            }
            if (!empty($this->{$attribute})) {
                $key = $this->getShortName() . "[$attribute]";
                $url[$key] = $this->{$attribute};
            }
        }

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-repeat"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'class' => 'btn btn-default btn-sm',
                    'title' => Yii::t('info', 'Сбросить')
                ]
            ),
        ];
    }

    public function getPageAttributes()
    {
        return [];
    }

    /**
     * @param $permissionPrefix
     */
    public function setPermissionPrefix($permissionPrefix)
    {
        $this->permissionPrefix = $permissionPrefix;
    }

    /**
     * @return mixed
     */
    public function getPermissionPrefix()
    {
        return $this->permissionPrefix;
    }

    /**
     * @param View $view
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * @param $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return mixed
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * @param $updatePath
     */
    public function setUpdatePath($updatePath)
    {
        $this->updatePath = $updatePath;
    }

    /**
     * @return mixed
     */
    public function getUpdatePath()
    {
        return $this->updatePath;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @var
     */
    protected $disableAttributes = [];

    /**
     * @var
     */
    protected $enableAttributes = [];

    /**
     * @return mixed
     */
    public function getDisableAttributes()
    {
        return $this->disableAttributes;
    }

    /**
     * @return mixed
     */
    public function getEnableAttributes()
    {
        return $this->enableAttributes;
    }
}

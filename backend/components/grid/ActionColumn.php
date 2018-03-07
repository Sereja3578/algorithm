<?php

namespace backend\components\grid;

use backend\helpers\Html;
use kartik\grid\ActionColumn as KartikActionColumn;
use kartik\grid\ActionColumnAsset;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use Closure;
use Yii;
use yii\helpers\Json;
use yii\web\Application;

class ActionColumn extends KartikActionColumn
{

    public $dropdownOptions = ['class' => 'pull-right'];

    public $mergeHeader = false;

    public $linkOptions = [];

    public $customOptions;

    public $customVisibility = false;

    public $disabled = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_null($this->header)) {
            $this->header = Yii::t('backend', 'Действия');
        }
        if (empty($this->viewOptions)) {
            $this->viewOptions = [
                'title' => Yii::t('backend', 'Просмотр'),
                'data-toggle' => 'tooltip'
            ];
        }

        $this->viewOptions = array_merge($this->viewOptions, $this->linkOptions);

        if (empty($this->updateOptions)) {
            $this->updateOptions = [
                'title' => Yii::t('backend', 'Редактировать'),
                'data-toggle' => 'tooltip'
            ];
        }
        if (empty($this->deleteOptions)) {
            $this->deleteOptions = [
                'title' => Yii::t('backend', 'Удалить'),
                'data-toggle' => 'tooltip'
            ];
        }

        $restoreDefaultOptions = [
            'icon' => 'glyphicon-repeat',
            'message' => Yii::t('backend', "Вы уверены, что клиента необходимо восстановить?"),
            'data-toggle' => 'tooltip',
            'title' => Yii::t('backend', "Восстановить"),
        ];
        if (!isset($this->customOptions['restore'])) {
            $this->customOptions['restore'] = $restoreDefaultOptions;
        } else {
            $this->customOptions['restore'] = array_replace_recursive($restoreDefaultOptions, $this->customOptions['restore']);
        }

        $approveDefaultOptions = [
            'icon' => 'glyphicon-repeat',
            'message' => Yii::t('backend', "Вы уверены, что клиента необходимо восстановить?"),
            'data-toggle' => 'tooltip',
            'title' => Yii::t('backend', "Восстановить"),
        ];
        if (!isset($this->customOptions['approve'])) {
            $this->customOptions['approve'] = $approveDefaultOptions;
        } else {
            $this->customOptions['approve'] = array_replace_recursive($approveDefaultOptions, $this->customOptions['approve']);
        }

        $cancelDefaultOptions = [
            'icon' => 'glyphicon-remove-circle',
            'message' => Yii::t('backend', "Вы уверены, что хотите отменить?"),
            'data-toggle' => 'tooltip',
            'title' => Yii::t('backend', "Отменить"),
        ];
        if (!isset($this->customOptions['cancel'])) {
            $this->customOptions['cancel'] = $cancelDefaultOptions;
        } else {
            $this->customOptions['cancel'] = array_replace_recursive($cancelDefaultOptions, $this->customOptions['cancel']);
        }

        if (!$this->customVisibility) {
            // view visibility
            $viewIsVisible = true;
            if (array_key_exists('view', $this->visibleButtons)) {
                $viewIsVisible = $this->visibleButtons['view'];
            }
            $this->visibleButtons['view'] = function (BaseActiveRecord $model, $key, $index) use ($viewIsVisible) {
                if ($viewIsVisible instanceof Closure) {
                    return call_user_func($viewIsVisible, $model, $key, $index) && $this->checkAccess($model, 'view');
                } else {
                    return $viewIsVisible && $this->checkAccess($model, 'view');
                }
            };
            // update visibility
            $updateIsVisible = true;
            if (array_key_exists('update', $this->visibleButtons)) {
                $updateIsVisible = $this->visibleButtons['update'];
            }
            $this->visibleButtons['update'] = function (BaseActiveRecord $model, $key, $index) use ($updateIsVisible) {
                if ($updateIsVisible instanceof Closure) {
                    return call_user_func($updateIsVisible, $model, $key, $index) && $this->checkAccess($model, 'update');
                } else {
                    return $updateIsVisible && $this->checkAccess($model, 'update');
                }
            };
            // delete visibility
            $deleteIsVisible = true;
            if (array_key_exists('delete', $this->visibleButtons)) {
                $deleteIsVisible = $this->visibleButtons['delete'];
            }
            $this->visibleButtons['delete'] = function (BaseActiveRecord $model, $key, $index) use ($deleteIsVisible) {
                if ($deleteIsVisible instanceof Closure) {
                    return call_user_func($deleteIsVisible, $model, $key, $index) && $this->checkAccess($model, 'delete');
                } else {
                    return $deleteIsVisible && $this->checkAccess($model, 'delete');
                }
            };
        }

        parent::init();
    }

    /**
     * @param BaseActiveRecord $model
     * @param string $action
     * @return bool
     */
    protected function checkAccess(BaseActiveRecord $model, $action)
    {
        if (!(Yii::$app instanceof Application)) {
            return false;
        }

        $canParams = $model->getPrimaryKey(true);
        $canParams['model'] = $model;
        $url = $this->createUrl($action, $model, [], null);

        if (preg_match('#\?#', $url)) {
            $urlWithoutParams = preg_split('#\?#', $url)[0];
        } else {
            $urlWithoutParams = $url;
        }

        if (preg_match('#^/' . Yii::$app->language . '/#', $url)) {
            $urlWithoutLanguage = preg_replace('#^/' . Yii::$app->language . '/#', '', $urlWithoutParams);
            return Yii::$app->getUser()->can($urlWithoutLanguage, $canParams);
        }

        return Yii::$app->getUser()->can(substr($urlWithoutParams, 1), $canParams);
    }

    /**
     * Метод для добавления и отображения кастомных кнопок в ActionColumn.
     * Для использования нужно в getGridColumns, в action добавить массив
     * с параметрами
     *
     * 'customOptions' => [
     *      'restore' => [
     *          'icon' => 'glyphicon-repeat',
     *          'message' => 'Вы уверены, что клиента необходимо восстановить?',
     *          'data-toggle' => 'tooltip',
     *          'params' => [],
     *          'title' => 'Восстановить'
     *      ]
     * ]
     *
     * @inheritdoc
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();

        if ($this->customOptions) {
            foreach ($this->customOptions as $button => $options) {
                $this->buttons[$button] = function ($url, $model, $key) use ($options, $button) {

                    $customParams = [
                        'icon',
                        'params',
                        'message',
                        'title',
                    ];

                    // Базовые установки
                    $defaultsOptions = [
                        'title' =>  Yii::t('kvgrid','View'),
                        'data-pjax' => 'false',
                        'message' => Yii::t('kvgrid','Are you sure to delete this item?'),
                        'icon' => 'glyphicon-asterisk',
                    ];

                    $pjax = $this->grid->pjax ? true : false;
                    $pjaxContainer = $pjax ? $this->grid->pjaxSettings['options']['id'] : '';

                    if ($pjax) {
                        $defaultsOptions['data-pjax-container'] = $pjaxContainer;
                    }

                    // Если параметры установлены заменяем дефолтные
                    $options = array_replace_recursive($defaultsOptions, $options);

                    $paramsList = [];
                    foreach ($customParams as $customParam) {
                        if (isset($options[$customParam])) {
                            if ($customParam == 'params') {
                                foreach ($options[$customParam] as &$value) {
                                    $value = str_replace('{id}', $model->id, $value);
                                }
                                $paramsList['url'] = ArrayHelper::merge($url, $options[$customParam]);
                            } elseif ($customParam == 'icon') {
                                $paramsList[$customParam] = '<span class="glyphicon ' . $options[$customParam] . '"></span>';
                            } else {
                                $paramsList[$customParam] = $options[$customParam];
                            }
                            unset($options[$customParam]);
                        }
                    }

                    extract($paramsList);
                    $options['title'] = $title;
                    $css = $this->grid->options['id'] . '-action-' . $button . '';
                    Html::addCssClass($options, $css);

                    if (isset($this->disabled[$button])) {
                        $disableParams = call_user_func($this->disabled[$button], $model);
                        $options['title'] = $disableParams['title'];
                        Html::removeCssClass($options, $css);
                        $options['class'] = $disableParams['class'];
                    }

                    $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $options['title'] : $icon));
                    $view = $this->grid->getView();

                    $delOpts = Json::encode(
                        [
                            'css' => $css,
                            'pjax' => $pjax,
                            'pjaxContainer' => $pjaxContainer,
                            'lib' => ArrayHelper::getValue($this->grid->krajeeDialogSettings, 'libName', 'krajeeDialog'),
                            'msg' => $message,
                        ]
                    );

                    /* Используем стандартный Ajax метод Картика для удаления,
                    так как он подходит почти для любых действий где нужен кастомный alert */
                    ActionColumnAsset::register($view);
                    $js = "kvActionDelete({$delOpts});";
                    $view->registerJs($js);
                    $this->initPjax($js);

                    if ($this->_isDropdown) {
                        $options['tabindex'] = '-1';
                        return '<li>' . Html::a($label, $url, $options) . '</li>' . PHP_EOL;
                    } else {
                        return Html::a($label, $url, $options);
                    }
                };
            }
        }
    }
}

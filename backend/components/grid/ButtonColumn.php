<?php

namespace backend\components\grid;

use backend\helpers\Html;
use yii\db\BaseActiveRecord;
use Closure;
use Yii;
use yii\web\Application;

class ButtonColumn extends ActionColumn
{

    public $template = '{action}';

    /**
     * @var string
     */
    public $permissionName;

    /**
     * @var string
     */
    public $permissionPrefix;

    /**
     * @var string
     */
    public $buttonText;

    /**
     * @var string
     */
    public $buttonAction = 'action';

    /**
     * @var array
     */
    public $buttonOptions = [];

    /**
     * @var string|array
     */
    public $baseCssClass = ['btn', 'btn-xs'];

    /**
     * @var bool
     */
    public $disablePjax = true;

    /**
     * @var bool|Closure
     */
    public $isVisible = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dropdown = false;
        if (is_null($this->buttonText) && $this->header) {
            $this->buttonText = $this->header;
        }
        if ($this->baseCssClass) {
            Html::addCssClass($this->buttonOptions, $this->baseCssClass);
        }
        if ($this->disablePjax) {
            $this->buttonOptions['data-pjax'] = '0';
        }
        // visibility
        if (!array_key_exists('action', $this->visibleButtons)) {
            $isVisible = $this->isVisible;
            $this->isVisible = function (BaseActiveRecord $model, $key, $index) use ($isVisible) {
                if ($isVisible instanceof Closure) {
                    return call_user_func($isVisible, $model, $key, $index) && $this->checkAccess2($model);
                } else {
                    return $isVisible && $this->checkAccess2($model);
                }
            };
            $this->visibleButtons['action'] = $this->isVisible;
        }
        parent::init();
    }

    /**
     * @param BaseActiveRecord $model
     * @return bool
     */
    protected function checkAccess2(BaseActiveRecord $model)
    {
        if (!(Yii::$app instanceof Application)) {
            return false;
        }

        if ($this->permissionName || $this->permissionPrefix) {
            if ($this->permissionName) {
                $permissionName = $this->permissionName;
            } else {
                $permissionName = $this->permissionPrefix . $this->buttonAction;
            }
            $canParams = $model->getPrimaryKey(true);
            $canParams['model'] = $model;
            return Yii::$app->getUser()->can($permissionName, $canParams);
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['action'])) {
            $this->buttons['action'] = function ($url, $model, $key) {
                $content = Html::a($this->buttonText, $url, $this->buttonOptions);
                if ($this->disablePjax) { // hack
                    $content = '<span></span>' . $content;
                }
                return $content;
            };
        }
    }

    /**
     * @inheritdoc
     */
    public function createUrl($action, $model, $key, $index)
    {
        return parent::createUrl($this->buttonAction, $model, $key, $index);
    }
}

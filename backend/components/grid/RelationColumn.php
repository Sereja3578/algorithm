<?php

namespace backend\components\grid;

use common\models\Asset;
use yii\boost\db\ActiveRecord as BoostActiveRecord;
use yii\helpers\Html;
use yii\db\BaseActiveRecord;
use Yii;
use yii\web\Application;

class RelationColumn extends Select2Column
{

    /**
     * @var string
     */
    public $permissionName;

    /**
     * @var array|string|null
     */
    public $linkUrl = null;

    /**
     * @var array
     */
    public $linkOptions = [];

    /**
     * @var string|array
     */
    public $baseCssClass;

    /**
     * @var string|callable
     */
    public $suffix = '';

    /**
     * @var bool
     */
    public $disablePjax = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->baseCssClass) {
            Html::addCssClass($this->linkOptions, $this->baseCssClass);
        }
        if ($this->disablePjax) {
            $this->linkOptions['data-pjax'] = '0';
        }
        if (is_null($this->query)) {
            $filterModel = $this->grid->filterModel;
            if ($filterModel instanceof BoostActiveRecord) {
                //$this->allowNotSet = $filterModel::getTableSchema()->getColumn($this->attribute)->allowNull;
                foreach ($filterModel::singularRelations() as $relationName => $relationData) {
                    if (isset($relationData['link']['id']) && $this->attribute == $relationData['link']['id']) {
                        /* @var $relationClass \yii\db\ActiveRecordInterface|string */
                        $relationClass = $relationData['class'];
                        if (is_subclass_of($relationClass, BoostActiveRecord::className())) {
                            $this->query = $relationClass::find();
                            $this->relationClass = $relationClass;
                        }
                    }
                }

                //@fix (для relation с составными ключами)
                if (!$this->relationClass) {
                    foreach ($filterModel::singularRelations() as $relationName => $relationData) {
                        if (in_array($this->attribute, $relationData['link']) && count($relationData['link']) == 1) {
                            /* @var $relationClass \yii\db\ActiveRecordInterface|string */
                            $relationClass = $relationData['class'];
                            if (is_subclass_of($relationClass, BoostActiveRecord::className())) {
                                $this->query = $relationClass::find();
                                $this->relationClass = $relationClass;
                            }
                        }
                    }
                }
            }
            if (!$this->query && ($relationClass = $this->relationClass)) {
                $this->query = $relationClass::find();
            }
        }
        parent::init();
    }

    /**
     * @param BoostActiveRecord $model
     * @return BoostActiveRecord
     */
    protected function getRelatedModel(BoostActiveRecord $model)
    {
        foreach ($model::singularRelations() as $relationName => $relationData) {
            if (in_array($this->attribute, $relationData['link']) && $model->isRelationPopulated($relationName)) {
                $relatedModel = $model->{$relationName};
                if ($relatedModel instanceof BoostActiveRecord) {
                    return $relatedModel;
                }
            }
        }
        if ($this->relationMethod) {
            $methodName = $this->relationMethod;
            return $model->$methodName()->one();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        if (is_callable($this->suffix)) {
            $suffix = call_user_func($this->suffix, $model, $key, $index, $this);
        } else {
            $suffix = $this->suffix;
        }

        if ($model instanceof BoostActiveRecord) {
            $relatedModel = $this->getRelatedModel($model);
            if ($relatedModel) {
                if ($relatedModel instanceof Asset
                ) {
                    return $relatedModel->getDocName() . $suffix;
                } else {
                    return $relatedModel->getTitleText() . $suffix;
                }
            }
        }
        $value = parent::getDataCellValue($model, $key, $index);
        return $value ? $value . $suffix : null;
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $content = parent::renderDataCellContent($model, $key, $index);
        if ($this->linkUrl && ($model instanceof BoostActiveRecord)) {
            $relatedModel = $this->getRelatedModel($model);
            if ($relatedModel && $this->checkAccess($relatedModel)) {
                $url = array_merge((array)$this->linkUrl, $relatedModel->getPrimaryKey(true));
                $content = Html::a($content, $url, $this->linkOptions);
                if ($this->disablePjax) { // hack
                    $content = '<span></span>' . $content;
                }
            }
        }
        return $content;
    }

    /**
     * @param BaseActiveRecord $model
     * @return bool
     */
    protected function checkAccess(BaseActiveRecord $model)
    {
        if (!(Yii::$app instanceof Application)) {
            return false;
        }

        if ($this->permissionName || $this->linkUrl) {
            if ($this->permissionName) {
                $permissionName = $this->permissionName;
            } else {
                $permissionName = ltrim($this->linkUrl, '/');
            }
            $canParams = $model->getPrimaryKey(true);
            $canParams['model'] = $model;
            return Yii::$app->getUser()->can($permissionName, $canParams);
        } else {
            return false;
        }
    }
}

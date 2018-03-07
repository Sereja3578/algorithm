<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 09.02.2018
 * Time: 18:13
 */

namespace backend\components;

use common\components\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\Controller as BaseController;
use yii\web\Application as WebApplication;
use Yii;
use yii\web\ForbiddenHttpException;

class Controller extends BaseController
{
    /**
     * @var string
     */
    protected $searchClass;

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => ['created_at'],
                'updatedAtAttribute' => ['updated_at'],
                'value' => new Expression('NOW()'),
            ]
        ]);
    }

    /**
     * @return string
     */
    public function getPermissionPrefix()
    {
        $permissionPrefix = $this->id;

        if (!$this->module instanceof WebApplication) {
            $permissionPrefix = $this->module->id . '/' . $permissionPrefix;
        }

        return $permissionPrefix;
    }



    /**
     * @return bool
     */
    public function checkPermission()
    {
        $permissionPrefix = $this->getPermissionPrefix();
        return Yii::$app->user->can($permissionPrefix);
    }

    /**
     * @param string $searchModel
     */
    public function setSearchModel(string $searchModel)
    {
        $this->searchModel = $searchModel;
    }

    /**
     * @return ActiveRecord
     */
    public function getSearchModel() : ActiveRecord
    {
        return new $this->searchClass;
    }
}
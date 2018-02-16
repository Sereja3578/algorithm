<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 09.02.2018
 * Time: 18:13
 */

namespace backend\components;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\Controller as BaseController;
use yii\web\Application as WebApplication;
use Yii;
use yii\web\ForbiddenHttpException;

class Controller extends BaseController
{
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
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        /* Не проверяем действия контроллеров вне модулей,
        так как они проверяются в behavior */
        if ($this->module->id == Yii::$app->id) {
            return true;
        }

        if ($this->checkPermission()) {
            return true;
        } else {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * @return bool
     */
    public function checkPermission()
    {
        $permissionPrefix = $this->getPermissionPrefix();
        return Yii::$app->user->can($permissionPrefix);
    }
}
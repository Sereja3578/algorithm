<?php

namespace backend\components\rbac;

use developeruz\db_rbac\behaviors\AccessBehavior as BaseAccessBehavior;
use yii\base\Module;
use yii\web\Application as WebApplication;
use Yii;
use yii\web\Application;

class AccessBehavior extends BaseAccessBehavior
{
    /**
     * @var string
     */
    private $permissionName;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [Module::EVENT_BEFORE_ACTION => 'interception'];
    }

    /**
     * @param \yii\base\ActionEvent $event
     */
    public function interception($event)
    {
        $action = $event->action;
        $controller = $action->controller;
        $module = $controller->module;
        $permissionName = $controller->id . '/' . $action->id;
        if (!$module instanceof WebApplication) {
            $permissionName = $module->id . '/' . $permissionName;
        }
        $this->permissionName = $permissionName;
        parent::interception($event);
    }

    /**
     * @param array $route
     * @return bool
     */
    protected function checkPermission($route)
    {
        if (!(Yii::$app instanceof Application)) {
            return false;
        }



        return Yii::$app->getUser()->can($this->permissionName, $route[1]);
    }
}

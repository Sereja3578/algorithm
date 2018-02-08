<?php

namespace console\controllers;

use common\models\Admin;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yii\rbac\DbManager;
use DirectoryIterator;
use ReflectionClass;
use yii\db\Exception as DbException;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use Yii;
use phpDocumentor\Reflection\DocBlock;

class RbacController extends Controller
{

    /**
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = new DbManager(['db' => Yii::$app->getDb()]);
        $authManager->init();
        return $authManager;
    }

    /**
     * @return array
     */
    protected function getRoleList()
    {
        return [
            'admin' => Yii::t('rbac', 'Администратор'),
            'common_user' => Yii::t('rbac', 'Пользователь сайта'),
        ];
    }

    /**
     * @return array
     */
    protected function getPermissionList()
    {
        return [
            'common_permission' => Yii::t('rbac', 'Общее разрешение'),
        ];
    }

    /**
     * @return array
     */
    protected function getPermissionPermissions()
    {
        return [
            'common_permission' => [
                'gridview',
                'datecontrol',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getRolePermissions()
    {
        return [
            'admin' => [
                'common_user',
                'user',
                'gii',
                'debug',
                'auth'
            ],
            'common_user' => [
                'common_permission'
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getBackendConfig()
    {
        $commonConfigPath = Yii::getAlias('@common/config');
        $backendConfigPath = Yii::getAlias('@backend/config');
        return ArrayHelper::merge(
            require($commonConfigPath . '/main.php'),
            require($commonConfigPath . '/main-local.php'),
            require($backendConfigPath . '/main.php'),
            require($backendConfigPath . '/main-local.php')
        );
    }

    /**
     * @var array id => [class, ns, path, description, ruleName, controllerList]
     */
    private $moduleList;

    /**
     * @return array id => [class, ns, path, description, ruleName, controllerList]
     */
    protected function getModuleList()
    {
        if (is_null($this->moduleList)) {
            $this->moduleList = [];
            foreach ($this->getModuleClassNames() as $id => $className) {
                $this->moduleList[$id] = [
                    'class' => $className,
                    'ns' => $this->getModuleNamespace($id),
                    'path' => $this->getModulePath($id),
                    'description' => $this->getModuleDescription($id),
                    'ruleName' => $this->getModuleRuleName($id),
                    'controllerList' => $this->getControllerList($id)
                ];
            }
        }
        return $this->moduleList;
    }

    /**
     * @var array id => className
     */
    private $moduleClassNames;

    /**
     * @return array id => className
     */
    protected function getModuleClassNames()
    {
        if (is_null($this->moduleClassNames)) {
            $this->moduleClassNames = [];
            $backendConfig = $this->getBackendConfig();
            foreach ($backendConfig['modules'] as $id => $config) {
                if (is_string($config)) {
                    $className = $config;
                } elseif (is_array($config) && array_key_exists('class', $config)) {
                    $className = $config['class'];
                } else {
                    throw new InvalidConfigException('Bad module config.');
                }
                if (class_exists($className)) {
                    $this->moduleClassNames[$id] = $className;
                } else {
                    throw new InvalidConfigException('Bad class name.');
                }
            }
        }
        return $this->moduleClassNames;
    }

    /**
     * @param string $moduleId
     * @return string|null
     */
    protected function getModuleClassName($moduleId)
    {
        $classNames = $this->getModuleClassNames();
        if (array_key_exists($moduleId, $classNames)) {
            return $classNames[$moduleId];
        } else {
            return null;
        }
    }

    /**
     * @param string $moduleId
     * @return string|null
     */
    protected function getModuleNamespace($moduleId)
    {
        $className = $this->getModuleClassName($moduleId);
        if ($className) {
            $reflectionClass = new ReflectionClass($className);
            return $reflectionClass->getNamespaceName();
        } else {
            return null;
        }
    }

    /**
     * @param string $moduleId
     * @return string|null
     */
    protected function getModulePath($moduleId)
    {
        $className = $this->getModuleClassName($moduleId);
        if ($className) {
            $reflectionClass = new ReflectionClass($className);
            return dirname($reflectionClass->getFileName());
        } else {
            return null;
        }
    }

    /**
     * @param string $moduleId
     * @return string|null
     */
    protected function getModuleDescription($moduleId)
    {
        $className = $this->getModuleClassName($moduleId);
        if ($className) {
            $description = null;
            $reflectionClass = new ReflectionClass($className);
            $docComment = $reflectionClass->getDocComment();
            if ($docComment) {
                $docBlock = new DocBlock($docComment);
                $shortDescription = $docBlock->getDescription();
                if ($shortDescription) {
                    $description = rtrim($shortDescription, '.');
                }
            }
            return $description;
        } else {
            return null;
        }
    }

    /**
     * @param string $moduleId
     * @return string|null
     */
    protected function getModuleRuleName($moduleId)
    {
        $ns = $this->getModuleNamespace($moduleId);
        if ($ns) {
            $className = $ns . '\rbac\Rule';
            if (class_exists($className)) {
                return $className;
            }
        }
        return null;
    }

    /**
     * @param string $moduleId
     * @return array id => [class, description, ruleName, actionList]
     */
    protected function getControllerList($moduleId)
    {
        $controllerList = [];
        foreach ($this->getControllerClassNames($moduleId) as $id => $className) {
            $controllerList[$id] = [
                'class' => $className,
                'description' => $this->getControllerDescription($moduleId, $id),
                'ruleName' => $this->getControllerRuleName($moduleId, $id),
                'actionList' => $this->getActionList($moduleId, $id)
            ];
        }
        return $controllerList;
    }

    /**
     * @var array moduleId => [id => className]
     */
    private $controllerClassNames = [];

    /**
     * @param string $moduleId
     * @return array id => className
     */
    protected function getControllerClassNames($moduleId)
    {
        if (!array_key_exists($moduleId, $this->controllerClassNames)) {
            $this->controllerClassNames[$moduleId] = [];
            $ns = $this->getModuleNamespace($moduleId);
            if ($ns) {
                $path = $this->getModulePath($moduleId) . '/controllers';
                if (is_dir($path)) {
                    foreach (new DirectoryIterator($path) as $directory) {
                        $filename = $directory->getFilename();
                        if (preg_match('~^((\w+)Controller)\.php$~', $filename, $match)) {
                            $className = $ns . '\controllers\\' . $match[1];
                            if (class_exists($className)) {
                                $id = Inflector::camel2id($match[2]);
                                $this->controllerClassNames[$moduleId][$id] = $className;
                            }
                        }
                    }
                }
            }
        }
        return $this->controllerClassNames[$moduleId];
    }

    /**
     * @param string $moduleId
     * @param string $controllerId
     * @return string|null
     */
    protected function getControllerClassName($moduleId, $controllerId)
    {
        $classNames = $this->getControllerClassNames($moduleId);
        if (array_key_exists($controllerId, $classNames)) {
            return $classNames[$controllerId];
        } else {
            return null;
        }
    }

    /**
     * @param string $moduleId
     * @param string $controllerId
     * @return string|null
     */
    protected function getControllerDescription($moduleId, $controllerId)
    {
        $className = $this->getControllerClassName($moduleId, $controllerId);
        if ($className) {
            $description = null;
            $reflectionClass = new ReflectionClass($className);
            $docComment = $reflectionClass->getDocComment();
            if ($docComment) {
                $docBlock = new DocBlock($docComment);
                $shortDescription = $docBlock->getDescription();
                if ($shortDescription) {
                    $description = rtrim($shortDescription, '.');
                }
            }
            return $description;
        } else {
            return null;
        }
    }

    /**
     * @param string $moduleId
     * @param string $controllerId
     * @return string|null
     */
    protected function getControllerRuleName($moduleId, $controllerId)
    {
        $ns = $this->getModuleNamespace($moduleId);
        if ($ns) {
            $className = $ns . '\rbac\\' . Inflector::id2camel($controllerId) . 'Rule';
            if (class_exists($className)) {
                return $className;
            }
        }
        return $this->getModuleRuleName($moduleId);
    }

    /**
     * @param string $moduleId
     * @param string $controllerId
     * @return array id => [description, ruleName]
     */
    protected function getActionList($moduleId, $controllerId)
    {
        $actionList = [];
        $className = $this->getControllerClassName($moduleId, $controllerId);
        if ($className) {
            $reflectionClass = new ReflectionClass($className);
            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $name = $reflectionMethod->getName();
                if (preg_match('~^action(\w{2,})$~', $name, $match)) {
                    $id = Inflector::camel2id($match[1]);
                    $description = null;
                    $docComment = $reflectionMethod->getDocComment();
                    if ($docComment) {
                        $docBlock = new DocBlock($docComment);
                        $shortDescription = $docBlock->getDescription();
                        if ($shortDescription) {
                            $description = rtrim($shortDescription, '.');
                        }
                    }
                    $actionList[$id] = [
                        'description' => $description,
                        'ruleName' => $this->getActionRuleName($moduleId, $controllerId, $id)
                    ];
                }
            }
        }
        return $actionList;
    }

    /**
     * @param string $moduleId
     * @param string $controllerId
     * @param string $actionId
     * @return string|null
     */
    protected function getActionRuleName($moduleId, $controllerId, $actionId)
    {
        $ns = $this->getModuleNamespace($moduleId);
        if ($ns) {
            $classShortName = Inflector::id2camel($controllerId) . Inflector::id2camel($actionId) . 'Rule';
            $className = $ns . '\rbac\actions\\' . $classShortName;
            if (class_exists($className)) {
                return $className;
            }
        }
        return $this->getControllerRuleName($moduleId, $controllerId);
    }

    /**
     * Updates auth tables.
     * @return int
     */
    public function actionInit()
    {
        $moduleList = $this->getModuleList();

        $authManager = $this->getAuthManager();

        /* @var $assignments \yii\rbac\Assignment[] */
        $assignments = [];
        try {
            foreach (Admin::find()->all() as $admin) {
                $assignments = array_merge($assignments, array_values($authManager->getAssignments($admin->id)));
            }
        } catch (DbException $e) {
        }

        $authManager->removeAllAssignments();
        $authManager->db->createCommand("SET foreign_key_checks = 0;")->execute();
        // remove generated
        $authManager->db->createCommand("
DELETE aic FROM `auth_item_child` aic
 INNER JOIN `auth_item` ai ON ai.name = aic.parent AND ai.data = :data
 INNER JOIN `auth_item` ai2 ON ai2.name = aic.child AND ai2.data = :data
        ", ['data' => serialize('generated')])->execute();
        $authManager->db->createCommand()->delete(
            $authManager->itemTable,
            'data = :data',
            ['data' => serialize('generated')]
        )->execute();

        $this->stdout('Role list:' . PHP_EOL, Console::FG_GREEN);
        foreach ($this->getRoleList() as $roleName => $roleDescription) {
            $this->stdout($roleName);
            $this->stdout(' ' . $roleDescription . PHP_EOL, Console::FG_YELLOW);

            $role = $authManager->createRole($roleName);
            $role->description = $roleDescription;
            $role->data = 'generated';
            $authManager->add($role);
        }
        $this->stdout(PHP_EOL);

        $this->stdout('Permission list:' . PHP_EOL, Console::FG_GREEN);
        foreach ($this->getPermissionList() as $permissionName => $permissionDescription) {
            $this->stdout($permissionName);
            $this->stdout(' ' . $permissionDescription . PHP_EOL, Console::FG_YELLOW);

            $permission = $authManager->createPermission($permissionName);
            $permission->description = $permissionDescription;
            $permission->data = 'generated';
            $authManager->add($permission);
        }
        $this->stdout(PHP_EOL);

        $this->stdout('Module list:' . PHP_EOL, Console::FG_GREEN);
        foreach ($moduleList as $moduleId => $moduleData) {
            $this->stdout($moduleId);
            $this->stdout(' ' . $moduleData['description'] . PHP_EOL, Console::FG_PURPLE);

            $modulePermission = $authManager->createPermission($moduleId);
            $modulePermission->description = $moduleData['description'];
            $modulePermission->ruleName = $moduleData['ruleName'];
            $modulePermission->data = 'generated';
            $authManager->add($modulePermission);

            foreach ($moduleData['controllerList'] as $controllerId => $controllerData) {
                $moduleControllerId = $moduleId . '/' . $controllerId;
                $this->stdout($moduleControllerId);
                $this->stdout(' ' . $controllerData['description'] . PHP_EOL, Console::FG_CYAN);

                $controllerPermission = $authManager->createPermission($moduleControllerId);
                $controllerPermission->description = $controllerData['description'];
                $controllerPermission->ruleName = $controllerData['ruleName'];
                $controllerPermission->data = 'generated';
                $authManager->add($controllerPermission);
                $authManager->addChild($modulePermission, $controllerPermission);

                foreach ($controllerData['actionList'] as $actionId => $actionData) {
                    $moduleControllerActionId = $moduleControllerId . '/' . $actionId;
                    $this->stdout($moduleControllerActionId);
                    $this->stdout(' ' . $actionData['description'] . PHP_EOL, Console::FG_YELLOW);

                    $actionPermission = $authManager->createPermission($moduleControllerActionId);
                    $actionPermission->description = $actionData['description'];
                    $actionPermission->ruleName = $actionData['ruleName'];
                    $actionPermission->data = 'generated';
                    $authManager->add($actionPermission);
                    $authManager->addChild($controllerPermission, $actionPermission);
                }
            }
        }
        $this->stdout(PHP_EOL);

        foreach ($this->getPermissionPermissions() as $permissionName => $subPermissionNames) {
            $permission = $authManager->getPermission($permissionName);
            if ($permission) {
                foreach ($subPermissionNames as $subPermissionName) {
                    $subPermission = $authManager->getPermission($subPermissionName);
                    if ($subPermission) {
                        $authManager->addChild($permission, $subPermission);
                    }
                }
            }
        }

        $roleList = $this->getRoleList();
        foreach ($this->getRolePermissions() as $roleName => $roleOrPermissionNames) {
            $role = $authManager->getRole($roleName);
            if ($role) {
                foreach ($roleOrPermissionNames as $roleOrPermissionName) {
                    if (array_key_exists($roleOrPermissionName, $roleList)) {
                        $childRole = $authManager->getRole($roleOrPermissionName);
                        if ($childRole) {
                            $authManager->addChild($role, $childRole);
                        }
                    } else {
                        $permission = $authManager->getPermission($roleOrPermissionName);
                        if ($permission) {
                            $authManager->addChild($role, $permission);
                        }
                    }
                }
            }
        }

        foreach ($assignments as $assignment) {
            $item = $authManager->getRole($assignment->roleName);
            if (!$item) {
                $item = $authManager->getPermission($assignment->roleName);
            }
            if ($item) {
                $authManager->assign($item, $assignment->userId);
            }
        }

        // remove unusable relations
        $authManager->db->createCommand("
DELETE aic FROM `auth_item_child` aic
 LEFT JOIN `auth_item` ai ON ai.name = aic.parent
 LEFT JOIN `auth_item` ai2 ON ai2.name = aic.child
 WHERE ai.name IS NULL OR ai2.name IS NULL;
        ")->execute();
        $authManager->db->createCommand("SET foreign_key_checks = 1;")->execute();

        return 0;
    }
}

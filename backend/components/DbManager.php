<?php

namespace backend\components;

use Yii;
use yii\db\Query;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

class DbManager extends \yii\rbac\DbManager
{

    private $assignments = [];

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        // Avoid multiple queries per request
        if (!isset($this->assignments[$userId])) {
            $this->assignments[$userId] = parent::getAssignments($userId);
        }
        return $this->assignments[$userId];
    }

    protected $item = [];

    /**
     * @inheritdoc
     */
    protected function getItem($name)
    {
        if (!isset($this->item[$name])) {
            $this->item[$name] = parent::getItem($name);
        }

        return $this->item[$name];
    }

    protected $parents = [];

    /**
     * @inheritdoc
     */
    protected function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        if (($item = $this->getItem($itemName)) === null) {
            return false;
        }

        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        $parents = $this->getParents($itemName);
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $itemName
     * @return mixed
     */
    protected function getParents($itemName)
    {
        if (!isset($this->parents[$itemName])) {
            $query = new Query;
            $this->parents[$itemName] = $query->select(['parent'])
                ->from($this->itemChildTable)
                ->where(['child' => $itemName])
                ->column($this->db);
        }

        return $this->parents[$itemName];
    }

    public function clearCache()
    {
        $this->item = [];
        $this->parents = [];
        $this->assignments = [];
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userId)
    {
        $this->clearCache();
        return parent::assign($role, $userId);
    }

    /**
     * @inheritdoc
     */
    public function revoke($role, $userId)
    {
        $this->clearCache();
        return parent::revoke($role, $userId);
    }

}
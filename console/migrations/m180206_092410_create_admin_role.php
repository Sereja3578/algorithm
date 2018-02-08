<?php

use yii\db\Migration;
use yii\rbac\DbManager;
use common\models\Admin;

/**
 * Class m180206_092407_add_admin_table
 */
class m180206_092410_create_admin_role extends Migration
{
    const TABLE_NAME = 'admin';

    /**
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = new DbManager(['db' => $this->db]);
        $authManager->init();
        return $authManager;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $authManager = $this->getAuthManager();
        $security = Yii::$app->getSecurity();
        $username = 'admin';

        $this->insert(self::TABLE_NAME, [
            'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
            'username' => $username,
            'password_hash' => $security->generatePasswordHash('123456'),
            'email' => $username . '@example.local',
            'status' => Admin::STATUS_ACTIVE
        ]);

        $adminId = $this->db->getLastInsertID();
        $role = $authManager->getRole($username);

        if ($role) {
            $authManager->assign($role, $adminId);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(self::TABLE_NAME, ['username' => 'admin']);
    }
}

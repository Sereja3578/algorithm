<?php

use yii\db\Migration;

/**
 * Class m180206_092407_add_admin_table
 */
class m180206_092407_add_admin_table extends Migration
{
    const TABLE_NAME = 'admin';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $query = $this->db->createCommand("CREATE TABLE " . self::TABLE_NAME . " LIKE user");
        $query->execute();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}

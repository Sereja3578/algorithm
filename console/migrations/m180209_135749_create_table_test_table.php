<?php

use console\components\Migration;

/**
 * Handles the creation of table `table_test`.
 */
class m180209_135749_create_table_test_table extends Migration
{

    const TABLE_NAME = 'table_test';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
        ]);
}

    /**
    * @inheritdoc
    */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}

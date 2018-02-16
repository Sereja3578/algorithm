<?php

use console\components\Migration;

/**
 * Handles the creation of table `game`.
 */
class m180215_162332_create_game_table extends Migration
{

    const TABLE_NAME = 'game';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Название игры'),
            'number_steps' => $this->tinyInteger(2)->notNull()->comment('Число шагов'),
            'created_at' => $this->createdAtShortcut(),
            'updated_at' => $this->updatedAtShortcut(),
        ]);

        $this->batchInsert(self::TABLE_NAME, ['name', 'number_steps'], [
            ['Игра в один шаг', 1],
            ['Игра в два шага', 2],
            ['Игра в три шага', 3],
            ['Игра в четыре шага', 4],
            ['Игра в пять шагов', 5],
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

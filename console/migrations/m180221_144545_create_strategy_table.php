<?php

use console\components\Migration;

/**
 * Handles the creation of table `strategy`.
 */
class m180221_144545_create_strategy_table extends Migration
{

    const TABLE_NAME = 'strategy';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'algorithm_params_id' => $this->integer(11)->unsigned()->notNull()->comment("Параметры алгоритма"),
            'timestamp' => $this->createdAtShortcut()->comment("Время достижения результата"),
            'iteration_number' => $this->integer(11)->unsigned()->notNull()->comment("Номер итерации"),
            'money_amount' => $this->decimal(23, 8)->unsigned()->notNull()->comment("Сумма денег на момент достижения результата"),
            'game_id' => $this->integer(11)->unsigned()->notNull()->comment("Игра"),
            'rate_amount' => $this->decimal(23, 8)->unsigned()->notNull()->comment("Ставка"),
            'forecast' => $this->string(10)->notNull()->comment("Прогноз"),
            'result' => $this->tinyInteger(1)->unsigned()->notNull()->comment("Результат"),
            'best_strategy' => $this->tinyInteger(1)->unsigned()->notNull()->comment("Лучшая стратегия"),
        ]);

        $this->addForeignKey(
            'fk_' . self::TABLE_NAME . '-algorithm_params_id',
                self::TABLE_NAME,
                'algorithm_params_id',
                'algorithm_params',
                'id'
        );

        $this->addForeignKey(
            'fk_' . self::TABLE_NAME . '-game_id',
            self::TABLE_NAME,
            'game_id',
            'game',
            'id'
        );
    }

    /**
    * @inheritdoc
    */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}

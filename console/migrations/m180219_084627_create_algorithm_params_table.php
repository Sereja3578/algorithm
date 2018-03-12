<?php

use console\components\Migration;

/**
 * Handles the creation of table `algorithm_params`.
 */
class m180219_084627_create_algorithm_params_table extends Migration
{

    const TABLE_NAME = 'algorithm_params';
    const ASSET_TABLE_NAME = 'asset';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTableWithComment(self::ASSET_TABLE_NAME, [
            'id' => $this->primaryKey(11),
            'name' => $this->string(25)->notNull()->comment('Название ассета'),
            'code' => $this->string(25)->notNull()->comment('Код ассета'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновленов в'),
        ], 'Ассеты');

        $this->createTableWithComment(self::TABLE_NAME, [
            'id' => $this->primaryKey(11),
            'iterations' => $this->integer(10)->unsigned()->notNull()->defaultValue(200000)->comment('Количество итераций'),
            'k_lucky' => $this->double(1)->unsigned()->notNull()->defaultValue(1.3)->comment('Коэффициент удачливости игрока'),
            'asset_id' => $this->integer(11)->unsigned()->notNull()->comment('Валютная пара'),
            'amount_start' => $this->decimal(23, 8)->unsigned()->notNull()->comment('Начальная сумма денег'),
            'amount_end' => $this->decimal(23, 8)->unsigned()->notNull()->comment('Конечная сумма денег'),
            't_start' => $this->createdAtShortcut()->notNull()->comment('Начальное время'),
            't_end' => $this->createdAtShortcut()->notNull()->comment('Конечное время'),
            'deviation_from_amount_end' => $this->double(1)->unsigned()->defaultValue(1)->notNull()->comment('Допустимое отклонение текущей суммы от конечной'),
            'games' => $this->string(255)->notNull()->comment('Игры и с указанием шанса'),
            't_next_start_game' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(5)->comment('Время задержки между играми'),
            'rates' => $this->string(255)->notNull()->comment('Ставки через запятую'),
            'number_rates' => $this->tinyInteger(2)->unsigned()->notNull()->defaultValue(2)->comment('Максимальное число ставок'),
            'rate_coef' => $this->double(1)->unsigned()->notNull()->defaultValue(1.2)->comment('Коэффициент повышения ставки'),
            'probability_play' => $this->double(1)->unsigned()->notNull()->defaultValue(0.7)->comment('Вероятность начала игры'),
            'created_at' => $this->createdAtShortcut()->comment('Создано в'),
            'updated_at' => $this->updatedAtShortcut()->comment('Обновленов в'),
        ], 'Параметры алгоритмов');

        $this->addForeignKey(
            'fk_' . self::TABLE_NAME . '-asset_id', self::TABLE_NAME,
            'asset_id',
            self::ASSET_TABLE_NAME,
            'id'
        );

        $this->batchInsert(
            self::ASSET_TABLE_NAME,
            ['name', 'code'],
            [
                ['EUR/USD', 'EURUSD'],
                ['EUR/RUB', 'EURRUB'],
                ['BTC/USD', 'BTCUSD'],
                ['USD/RUB', 'USDRUB'],
            ]
        );

    }

    /**
    * @inheritdoc
    */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
        $this->dropTable(self::ASSET_TABLE_NAME);
    }
}

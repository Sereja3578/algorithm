<?php

use console\components\Migration;

/**
 * Class m180228_204015_add_column_use_fake_coefs_to_algorithm_params_table
 */
class m180228_204015_add_column_use_fake_coefs_to_algorithm_params_table extends Migration
{
    const TABLE_NAME = 'algorithm_params';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            self::TABLE_NAME,
            'use_fake_coefs',
            $this->tinyInteger(1)->notNull()->defaultValue(0)->
            comment('Использовать фейковые коэффициенты')->after('probability_play')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(self::TABLE_NAME, 'use_fake_coefs');
    }
}

<?php

use console\components\Migration;

/**
 * Class m180209_082448_add_token_expires_at_column_in_admin_and_user_tables
 */
class m180209_082448_update_admin_and_user_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $tablesNames = [
            'user',
            'admin'
        ];

        foreach ($tablesNames as $tableName) {
            $this->addColumn(
                $tableName,
                'password_reset_token_expires_at',
                $this->
                timestamp()->
                null()->
                comment("Время окончания действия токена восстановления пароля")->
                after("password_reset_token")
            );

            // Создаем новые колонки с timestamp
            $this->addColumn($tableName, 'created_at_timestamp', $this->createdAtShortcut()->after('created_at')->comment('Созданов в'));
            $this->addColumn($tableName, 'updated_at_timestamp', $this->updatedAtShortcut()->after('updated_at')->comment('Обновлено в'));

            // Переносим данные в новые колонки и меняем их тип на timestamp
            $this->db->createCommand(<<<SQL
UPDATE $tableName
SET `created_at_timestamp` = FROM_UNIXTIME(`created_at`), `updated_at_timestamp` = FROM_UNIXTIME(`updated_at`)
SQL
)->execute();

            // Удаляем старые колонки
            $this->dropColumn($tableName, 'created_at');
            $this->dropColumn($tableName, 'updated_at');

            // Переименовываем новые колонки
            $this->renameColumn($tableName, 'created_at_timestamp', 'created_at');
            $this->renameColumn($tableName, 'updated_at_timestamp', 'updated_at');
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180209_082448_update_admin_and_user_tables cannot be reverted.\n";

        return false;
    }
}

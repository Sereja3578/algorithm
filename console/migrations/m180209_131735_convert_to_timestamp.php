<?php

use console\components\Migration;

/**
 * Class m180209_131733_convert_to_timestamp
 */
class m180209_131735_convert_to_timestamp extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tablesNames = [
            'auth_assignment',
            'auth_item',
            'auth_rule',
            'migration'
        ];

        $columnsNames = [
            'created_at',
            'updated_at',
            'apply_time'
        ];

        foreach ($tablesNames as $tableName) {
            foreach ($columnsNames as $columnName) {
                if ($this->hasColumn($tableName, $columnName)) {

                    $newColumnName = $columnName . '_timestamp';

                    if ($columnName == 'created_at') {
                        // Создаем новые колонки с timestamp
                        $this->addColumn($tableName, $newColumnName, $this->createdAtShortcut()->after('created_at')->comment('Созданов в'));
                    } elseif ($columnName == 'updated_at') {
                        // Создаем новые колонки с timestamp
                        $this->addColumn($tableName, $newColumnName, $this->updatedAtShortcut()->after('updated_at')->comment('Обновлено в'));
                    } else {
                        $this->addColumn($tableName, $newColumnName, $this->createdAtShortcut()->comment('Созданов в'));
                    }

                    // Переносим данные в новые колонки и меняем их тип на timestamp
                    $this->db->createCommand(<<<SQL
UPDATE $tableName
SET $newColumnName = FROM_UNIXTIME($columnName)
SQL
                    )->execute();

                    // Удаляем старые колонки
                    $this->dropColumn($tableName, $columnName);

                    // Переименовываем новые колонки
                    $this->renameColumn($tableName, $columnName . '_timestamp', $columnName);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180209_131735_convert_to_timestamp cannot be reverted.\n";

        return false;
    }
}

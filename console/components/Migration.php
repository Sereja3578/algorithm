<?php

/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 09.02.2018
 * Time: 14:25
 */

namespace console\components;

use yii\behaviors\TimestampBehavior;
use yii\boost\db\Migration as BaseMigration;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Migration  extends BaseMigration
{
    /**
     * @param $tableName
     * @param $columnName
     * @return bool
     */
    public function hasColumn ($tableName, $columnName) {
        $tableColumns = $this->db->getTableSchema($tableName)->columnNames;
        if (in_array($columnName, $tableColumns)) {
            return true;
        }
        return false;
    }
}
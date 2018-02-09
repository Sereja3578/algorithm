<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 09.02.2018
 * Time: 17:22
 */

namespace console\controllers;

use yii\console\controllers\MigrateController as BaseMigrateController;

class MigrateController extends BaseMigrateController
{
    /**
     * @inheritdoc
     */
    protected function addMigrationHistory($version)
    {
        $command = $this->db->createCommand();
        $command->insert($this->migrationTable, [
            'version' => $version,
        ])->execute();
    }
}
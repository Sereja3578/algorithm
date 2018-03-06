<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNsReport) {
    $modelFullClassName = '\\' . $generator->nsReport . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNsReport ?>;

use common\models\report\query\base\<?= $className . 'Base'?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 * 
 * @author Pavel Veselov
 */
class <?= $className ?> extends <?= $className . 'Base'?>

{
    
}

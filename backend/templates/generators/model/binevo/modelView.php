<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";

$__baseClass = $className . 'Base';
$__baseClassNs = $generator->baseClassNsReport . $__baseClass;
$__queryClass = $className . 'Query';
$__baseClassPath = $generator->baseClassNsReport;
$__queryClassPath = $generator->queryClassNsReport;

?>

namespace <?= $generator->nsReport ?>;

use Yii;
use <?=$__queryClassPath . $__queryClass?>;
use <?=$__baseClassPath . $__baseClass?>;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 * 
 * @author Pavel Veselov
 */
class <?= $className ?> extends <?= $__baseClass?>
{

}

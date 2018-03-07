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

use yii\db\Schema;

$className = $className . 'Base';

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>\base;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @author Pavel Veselov
 *
 * @see <?= $modelFullClassName . "Query\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    <?php
    if ($modelClassName !== 'Migration') {
        $__className = "common\\models\\{$modelClassName}";
        if (class_exists($__className)) {
            $__classModel = new $__className();
            $__keys = $__classModel->getTableSchema()->primaryKey;

            if (sizeof($__classModel->getTableSchema()->primaryKey) > 0) {
    ?>/**<?php
foreach ($__keys as $key) {
    echo "\n"; ?>     * @param <?= $__classModel->getTableSchema()->columns[$key]->phpType?> $<?= $key?><?php
}
    ?><?php echo "\n"; ?>     * @return self
     */
    public function pk($<?= implode(', $', $__keys)?>)
    {
        return $this<?php
        foreach ($__keys as $key) {
            echo ""; ?>->andWhere([$this->a('<?= $key?>') => $<?= $key?>])<?php
        }
        echo "";
        ?>;
    }
    <?php
            }
        }
    }
    ?>
    
    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    
    <?php
    if (sizeof($tableSchema->primaryKey) > 0) {
    ?>
/**
    * @inheritdoc
    */
    public function listItems()
    {
    <?php

    $isset_varchar = false;
    $isset_named = false;
    $varchar_field = [];
            
    foreach ($tableSchema->columns as $column) {
        if (in_array($column->name, ['name', 'username'])) {
            $isset_named = true;
        }
                
        $ctype = explode('(', $column->dbType);
        if (in_array($ctype[0], ['varchar', 'char', 'text']) && !in_array($column->name, ['auth_key'])) {
            $isset_varchar = true;
            $varchar_field[] = $column->name;
        }
    }
            
    if ($isset_named === true || $isset_varchar === true) {
        if ($isset_named === true) {
            if (in_array($modelClassName, array('User'))) {
                $field_name = 'username';
            } else {
                $field_name = 'name';
            }
        } else {
            $field_name = $varchar_field[0];
        }
        ?>  return $this->select([
            $this->a('<?=$field_name?> name'),
            $this->a('<?=$tableSchema->primaryKey[0]?> id')
        ])->indexBy('id')->orderBy('id');
        <?php
    } else {
        ?>  return $this->select([
        $this->a('<?=$tableSchema->primaryKey[0]?> name'),
        $this->a('<?=$tableSchema->primaryKey[0]?> id')
        ])->indexBy('id')->orderBy('id');
    <?php
    }
    ?>}
    <?php
    }
    ?>
    
    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

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

$className = $className . 'Base';

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNsReport) {
    $modelFullClassName = '\\' . $generator->nsReport . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNsReport ?>\base;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @author Pavel Veselov
 *
 * @see <?= $modelFullClassName . "Query\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClassReport, '\\') . "\n" ?>
{
    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    
    /**
     * @inheritdoc
     */
    public function listItems()
    {
    <?php
        foreach ($tableSchema->columns as $column) {
            $field_name = $column->name;
            break;
        }
    ?>  return $this->select([
            $this->a('<?=$field_name?> name'),
            $this->a('<?=$field_name?> id')
        ])->indexBy('id')->orderBy('id');
    }
    
    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

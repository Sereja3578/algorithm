<?php

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";

$_queryAbstract = $className . 'Query';
$className = $className . 'Base';
?>

namespace <?= $generator->ns ?>\base;

<?= $generator->modelNS($tableSchema, $tableName) ?>

<?= $generator->modelPhpDocs($tableSchema, $tableName, $relations) ?>
class <?= $className ?> extends ActiveRecord
{
    <?= $generator->statusConstants($tableSchema, $tableName) ?>

    <?= $generator->generateTimestampAttributes($tableSchema, $tableName) ?>
    <?= $generator->generateStatusAttributes($tableSchema, $tableName) ?>
    <?php
    if ($className === 'UserBase') {
    ?>
    /**
 * @return string $name
 */
    public function getName() {
    return $this->id . ': ' . $this->username;
    }
    <?php
    }
    ?>

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
    
<?= $generator->modelBehaviors($tableSchema, $tableName) ?>
<?php if ($generator->db !== 'db') : ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        <?php
            $columnsNull = [];
            
        foreach ($tableSchema->columns as $column) {
            if ($column->allowNull) {
                $columnsNull[] = $column->name;
            }
        }
            
        if (sizeof($columnsNull) > 0) {
            $rules = \yii\helpers\ArrayHelper::merge([
            "[['" . implode("', '", $columnsNull) . "'], 'default', 'value' => NULL]"
            ], $rules);
        }
            
        ?>return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label) : ?>
            <?= "'$name' => " . $generator->generateStringMessage($label, $tableName) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation) : ?>

    /**
     * @return \common\components\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName) : ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif;

    $rel[$tableName] = array();

    foreach ($relations as $name => $relation) :
    $pos = strpos($relation[0], 'viaTable');
    
if ($pos === false) {
    $relationKeys = $generator->getRelationKeys($relation[1], $tableName, $tableSchema);
    if (sizeof($relationKeys) > 0) {
        if (!isset($rel[$tableName][$relation[1]])) {
?>    /**
     * Create New <?=$relation[1] . "\n"?>
     *
     * @return \common\models\<?=$relation[1] . "\n"?>
     */
    public function new<?= $relation[1] ?>()
    {
        $<?=  lcfirst($relation[1])?> = new <?=$relation[1]?>();
<?php
            
    foreach ($relationKeys as $destination_id => $source_id) {
        ?>        $<?=  lcfirst($relation[1])?>-><?=$destination_id?> = $this-><?=$source_id?>;
        <?php
    }
    ?>

        return $<?=  lcfirst($relation[1])?>;
    }
    
    <?php  $rel[$tableName][$relation[1]] = true;
        }
    }
} ?>    
<?php endforeach; ?>    
}

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
/* @var $isTable boolean */

echo "<?php\n";

$_queryAbstract = $className . 'Query';
$modelClass = $className;
$className = $className . 'Base';
?>

namespace <?= $generator->nsReport ?>\base;

<?= $generator->modelNS($tableSchema, $tableName, $isTable) ?>

<?php
foreach ($tableSchema->columns as $column) {
    if ($generator->isIdModelVirtual($generator->getReplaceString($column->name), null, $tableSchema) !== false) {
        $relation = $generator->isIdModelVirtual($generator->getReplaceString($column->name), null, $tableSchema);
        ?>
//use <?php echo $generator->ns . '\\' . ucfirst($relation['name']) . ";\r\n";
    }
}
?>


<?= $generator->modelPhpDocs($tableSchema, $tableName, $relations) ?>
class <?= $className ?> extends ActiveRecordReport
{
    <?= $generator->statusConstants($tableSchema, $tableName) ?>

    <?= $generator->generateTimestampAttributes($tableSchema, $tableName) ?>
    <?= $generator->generateStatusAttributes($tableSchema, $tableName) ?>

    /**
     * @return array $primary_keys
     */
    public static function primaryKey() {
        return [<?php
            $pk = [];
            $firstColumn = null;

            foreach ($tableSchema->columns as $column) {
                if (is_null($firstColumn)) {
                    $firstColumn = $column->name;
                }

                if (strpos($column->name, "pk_") === false) {
                    if (($pos = mb_stripos($column->name, "tk_")) === false) {
                        continue;
                    }

                    if ($pos === 0) {
                        $pk[] = $column->name;
                    }

                    continue;
                }

                $pk[] = $column->name;
            }

            if (count($pk) > 0) {
                echo '\'' . implode('\',\'', $pk) . '\'';
            } else {
                echo '\'' . $firstColumn . '\'';
            }
        ?>];
    }

    /**
     * @return array $title_keys
     */
    public static function titleKey() {
        return [<?php
            $keys = [];
            foreach ($generator->getOtherKeys($tableSchema) as $key => $column) {
                if ($generator->isIdModelVirtual($generator->getReplaceString($column), null, $tableSchema) === false) {
                    $keys[] = "\r\n\t\t\t'$column',";
                } else {
                    $relation = $generator->isIdModelVirtual($generator->getReplaceString($column), null, $tableSchema);
                    $keys[] = "\r\n\t\t\t'$column' => [\r\n\t\t\t\t'attribute' => '$column', "
                     . "\r\n\t\t\t\t'class' => '\\".$relation['class']."', "
                     . "\r\n\t\t\t\t'relation' => '".$relation['name']."'\r\n\t\t\t],\r\n\t\t";
                }
            }

            echo implode("\r\n", $keys);
        ?>];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
    
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
            <?= "'$name' => " . $generator->generateString($label, [], false) . ",\n" ?>
<?php endforeach; ?>
        ];
    }

<?php if ($queryClassName) : ?>
<?php
    $queryClassFullName = ($generator->nsReport === $generator->queryNsReport) ? $queryClassName : '\\' . $generator->queryNsReport . '\\' . $queryClassName;
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
<?php endif; ?>

    <?php
        foreach ($tableSchema->columns as $column) {
            if ($generator->isIdModelVirtual($generator->getReplaceString($column->name), null, $tableSchema) !== false) {
                $relation = $generator->isIdModelVirtual($generator->getReplaceString($column->name), null, $tableSchema);
                ?>/**
     * @inheritdoc
     * @return \<?=$generator->ns . '\\' . ucfirst($relation['name']) . "\r\n";?>
     */
    public function get<?=ucfirst($relation['name'])?>() {
        return $this->hasOne(<?=ucfirst($relation['name'])?>::className(), ['id' => '<?=$column->name?>']);
    }
                <?php
            }
        }
    echo "\r\n";
    ?>}

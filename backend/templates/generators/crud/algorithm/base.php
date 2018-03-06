<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;
use yii\db\Schema;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->baseSearchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->baseSearchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use yii\helpers\ArrayHelper;
use backend\helpers\Html;
use backend\components\SearchInterface;
use backend\components\SearchTrait;

/**
 * <?= $searchModelClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
 */
class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?> implements SearchInterface

{
    use SearchTrait {
        modifyQuery as protected modifyQueryDefault;
        getSort as protected getSortDefault;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                <?= implode(",\n                ", $rules) ?>,
            ]
        );
    }
<?php
$tableSchema = $generator->getTableSchema();
?>
    /**
     * @return string
     */
    public function getGridTitle()
    {
        return <?= $generator->generateI18N(
            $tableSchema->comment,
            true
        )?>;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [

            ]
        );
    }

    /**
     * @return array
     */
    public function getListTitle()
    {
        return <?= $generator->generateI18N($tableSchema->comment, true) ?> . ': ' . $this-><?= $generator->getNameAttribute() ?>;
    }

    /**
     * @return array
     */
    public function getListColumns()
    {
        return [
            <?php
            $columns = [];
            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {
                    $columns[] = $name;
                }
            } else {
                foreach ($generator->getTableSchema()->columns as $column) {
                    $columns[] = $column;
                }
            }

            $relations = $generator->getRelationsNs($generator->getTableSchema());

            $tableName = $generator->getTableSchema()->fullName;
            $relation_links = $generator->getLinks($tableName);

            $generator->generateTimestampAttributes($generator->getTableSchema());
            $generator->generateStatusAttributes($generator->getTableSchema());
            foreach ($columns as $column) {
                $name = $tableSchema ? $column->name : $column;
                if ($generator->existRelation($column)) {
                    $relationName = $generator->getRelationByColumn($column);
                    $relationModel = $generator->getRelationModel($column);

                    $code = $generator->generateRelationCode($relationName, $relationModel, $column->name);
                    ?>[
                'attribute' => '<?= $name ?>',
                'value' => function ($form, $widget) {
                    <?=$code?>

                },
                'format' => 'raw',
            ],<?php
                    echo "\n            ";
                } elseif (in_array($name, $generator->getDatetimeAttributes())) { ?>'<?= $name ?>',<?php
                    echo "\n            ";
                } elseif (in_array($name, $generator->imageAttributes)) { ?>[
                    'attribute' => '<?= $name ?>',
                    'value' => function ($form, $widget) {
                        $model = $widget->model;
                        return Html::tag('img', '', ['src' => $model-><?= $name ?>Url]);
                    },
                    'format' => 'raw',
                    ],<?php
                    echo "\n            ";
                } elseif (in_array($name, $generator->getStatusAttributes())) {   ?>'<?= $name ?>',<?php
                    echo "\n            ";
                } elseif ($tableSchema && $column->phpType === 'double') {
                    $decimals = $column->scale && is_int($column->scale) ? $column->scale : 4;
                    ?>[
                'attribute' => '<?= $name ?>',
                'format' => ['decimal', <?= $decimals ?>],
            ],<?php
                    echo "\n            ";
                } else {
                    if (preg_match('~(.*)_amount$~', $name, $match) || $name == 'amount') {
                        ?>[
                'attribute' => '<?= $name ?>',
                'value' => function ($form, $widget) {
                    $model = $widget->model;
                    $value = $model-><?=$name?>;
                    return $value !== null ? CurrencyColumn::asCurrency($model, $value, $model->currency) : null;
                }
            ],<?php
                    } else {
                        ?>'<?= $name ?>',<?php
                    }

                    echo "\n            ";
                }
            }
            echo "";
            ?>

        ];
    }
}
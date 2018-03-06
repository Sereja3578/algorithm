<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;
use yii\db\Schema;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\settings\Generator */

$searchModelClass = StringHelper::basename($generator->searchModelClass);

$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

$modelClass = StringHelper::basename($generator->modelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}


echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use yii\helpers\ArrayHelper;
use backend\helpers\Html;
use backend\components\SearchInterface;
use backend\components\SearchTrait;
use backend\components\grid\CurrencyColumn;
use backend\widgets\GridView;

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
     * @return array
     */
    public function getGridToolbar()
    {
        return [
            $this->getGridReset(),
            $this->getGridShare()
        ];
    }

    /**
     * @var string[]
     */
    public $settings = [];

    /**
     * @return array
     */
    public function getDisableColumns()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                <?= implode(",\n                ", $rules) ?>,
                [['settings'], 'safe'],
            ]
        );
    }

    <?php
    $tableSchema = $generator->getTableSchema();
    ?>/**
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
     * @return []
     */
    public function getGridColumns()
    {
        $searchModel = $this;

        return [<?php
            $columns = [];
            $keys = [];
            if (($tableSchema = $generator->getTableSchema()) === false) {
                foreach ($generator->getColumnNames() as $name) {
                    $columns[] = $name;
                }
            } else {
                foreach ($tableSchema->foreignKeys as $i => $key) {
                    $keys[] = array_flip($key)['id'];
                }

                foreach ($tableSchema->columns as $i => $column) {
                    if (in_array($i, $keys)) {
                        $columns[] = $column;
                    }
                }
            }
            foreach ($columns as $index => $column) {
                echo $generator->generateColumn($tableSchema, $column, true);
            }
            ?>

            'settings' => [
                'attribute' => 'settings',
                'label' => $this->getAttributeLabel('settings'),
                'value' => function (<?= $modelClass ?> $model, $key, $index, $widget) use ($searchModel) {
                    $can = Yii::$app->getUser()->can($searchModel->getPermissionPrefix() . 'update', $model->getPrimaryKey(true));
                    return $this->getView()->render($can ? '_settings' : '_settings_view', [
                        'model' => $model,
                        'searchModel' => $searchModel
                    ]);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => $this->settingsListItems(),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'filterInputOptions' => [
                    'placeholder' => '',
                    'multiple' => true
                ],
                'format' => 'raw'
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function settingsAttributes()
    {
        return [<?php
            $keys = [];
            $columns = [];
            foreach ($tableSchema->foreignKeys as $i => $key) {
                $keys[] = array_flip($key)['id'];
            }

            foreach ($tableSchema->columns as $i => $column) {
                if (!in_array($i, $keys)) {
                    echo "\n            '{$i}',";
                }
            }
            ?>

        ];
    }

    /**
     * @return array
     */
    public function settingsListItems()
    {
        $settingsAttributes = $this->settingsAttributes();

        return array_filter(
            $this->attributeLabels(),
            function ($attribute) use ($settingsAttributes) {
                return in_array($attribute, $settingsAttributes);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'settings' => Yii::t('models', 'Настройки'),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = null)
    {
        if (!empty($params)) {
            $this->load($params);
        }

        $this->query = <?= $modelClass ?>::find();
        <?= implode("\n        ", $searchConditions) ?>
        <?php
            $__classModel = ltrim($generator->modelClass, '\\');
            /** @var \yii\db\ActiveRecord $__className */
            $__className = new $__classModel();
            $__relations = array();

            $keys = $__className->getTableSchema()->foreignKeys;
            $columns = $__className->getTableSchema()->columns;

            $joinWith = [];
            $innerJoinWith = [];

            foreach ($keys as $i => $key) {
                $relationTable = array_shift($key);
                $column = array_intersect_key($columns, $key);
                $columnOptions = array_shift($column);

                foreach ($__className::singularRelations() as $relation => $relationOptions) {
                    if (in_array($columnOptions->name, $relationOptions['link'])) {
                        if ($columnOptions->allowNull) {
                            $joinWith[] = $relation . ' ' . $relation;
                        } else {
                            $innerJoinWith[] = $relation . ' ' . $relation;
                        }
                        break;
                    }
                }
            }

            if (count($innerJoinWith) > 0) {
                echo "\n        \$this->query->innerJoinWith([\n            '" . join("',\n            '", $innerJoinWith) . "'\n        ]);";
            }
            if (count($joinWith) > 0) {
                echo "\n\n        \$this->query->joinWith([\n            '" . join("',\n            '", $joinWith) . "'\n        ]);\n";
            }
        ?>
        
        return $this->getDataProvider();
    }
}
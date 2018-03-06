<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator \backend\templates\generators\settings\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)) : ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else : ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\ActiveRecord;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 *
 * @method actionExportConfirm()
 * @method actionExport($partExport, $params = null)
 */
class <?= $controllerClass ?> extends <?= "Controller\n" ?>
{
<?php if (!empty($generator->searchModelClass)) : ?>
    protected $searchClass = '<?= ltrim($generator->searchModelClass, '\\') ?>';
<?php endif; ?><?php if (!empty($class::$icon)) : ?>
    /**
     * Icon class, glyphicon or something else
     * @var string
     */
    static $icon = '<?= $class::$icon ?>';

    /**
     * Gets icon class
     * @return string
     */
    public static function icon()
    {
        return explode('-', self::$icon)[0] . ' ' . self::$icon;
    }
<?php endif; ?>

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)) : ?>
        /** @var ActiveRecord $searchModel */
        $searchModel = $this->getSearchModel();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(),
        ]);
<?php else : ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return string
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);
        $request = Yii::$app->getRequest();

        if ($model->load($request->getBodyParams()) && $model->validate()) {
            if (!$model->save()) {
                throw $model->newException();
            }
        }

        $searchModel = new $this->searchClass;
        $settings = $request->getBodyParam($model->formName());

        if ($settings) {
            $searchModel->settings = array_keys($settings);
        }

        return $this->renderPartial('_settings', [
            'model' => $model,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "\$$pk";
    }
    $condition = implode(', ', $condition);
}
?>
        if (($model = <?=$modelClass?>::find()<?php
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
                echo "\n                ->innerJoinWith([\n                    '" . join("',\n                    '", $innerJoinWith) . "'\n                ])";
            }
            if (count($joinWith) > 0) {
                echo "\n                ->joinWith([\n                    '" . join("',\n                    '", $joinWith) . "'\n                ])";
            }
        ?>

                ->pk(<?= $condition ?>)
                ->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('errors', 'Запрашиваемая страница не найдена.'));
        }
    }
}
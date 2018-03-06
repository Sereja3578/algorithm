<?php

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\settings\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\widgets\ListView;
use yii\data\ArrayDataProvider;
use yii\bootstrap\ActiveForm;
use backend\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\db\Schema;

/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $searchModel <?= ltrim($generator->searchModelClass, '\\') ?> */

Pjax::begin([
    'id' => 'pjax-partial-' . <?= $generator->generateSettingsIdParams() ?>,
    'enablePushState' => false
]);

$form = ActiveForm::begin([
    'action' => Url::to(['update', <?= $generator->generateSettingsParams()?>]),
    'enableClientScript' => false,
    'options' => [
        'data-pjax' => true,
        'class' => 'pjax-partial'
    ]
]);

$hasFields = false;

echo ListView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $searchModel->settings ?: $searchModel->settingsAttributes()
    ]),
    'itemView' => function ($attribute) use ($model, $form, &$hasFields) {
        if ($model->isAttributeSafe($attribute)) {
            $hasFields = true;
            $column = $model->getTableSchema()->getColumn($attribute);
            if (in_array($column->type, [Schema::TYPE_BOOLEAN, Schema::TYPE_SMALLINT])
                && ($column->size == 1) && $column->unsigned
            ) {
                $field = $form->field($model, $attribute)->checkbox([
                    'template' => "{label}\n{beginWrapper}\n{input}\n<span class=\"hidden\">"
                        . $model->$attribute . "</span>\n{hint}\n{error}\n{endWrapper}"
                ]);
            } else {
                $field = $form->field($model, $attribute, [
                    'template' => "{label}\n{beginWrapper}\n{input}\n<span class=\"hidden\">"
                        . $model->$attribute . "</span>\n{hint}\n{error}\n{endWrapper}"
                ]);
            }
            return $field;
        } else {
            return '';
        }
    },
    'layout' => '{items}'
]);

if ($hasFields) {
    echo Html::submitInput(Yii::t('buttons', 'Сохранить'), ['class' => 'btn btn-primary']);
}

ActiveForm::end();

Pjax::end();
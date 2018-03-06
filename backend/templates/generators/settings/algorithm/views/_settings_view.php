<?php

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\settings\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\widgets\ListView;
use yii\data\ArrayDataProvider;

/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $searchModel <?= ltrim($generator->searchModelClass, '\\') ?> */

echo ListView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $searchModel->settings ?: $searchModel->settingsAttributes()
    ]),
    'itemView' => function ($attribute) use ($model) {
        if ($model->isAttributeSafe($attribute)) {
            $value = Yii::$app->getFormatter()->asText($model->$attribute);
            return <<<HTML
<div class="form-group">
    <label class="control-label">{$model->getAttributeLabel($attribute)}</label>
    <p>{$value}</p>
    <p class="help-block">{$model->getAttributeHint($attribute)}</p>
</div>
HTML;
        } else {
            return '';
        }
    },
    'layout' => '{items}'
]);
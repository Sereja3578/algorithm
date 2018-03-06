<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator backend\templates\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use backend\helpers\Html;
use yii\widgets\ActiveForm;
<?php
    $relations = $generator->getRelationsNs($generator->getTableSchema());
if (sizeof($relations) > 0) {
    foreach ($relations as $relation) {
?>
use common\models\<?=  ucfirst($relation)?>;
<?php
    }
}
?>

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>
<?= "<?php " ?>$form = ActiveForm::begin(<?= $generator->modelHasImages ? "['options' => ['enctype' => 'multipart/form-data']]" : '' ?>); ?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">


<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        if (in_array($attribute, $generator->getDatetimeAttributes())) {
            echo "    <?php \$model->{$attribute} = strtotime(\$model->{$attribute});?>\n\n";
        }
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
</div>
<div class="box-footer">
    <?= "<?= " ?>Html::saveButton($model) ?>


    <?php if ($generator->modelHasImages) : ?>
        <?= '<?php' ?> ob_start(); ?>
        <?php foreach (array_intersect($generator->imageAttributes, $generator->getColumnNames()) as $attribute) : ?>
            $("#<?= '<?=' ?> Html::getInputId($model, '<?= $attribute ?>') ?>").on("fileclear", function() {
            $(this).closest("form").find("input[type=hidden][name='<?= '<?=' ?> Html::getInputName($model, '<?= $attribute ?>') ?>']").val("");
            });
        <?php endforeach; ?>
        <?= '<?php' ?>

        $js = ob_get_clean();
        $this->registerJs($js);
        ?>
    <?php endif; ?>
</div>
<?= "<?php " ?>ActiveForm::end(); ?>

<?php

use yii\helpers\Html;
use dmstr\helpers\AdminLteHelper;

/* @var $this \yii\web\View */
/* @var $content string */


backend\assets\AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
$admin = Yii::$app->getUser()->identity;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition <?= AdminLteHelper::skinClass() ?> sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        [
            'directoryAsset' => $directoryAsset,
            'admin' => $admin,
        ]
    ) ?>

    <?= $this->render(
        'left.php',
        [
            'directoryAsset' => $directoryAsset,
            'admin' => $admin,
        ]
    )
    ?>

    <?= $this->render(
        'content.php',
        [
            'content' => $content,
            'directoryAsset' => $directoryAsset,
            'admin' => $admin,
        ]
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

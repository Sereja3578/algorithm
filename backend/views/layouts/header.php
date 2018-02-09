<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= $admin ? Html::encode($admin->username) : ''; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
                            <div>
                                <p class="pull-left">
                                    <?= Yii::t('header', 'Имя: '), $admin ? Html::encode($admin->username) : ''; ?>
                                </p>
                                <p class="pull-right">
                                    <?= Yii::t('header', 'Роль: '), $admin ? Html::encode($admin->username) : ''; ?>
                                </p>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer clear-both">
                            <div class="pull-right">
                                <?= Html::a(
                                    Yii::t('main', 'Выход'),
                                    ['auth/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

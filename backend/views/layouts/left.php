<?php
use yii\helpers\Html;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= $admin ? Html::encode($admin->username) : ''; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i><?= Yii::t('left', 'Онлайн'); ?></a>
            </div>
        </div>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => Yii::t('left-menu', 'Алгоритмы'), 'icon' => 'file-code-o', 'url' => ['/algorithm']]
                ],
            ]
        ) ?>

    </section>

</aside>

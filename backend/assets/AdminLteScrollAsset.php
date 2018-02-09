<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class AdminLteScrollAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    
    public $js = [
        'slimScroll/jquery.slimscroll.js',
    ];

    public $depends = [
        'dmstr\web\AdminLteAsset',
    ];
}

<?php
/**
 * Created by PhpStorm.
 * User: veselov
 * Date: 01.08.2016
 * Time: 17:38
 */

namespace backend\components;

use Yii;

class View extends \yii\web\View
{
    protected function renderBodyEndHtml($ajaxMode)
    {
        $htmlCode = parent::renderBodyEndHtml($ajaxMode);

        if (strpos(Yii::$app->request->userAgent, "Safari") !== false) {
            $htmlCode = str_replace("window.addEventListener('popstate',function(){window.location.reload();});", "", $htmlCode);
        }

        return $htmlCode;
    }
}
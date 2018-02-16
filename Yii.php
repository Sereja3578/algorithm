<?php

require(__DIR__ . '/vendor/yiisoft/yii2/BaseYii.php');

/**
 * @property yii\console\Application|yii\web\Application|FakeApplication $app
 */
class Yii extends \yii\BaseYii
{
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require(__DIR__ . '/vendor/yiisoft/yii2/classes.php');
Yii::$container = new yii\di\Container();

/**
 * @property Redis $pureRedisQuotes
 */
abstract class FakeApplication extends yii\base\Application
{
}

/**
 * @property common\models\Admin|common\models\User|null $identity
 *
 * @method common\models\Admin|common\models\User|null getIdentity() getIdentity(boolean $autoRenew)
 */
abstract class FakeWebUser extends yii\web\User
{
}

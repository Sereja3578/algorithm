<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 07.03.2018
 * Time: 21:11
 */

namespace backend\components\validators;

use common\models\AlgorithmParams;
use Yii;
use yii\validators\Validator;

class GameChanceValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = Yii::t('validators', 'Указан шанс для не выбраной игры');
    }

    public function validateAttribute($model, $attribute)
    {
        /**
         * @var AlgorithmParams $model
         */
        foreach ($model->gamesChances as $gameId => $chance) {
            if (!in_array($chance, explode(', ', $model->games))) {
                $model->addError('gamesChances', \Yii::t('validators', 'Указан шанс для не выбраной игры'));
            }
        }
    }

//    public function clientValidateAttribute($model, $attribute, $view)
//    {
//        /**
//         * @var AlgorithmParams $model
//         */
//        $games = json_encode($model->games);
//        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//        return <<<JS
//$.each(value, function(gameId, chance) {
//    if ($.inArray(gameId, $games) === -1) {
//        messages.push($message);
//    }
//})
//JS;
//    }
}
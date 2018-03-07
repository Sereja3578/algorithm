<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 07.03.2018
 * Time: 17:05
 */

use common\models\Game;

/* @var $this yii\web\View */
/* @var $model common\models\AlgorithmParams */
/* @var $form yii\widgets\ActiveForm */

$games = Game::findAll();

foreach ($games as $game) {
    echo $form->field($model, 'gamesChances[' . $game->id . ']')->hiddenInput()->widget(\kartik\widgets\TouchSpin::className(), [
        'options' => [
            'id' => 'game_' . $game->id,
            'class' => 'game-chance-field',
        ],
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0.1,
            'step' => 0.1,
            'decimals' => 1,
        ]
    ])->label(Game::getMessage($game->id));
}
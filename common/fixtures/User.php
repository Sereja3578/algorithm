<?php

namespace common\fixtures;

use yii\boost\test\ActiveFixture;

/**
 * User fixture
 * @see \common\models\User
 */
class User extends ActiveFixture
{

    public $modelClass = 'common\models\User';

    /*[
        'id' => '',
        'username' => '',
        'auth_key' => '',
        'password_hash' => '',
        'password_reset_token' => '',
        'password_reset_token_expires_at' => '',
        'email' => '',
        'status' => '',
        'created_at' => '',
        'updated_at' => ''
    ]*/

    public $dataFile = '@common/tests/data/user.php';
}

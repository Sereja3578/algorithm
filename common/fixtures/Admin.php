<?php

namespace common\fixtures;

use yii\boost\test\ActiveFixture;

/**
 * Admin fixture
 * @see \common\models\Admin
 */
class Admin extends ActiveFixture
{

    public $modelClass = 'common\models\Admin';

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

    public $dataFile = '@common/tests/data/admin.php';
}

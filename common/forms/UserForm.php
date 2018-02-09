<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 07.02.2018
 * Time: 13:12
 */

namespace common\forms;

use common\models\Admin;
use common\models\User;
use yii\base\Model;
use Yii;

class UserForm extends BaseForm
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var User|Admin|null
     */
    private $_user;

    /**
     * Finds user by [[username]]
     *
     * @return User|Admin|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = ($this->scenario == 'user_action') ? User::findByUsername($this->username) : Admin::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * @return Admin|User
     */
    protected function getNewUser()
    {
        return ($this->scenario == 'user_action') ? new User() : new Admin();
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $attributes = $this->getAttributesNames();
        $scenarios = parent::scenarios();
        $scenarios['admin_action'] = $attributes;
        $scenarios['user_action'] = $attributes;
        return $scenarios;
    }
}
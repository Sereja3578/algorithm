<?php
namespace common\forms;

use Yii;

/**
 * Login form
 */
class LoginForm extends UserForm
{
    /**
     * @var string
     */
    public $password;
    /**
     * @var bool
     */
    public $rememberMe = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();

            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'username' => Yii::t('models', 'Логин'),
            'password' => Yii::t('models', 'Пароль'),
            'rememberMe' => Yii::t('models', 'Запомнить меня'),
        ]);
    }

    public function getFormTitle()
    {
        return Yii::t('models', 'Вход');
    }
}

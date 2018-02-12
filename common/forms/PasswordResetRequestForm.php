<?php
namespace common\forms;

use common\models\Admin;
use Yii;
use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends UserForm
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'emailValidation',
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    public function emailValidation($email)
    {
        /* @var User|Admin $userModel */
        $userModel = $this->getNewUser();
        $user = $userModel::findOne(['email' => $email, 'status' => $userModel::STATUS_ACTIVE]);

        return $user ? true : false;
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {

        /* @var User|Admin $userModel */
        $userModel = $this->getNewUser();

        $user = $userModel::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        $user->generatePasswordResetToken();

        if (!$user->save()) {
            return false;
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }

    /**
     * @return string
     */
    public function getFormTitle()
    {
        return Yii::t('models', 'Запросить восстановление пароля');
    }
}

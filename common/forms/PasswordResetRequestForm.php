<?php
namespace common\forms;

use common\models\Admin;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends BaseForm
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
        $user = ($this->scenario == 'user_action') ?
            User::findOne(['email' => $this->email, 'status' => User::STATUS_ACTIVE]) :
            Admin::findOne(['email' => $this->email, 'status' => Admin::STATUS_ACTIVE]);
        return $user ? true : false;
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        if($this->scenario == 'user_action') {
            /* @var $user User */
            $user = User::findOne([
                'status' => User::STATUS_ACTIVE,
                'email' => $this->email,
            ]);
        } else {
            /* @var $user Admin */
            $user = Admin::findOne([
                'status' => Admin::STATUS_ACTIVE,
                'email' => $this->email,
            ]);
        }

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
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

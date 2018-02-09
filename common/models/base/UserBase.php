<?php

namespace common\models\base;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $password_reset_token_expires_at
 * @property string $email
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class UserBase extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [[
                'password_reset_token_expires_at',
                'created_at',
                'updated_at'
            ], 'filter', 'filter' => function ($value) {
                return is_int($value) ? date('Y-m-d H:i:s', $value) : $value;
            }],
            [[
                'password_reset_token_expires_at',
                'created_at',
                'updated_at'
            ], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [[
                'created_at',
                'updated_at'
            ], 'default', 'value' => new Expression('CURRENT_TIMESTAMP')],
            [['status'], 'default', 'value' => '10'],
            [[
                'password_reset_token',
                'password_reset_token_expires_at'
            ], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'username' => Yii::t('models', 'Username'),
            'auth_key' => Yii::t('models', 'Auth Key'),
            'password_hash' => Yii::t('models', 'Password Hash'),
            'password_reset_token' => Yii::t('models', 'Password Reset Token'),
            'password_reset_token_expires_at' => Yii::t('models', 'Время окончания действия токена восстановления пароля'),
            'email' => Yii::t('models', 'Email'),
            'status' => Yii::t('models', 'Status'),
            'created_at' => Yii::t('models', 'Созданов в'),
            'updated_at' => Yii::t('models', 'Обновлено в'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function datetimeAttributes()
    {
        return [
            'password_reset_token_expires_at',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'User');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return ['username'];
    }

    /**
     * @inheritdoc
     */
    // public function getTitleText()
    // {
    //     return $this->username;
    // }

    /**
     * @param string $username
     * @return integer
     */
    public static function pkByUsername($username)
    {
        return static::find()->select(['id'])->username($username)->scalar();
    }

    /**
     * @param string $username
     * @return integer
     */
    public static function idByUsername($username)
    {
        return static::find()->select(['id'])->username($username)->scalar();
    }

    /**
     * @param string $email
     * @return integer
     */
    public static function pkByEmail($email)
    {
        return static::find()->select(['id'])->email($email)->scalar();
    }

    /**
     * @param string $email
     * @return integer
     */
    public static function idByEmail($email)
    {
        return static::find()->select(['id'])->email($email)->scalar();
    }

    /**
     * @param string $passwordResetToken
     * @return integer
     */
    public static function pkByPasswordResetToken($passwordResetToken)
    {
        return static::find()->select(['id'])->passwordResetToken($passwordResetToken)->scalar();
    }

    /**
     * @param string $passwordResetToken
     * @return integer
     */
    public static function idByPasswordResetToken($passwordResetToken)
    {
        return static::find()->select(['id'])->passwordResetToken($passwordResetToken)->scalar();
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function usernameByPk($id)
    {
        return static::find()->select(['username'])->pk($id)->scalar();
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function usernameById($id)
    {
        return static::find()->select(['username'])->id($id)->scalar();
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function emailByPk($id)
    {
        return static::find()->select(['email'])->pk($id)->scalar();
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function emailById($id)
    {
        return static::find()->select(['email'])->id($id)->scalar();
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function passwordResetTokenByPk($id)
    {
        return static::find()->select(['password_reset_token'])->pk($id)->scalar();
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function passwordResetTokenById($id)
    {
        return static::find()->select(['password_reset_token'])->id($id)->scalar();
    }

    /**
     * @param string $email
     * @return string
     */
    public static function usernameByEmail($email)
    {
        return static::find()->select(['username'])->pk($email)->scalar();
    }

    /**
     * @param string $passwordResetToken
     * @return string
     */
    public static function usernameByPasswordResetToken($passwordResetToken)
    {
        return static::find()->select(['username'])->pk($passwordResetToken)->scalar();
    }

    /**
     * @param string $username
     * @return string
     */
    public static function emailByUsername($username)
    {
        return static::find()->select(['email'])->pk($username)->scalar();
    }

    /**
     * @param string $passwordResetToken
     * @return string
     */
    public static function emailByPasswordResetToken($passwordResetToken)
    {
        return static::find()->select(['email'])->pk($passwordResetToken)->scalar();
    }

    /**
     * @param string $username
     * @return string
     */
    public static function passwordResetTokenByUsername($username)
    {
        return static::find()->select(['password_reset_token'])->pk($username)->scalar();
    }

    /**
     * @param string $email
     * @return string
     */
    public static function passwordResetTokenByEmail($email)
    {
        return static::find()->select(['password_reset_token'])->pk($email)->scalar();
    }

    /**
     * @param bool $passwordResetTokenNotExpired
     * @return bool
     */
    public function getIsPasswordResetTokenNotExpired($passwordResetTokenNotExpired = true)
    {
        if ($passwordResetTokenNotExpired) {
            return is_null($this->password_reset_token_expires_at) || strtotime($this->password_reset_token_expires_at) > time();
        } else {
            return !is_null($this->password_reset_token_expires_at) && strtotime($this->password_reset_token_expires_at) <= time();
        }
    }
}

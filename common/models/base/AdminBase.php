<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class AdminBase extends \common\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'status',
                'created_at',
                'updated_at'
            ], 'integer'],
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['status'], 'default', 'value' => '10'],
            [['password_reset_token'], 'default', 'value' => null],
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
            'email' => Yii::t('models', 'Email'),
            'status' => Yii::t('models', 'Status'),
            'created_at' => Yii::t('models', 'Created At'),
            'updated_at' => Yii::t('models', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AdminQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AdminQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Admin');
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
}

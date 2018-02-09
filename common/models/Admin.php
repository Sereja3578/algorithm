<?php

namespace common\models;

use Yii;
use common\models\base\AdminBase;
use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * User
 * @see \common\models\query\UserQuery
 */
class Admin extends AdminBase implements IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_DELETED = 3;

    /**
     * @var string
     */
    public $newPassword;

    /**
     * @var string|array
     */
    public $role;

    /**
     * @return array
     */
    public static function roleListItems()
    {
        $roles = [];
        $authManager = Yii::$app->getAuthManager();
        if ($authManager) {
            foreach ($authManager->getRoles() as $role) {
                $roles[$role->name] = $role->description;
            }
        }
        return $roles;
    }

    /**
     * @param $id
     */
    public function blockUser($id)
    {
        self::updateAll(['status' => self::STATUS_BLOCKED], ['id' => $id]);
    }

    /**
     * @param $id
     */
    public function deleteUser($id)
    {
        self::updateAll(['status' => self::STATUS_DELETED], ['id' => $id]);
    }

    /**
     * @param $id
     */
    public function unBlockUser($id)
    {
        self::updateAll(['status' => self::STATUS_ACTIVE], ['id' => $id]);
    }

    /**
     * @param $id
     */
    public function restoreUser($id)
    {
        self::updateAll(['status' => self::STATUS_ACTIVE], ['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param string $token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        return static::find()->andWhere([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ])->passwordResetTokenNotExpired()->one();
    }

    /**
     * @param string $token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        return static::find()->andWhere([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ])->passwordResetTokenNotExpired()->exists();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() == $authKey;
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function generatePasswordResetToken()
    {
        $expires = Yii::$app->params['passwordResetTokenExpires'];
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString();
        $expression = new Expression(sprintf('DATE_ADD(NOW(), INTERVAL %d SECOND)', $expires));
        $this->password_reset_token_expires_at = $expression;
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
        $this->password_reset_token_expires_at = null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'newPassword' => Yii::t('models', 'Новый пароль'),
            'role' => Yii::t('models', 'Роль'),
            'middle_name' => Yii::t('models', 'Отчество'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['newPassword'], 'string', 'length' => [6, 50]],
            [['role'], 'in', 'range' => array_keys(static::roleListItems()), 'allowArray' => true],
            [['role'], 'required'],
            [['newPassword'], $this->isNewRecord?'required':'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $authManager = Yii::$app->getAuthManager();
        if ($authManager) {
            $assignments = $authManager->getAssignments($this->id);
            if (count($assignments) == 1) {
                $this->role = array_values($assignments)[0]->roleName;
            } elseif (count($assignments) > 1) {
                $this->role = [];
                foreach ($assignments as $assignment) {
                    $this->role[] = $assignment->roleName;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->auth_key) {
                $this->generateAuthKey();
            }
            if ($this->newPassword) {
                $this->setPassword($this->newPassword);
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $authManager = Yii::$app->getAuthManager();
        if ($authManager) {
            if (!$insert) {
                $authManager->revokeAll($this->id);
            }
            foreach ((array)$this->role as $roleName) {
                $role = $authManager->getRole($roleName);
                if ($role) {
                    $authManager->assign($role, $this->id);
                }
            }
        }
    }
}

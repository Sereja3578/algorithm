<?php

namespace common\models\query\base;

/**
 * This is the ActiveQuery class for [[\common\models\Admin]].
 *
 * @see \common\models\Admin
 */
class AdminQueryBase extends \common\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\Admin[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Admin|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer|integer[] $id
     * @return $this
     */
    public function pk($id)
    {
        return $this->andWhere([$this->a('id') => $id]);
    }

    /**
     * @param integer|integer[] $id
     * @return $this
     */
    public function id($id)
    {
        return $this->andWhere([$this->a('id') => $id]);
    }

    /**
     * @param string|string[] $username
     * @return $this
     */
    public function username($username)
    {
        return $this->andWhere([$this->a('username') => $username]);
    }

    /**
     * @param string|string[] $email
     * @return $this
     */
    public function email($email)
    {
        return $this->andWhere([$this->a('email') => $email]);
    }

    /**
     * @param string|string[] $passwordResetToken
     * @return $this
     */
    public function passwordResetToken($passwordResetToken)
    {
        return $this->andWhere([$this->a('password_reset_token') => $passwordResetToken]);
    }
}

<?php

namespace common\models\query;

use common\models\query\base\AdminQueryBase;

/**
 * Admin query
 * @see \common\models\Admin
 */
class AdminQuery extends AdminQueryBase
{
    /**
     * @param bool $passwordResetTokenNotExpired
     * @return $this
     */
    public function passwordResetTokenNotExpired($passwordResetTokenNotExpired = true)
    {
        $columnName = $this->a('password_reset_token_expires_at');
        if ($passwordResetTokenNotExpired) {
            return $this->andWhere($columnName . ' IS NULL OR ' . $columnName . ' > NOW()');
        } else {
            return $this->andWhere($columnName . ' IS NOT NULL AND ' . $columnName . ' <= NOW()');
        }
    }
}

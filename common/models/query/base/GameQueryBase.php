<?php

namespace common\models\query\base;

/**
 * This is the ActiveQuery class for [[\common\models\Game]].
 *
 * @see \common\models\Game
 */
class GameQueryBase extends \common\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\Game[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Game|array|null
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
}

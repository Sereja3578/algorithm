<?php

namespace common\models\query\base;

/**
 * This is the ActiveQuery class for [[\common\models\Strategy]].
 *
 * @see \common\models\Strategy
 */
class StrategyQueryBase extends \common\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\Strategy[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Strategy|array|null
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
     * @param integer|integer[] $algorithmParamsId
     * @return $this
     */
    public function algorithmParamsId($algorithmParamsId)
    {
        return $this->andWhere([$this->a('algorithm_params_id') => $algorithmParamsId]);
    }

    /**
     * @param integer|integer[] $gameId
     * @return $this
     */
    public function gameId($gameId)
    {
        return $this->andWhere([$this->a('game_id') => $gameId]);
    }

    /**
     * @param int|bool $result
     * @return $this
     */
    public function result($result = true)
    {
        return $this->andWhere([$this->a('result') => $result ? 1 : 0]);
    }

    /**
     * @param int|bool $bestStrategy
     * @return $this
     */
    public function bestStrategy($bestStrategy = true)
    {
        return $this->andWhere([$this->a('best_strategy') => $bestStrategy ? 1 : 0]);
    }
}

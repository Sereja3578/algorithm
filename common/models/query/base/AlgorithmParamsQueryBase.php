<?php

namespace common\models\query\base;

/**
 * This is the ActiveQuery class for [[\common\models\AlgorithmParams]].
 *
 * @see \common\models\AlgorithmParams
 */
class AlgorithmParamsQueryBase extends \common\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\AlgorithmParams[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\AlgorithmParams|array|null
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
     * @param integer|integer[] $assetId
     * @return $this
     */
    public function assetId($assetId)
    {
        return $this->andWhere([$this->a('asset_id') => $assetId]);
    }
}

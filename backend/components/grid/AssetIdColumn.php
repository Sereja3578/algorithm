<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 07.03.2018
 * Time: 10:49
 */

namespace backend\components\grid;


class AssetIdColumn extends RelationColumn
{
    /**
     * @var string
     */
    public $attribute = 'asset_id';

    /**
     * @var bool
     */
    public $allowNotSet = false;
}
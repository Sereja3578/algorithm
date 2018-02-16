<?php
/**
 * Created by PhpStorm.
 * User: ilichev
 * Date: 19.02.2018
 * Time: 11:21
 */

namespace backend\components\interfaces;

use yii\boost\data\SearchInterface;

interface SearchModelInterface extends SearchInterface
{
    public function getGridTitle();
    public function getGridColumns();
}
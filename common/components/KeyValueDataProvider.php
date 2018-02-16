<?php

namespace common\components;

use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;
use yii\db\QueryInterface;

class KeyValueDataProvider extends BaseDataProvider
{
    /**
     * @var QueryInterface
     */
    public $query;

    /**
     * @var \Closure
     */
    public $decorator;

    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }
        $decorator = $this->decorator;
        if ($decorator instanceof \Closure) {
            return $decorator($query->all());
        } else {
            return $query->all();
        }
    }

    protected function prepareKeys($models)
    {
        $keys = [];
        if (($pagination = $this->getPagination()) !== false) {
            for ($i = 0; $i < $this->getTotalCount(); $i++) {
                $keys[] = $i;
            }
        }
        return $keys;
    }

    protected function prepareTotalCount()
    {
        $query = clone $this->query;
        return $query->count();
    }
}
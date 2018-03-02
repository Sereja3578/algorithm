<?php

namespace common\components;

use yii\base\Component;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQueryTrait;
use yii\db\ActiveRelationTrait;
use yii\db\Exception;
use yii\db\QueryTrait;

class KeyValueQuery extends Component implements ActiveQueryInterface
{
    use QueryTrait;
    use ActiveQueryTrait;
    use ActiveRelationTrait;

    /**
     * @var KeyValue
     */
    public $modelClass;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    private $command;

    public $where = [];

    public function __construct($modelClass, $config = [])
    {
        $this->modelClass = $modelClass;
        parent::__construct($config);
    }

    public function all($db = null)
    {
        return $this->setCommand('lrange')->execute();
    }

    public function one($db = null)
    {
        return $this->setCommand('lrange')->limit(1)->execute();
    }

    public function keys($db = null)
    {
        return $this->setCommand('keys')->execute();
    }

    public function count($q = '*', $db = null)
    {
        $count = 0;
        if ($q == '*') {
            foreach ($this->where as $key) {
                $count += $this->getCount($key);
            }
        } elseif (is_string($q)) {
            $count += $this->getCount($q);
        }
        return $count;
    }

    protected function getCount($key)
    {
        $count = $offset = 0;
        $limit = $step = 50;
        while ($rows = $this->getDb()->lRange($key, $offset, $limit - 1)) {
            $count += count($rows);
            $offset += $step;
            $limit += $step;
        }
        return $count;
    }

    public function exists($db = null)
    {
        throw new Exception('Method not implemented.');
    }

    public function offset($offset)
    {
        $this->offset = (int)$offset;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = (int)$limit;
        return $this;
    }

    /**
     * @param array|string $cond
     * @return $this
     * @throws Exception
     */
    public function where($cond)
    {
        $this->where = [];
        return $this->andWhere($cond);
    }

    /**
     * @param array|string $cond
     * @return $this
     */
    public function andWhere($cond)
    {
        $this->buildCondition($cond);
        return $this;
    }

    protected function buildCondition($cond)
    {
        $modelClass = $this->modelClass;
        if (is_array($cond)) {
            $this->where[] = implode(':', array_merge([$modelClass::primaryKey()], $cond));
        } elseif (is_string($cond) || is_numeric($cond)) {
            if (strpos($cond, $modelClass::primaryKey()) === false) {
                $this->where[] = ($modelClass::primaryKey()) . ':' . $cond;
            } else {
                $this->where[] = $cond;
            }
        } else {
            throw new Exception('Invalid condition.');
        }
    }

    public function setCommand($command)
    {
        if (!is_string($command)) {
            return false;
        }
        $this->command = strtolower($command);
        return $this;
    }

    protected function execute()
    {
        $data = [];
        $originalLimit = $this->limit;
        foreach ($this->where as $key) {
            if ($this->command == 'lrange') {
                $data = array_merge($data, $this->getRange($key));
                if (count($data) == $originalLimit) {
                    break;
                } elseif (count($data) > 0 && count($data) < $this->limit) {
                    $this->limit($this->limit - count($data));
                    $this->offset(0);
                } else {
                    $this->offset($this->offset - $this->count($key));
                }
            }
            if ($this->command == 'keys') {
                $data = array_merge($data, $this->getKeys($key));
                break;
            }
        }
        return $data;
    }

    protected function getKeys($key)
    {
        return $this->getDb()->getKeys($key) ?: [];
    }

    protected function getRange($key)
    {
        $end = $this->offset + $this->limit - 1;
        if ($this->limit <= 0) {
            $end = -1;
        }
        return $this->getDb()->lRange($key, (int)$this->offset, -1) ?: [];
    }

    protected function getDb()
    {
        $modelClass = $this->modelClass;
        return $modelClass::getDb();
    }
}

<?php

namespace common\components;

use yii\boost\base\InvalidModelException;
use yii\boost\db\ActiveQuery;
use yii\boost\db\ActiveRecord as BoostActiveRecord;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Application;

/**
 * Class ActiveRecord
 *
 * @method search()
 *
 * @package common\components
 */
class ActiveRecord extends BoostActiveRecord
{
    use MessageTrait;

    public $errorCode = null;

    /**
     * @return string
     */
    public function getShortName()
    {
        $searchClass = new \ReflectionClass($this);
        return $searchClass->getShortName();
    }

    /**
     * @inheritdoc
     */
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        if (Yii::$app->id == 'app-backend') {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $update = parent::updateAll($attributes, $condition, $params);
            if ($update) {
                $transaction->commit();
                return $update;
            } else {
                $transaction->rollBack();
                return $update;
            }
        }
        return parent::updateAll($attributes, $condition, $params);
    }

    /**
     * @param string $attribute
     * @return string[]
     */
    public function getSimpleErrors($attribute = null)
    {
        if ($attribute) {
            return implode(' ', $this->getErrors($attribute));
        } else {
            return array_map(function ($errors) {
                return implode(' ', $errors);
            }, $this->getErrors());
        }
    }

    /**
     * @param string $name
     */
    public function clearAttribute($name)
    {
        if ($this->isAttributeChanged($name)) {
            $this->setAttribute($name, $this->getOldAttribute($name));
        }
    }

    /**
     * @param string $permissionName
     * @param array $params
     * @param bool $allowCaching
     * @return bool
     */
    public function webUserCan($permissionName, $params = [], $allowCaching = true)
    {
        if (!$this->getIsNewRecord()) {
            $params = array_merge($params, $this->getPrimaryKey(true));
        }
        $params['model'] = $this;
        return Yii::$app instanceof Application && Yii::$app->getUser()->can($permissionName, $params, $allowCaching);
    }

    /**
     * Возвращает имя из phpDoc
     * @return string
     */
    public function getDocName()
    {
        return static::getMessage($this->id);
    }

    /**
     * Save model hardly
     * @param bool $runValidation
     * @param null $attributeNames
     * @throws InvalidModelException
     */
    public function hardSave($runValidation = true, $attributeNames = null) {
        if (!$this->save($runValidation, $attributeNames)) {
            throw $this->newException();
        }
    }


    /**
     * @return \common\components\ActiveQuery|ActiveQuery
     */
    public function getQuery()
    {
        return static::find();
    }

    public function validateJson($attribute, $params) {
        $settings = json_decode($this->$attribute, true);
        if($this->$attribute && !is_array($settings)) {
            $this->addError($attribute, Yii::t('api-errors', 'Некорректные параметры'));
        }
    }

    /**
     * Example
     *
     * ```php
     * User::batchUpdate([
     *      'name' => ['Alice', 'Bob'],
     *      'age' => '18'
     * ], [
     *      'id' => [1, 2, 3],
     *      'enabled' => '1'
     * ]);
     * ```
     *
     * @param array $columns
     * @param array $condition
     * @return string
     */
    public static function batchUpdate(array $columns, $condition)
    {
        $table = static::tableName();
        $command = static::getDb()->createCommand();

        if (($tableSchema = $command->db->getTableSchema($table)) !== null) {
            $columnSchemas = $tableSchema->columns;
        } else {
            $columnSchemas = [];
        }

        $lines = [];
        foreach ($columns as $name => $value) {
            if ($value instanceof Expression) {
                $lines[] = $command->db->quoteColumnName($name) . '=' . $value->expression;
            } elseif (is_array($value)) {
                $line = $command->db->quoteColumnName($name) . " = (CASE ";
                foreach ($value as $valueIndex => $val) {
                    $line .= " WHEN ";
                    $when = [];
                    foreach ($condition as $whenKey => $conditions) {
                        $param = (is_array($conditions) && isset($conditions[$valueIndex])) ? $conditions[$valueIndex] : $conditions;
                        $whenValue = !is_array($param) && isset($columnSchemas[$whenKey]) ? $columnSchemas[$whenKey]->dbTypecast($param) : $param;
                        $when[] = $command->db->quoteColumnName($whenKey) . " = " . $command->db->quoteValue($whenValue);
                    }
                    $conditionValue = !is_array($val) && isset($columnSchemas[$name]) ? $columnSchemas[$name]->dbTypecast($val) : $val;
                    $line .= join(' AND ', $when) . " THEN " . $command->db->quoteValue($conditionValue);
                }
                $line .= " END )";
                $lines[] = $line;
            } else {
                $setValue = !is_array($value) && isset($columnSchemas[$name]) ? $columnSchemas[$name]->dbTypecast($value) : $value;
                $lines[] = $command->db->quoteColumnName($name) . '=' . $setValue;
            }
        }

        $sql = 'UPDATE ' . $command->db->quoteTableName($table) . ' SET ' . implode(', ', $lines);

        $parts = [];
        foreach ($condition as $whereKey => $whereValue) {
            if (ArrayHelper::isTraversable($whereValue) || $whereValue instanceof Query) {
                $parts[] = $command->db->quoteColumnName($whereKey) . " IN ('" . join("', '", $whereValue) . "')";
            } else {
                $parts[] = $command->db->quoteColumnName($whereKey) . " = '" . $whereValue . "'";
            }
        }

        $where = join(' AND ', $parts);
        $rawSql = $where === '' ? $sql : $sql . ' WHERE ' . $where;
        $command->setSql($rawSql);
        return $command->execute();
    }

}

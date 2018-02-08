<?php
namespace common\components;

use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\boost\db\ActiveQuery as BaseActiveQuery;

class ActiveQuery extends BaseActiveQuery
{
    /**
     * Глобальные фильтры по языку локализации
     * @return $this
     */
    public function initLanguageFilters()
    {
        return $this;
    }

    /**
     * @param bool $indexByName
     * @return $this
     * @throws NotSupportedException
     */
    public function listItems($indexByName = false)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $primaryKey = $modelClass::primaryKey();
        if (count($primaryKey) != 1) {
            throw new NotSupportedException('Primary key must be a single column.');
        }
        $k = 'id_for_index';
        $titleKey = $modelClass::titleKey();
        $separator = $modelClass::getDb()->quoteValue($modelClass::TITLE_SEPARATOR);
        if (is_array($titleKey)) {
            $this->orderBy(array_fill_keys($titleKey, SORT_ASC));
            if (count($titleKey) > 1) {
                $this->select([
                    new Expression('CONCAT(IFNULL(' . implode(',\'\'), IFNULL(' . $separator . ',\'\'), ', $this->a($titleKey)) . ') name'),
                    $k => $this->a($primaryKey[0])
                ]);
            } else {
                if (is_array($titleKey)) {
                    $titleKey = $titleKey[0];
                }

                $this->select([
                    $this->a($titleKey),
                    $k => $this->a($primaryKey[0])
                ]);
            }
        } elseif ($titleKey instanceof Expression) {
            $this->select([
                str_replace("{alias}", $this->alias, $titleKey),
                $k => $this->a($primaryKey[0])
            ]);
        } else {
            $this->select([
                $this->a($titleKey),
                $k => $this->a($primaryKey[0])
            ]);
        }

        if ($indexByName) {
            $key = $this->a($titleKey);
            $this->select(["$key id", "$key name"])->groupBy(['name'])->indexBy('id');
        } else {
            $this->orderBy($this->a($primaryKey[0]))->indexBy($k);
        }

        return $this;
    }
}

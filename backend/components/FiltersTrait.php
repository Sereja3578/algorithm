<?php

namespace backend\components;

use backend\components\grid\DateRangeColumn;
use backend\components\grid\DatetimeRangeColumn;

/**
 * Class FiltersTrait
 *
 * @method dateAttributes()
 * @method datetimeAttributes()
 * @method static getAttributes()
 *
 * @package backend\components
 */
trait FiltersTrait
{

    public function initDateFilters()
    {
        $attributes = array_intersect_key(static::getAttributes(), array_flip(static::dateAttributes()));
        $changes = DateRangeColumn::modifyQuery($this->query, $attributes);

        static::updateAttributes($changes);
    }

    public function initDatetimeFilters($customAttributes = [])
    {
        $attributes = array_merge(
            array_intersect_key(
                array_merge(static::getAttributes(),array_flip(array_keys($customAttributes))),
                array_flip(static::datetimeAttributes())
            ),
            $customAttributes
        );

        $changes = DatetimeRangeColumn::modifyQuery($this->query, $attributes);
        static::updateAttributes($changes);
    }

    /**
     * @param $attributes
     */
    public function updateAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if (!empty($this->{$attribute})) {
                $this->{$attribute} = $value;
            }
        }
    }
}
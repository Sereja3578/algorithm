<?php

namespace backend\components\grid;

use backend\widgets\GridView;
use DateTime;

class DatetimeRangeColumn extends DataColumn
{

    const DATE_FORMAT = 'd/m/Y H:i';

    const DATE_SEPARATOR = ' - ';

    const JS_DATE_FORMAT = 'DD/MM/YYYY HH:mm';

    public $hAlign = 'center';

    public $vAlign = 'middle';

    public $width = '10%';

    public $format = 'datetime';

    public $filterType = GridView::FILTER_DATE_RANGE;

    public $filterWidgetOptions = [];

    public $filterInputOptions = [
        'class' => 'form-control pull-right',
        'readonly' => true
    ];

    public function init()
    {
        $this->filterWidgetOptions = [
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => self::DATE_FORMAT,
                'timePicker' => true,
                'timePicker24Hour' => true,
                'timePickerIncrement' => 1,
                'locale' => [
                    'format' => self::DATE_FORMAT,
                    'separator' => self::DATE_SEPARATOR
                ],
                'autoclose' => true
            ],
            'pluginEvents' => [
                'cancel.daterangepicker' => "function(ev, picker) { \$(this).val('').trigger('change'); }",
                'apply.daterangepicker' => "function(ev, picker) { 
    if (\$(this).val() == '') {
        \$(this).val(
            picker.startDate.format('" . self::JS_DATE_FORMAT . "') 
            + ' - ' 
            + picker.endDate.format('" . self::JS_DATE_FORMAT . "')
        );
    }
    \$(this).trigger('change'); 
 }"
            ]
        ];

        parent::init();
    }

    /**
     * @param \yii\db\QueryInterface $query
     * @param array $columns attribute => value
     * @return array
     */
    public static function modifyQuery(&$query, array $columns)
    {
        foreach ($columns as $attribute => $value) {
            if (preg_match('~^(.+)' . preg_quote(static::DATE_SEPARATOR) . '(.+)$~', $value, $match)) {
                $date1 = DateTime::createFromFormat(static::DATE_FORMAT, $match[1]);
                $date2 = DateTime::createFromFormat(static::DATE_FORMAT, $match[2]);

                if ($date1 && $date2) {
                    //$columns[$attribute] = $date1 . static::DATE_SEPARATOR . $date2;
                    $query->andWhere([
                        'BETWEEN',
                        strpos($attribute, '.') ? $attribute : $query->a($attribute),
                        $date1->format('Y-m-d H:i:00'),
                        $date2->format('Y-m-d H:i:59')
                    ]);
                }
            }
        }
        return $columns;
    }
}

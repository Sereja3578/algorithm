<?php

namespace backend\components\grid;

use kartik\grid\DataColumn as KartikDataColumn;

class DataColumn extends KartikDataColumn
{
    public $vAlign = 'middle';

    // TODO: переделать на LinkColumn
    public $format = 'raw';

    // TODO: убрать
    public $filterOptions = [
        'pluginOptions' => ['allowClear' => true]
    ];
}

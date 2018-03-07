<?php

namespace backend\widgets;

use backend\components\grid\CurrencyColumn;
use Yii;
use kartik\detail\DetailView as KartikDetailView;
use yii\base\Model;
use yii\boost\db\ActiveRecord as BoostActiveRecord;
use backend\helpers\Html;
use yii\helpers\ArrayHelper;

class DetailView extends KartikDetailView
{

    /**
     * @param array $config
     * @return string
     */
    public static function widget($config = [])
    {
        $config = ArrayHelper::merge([
            'template' => function ($attribute, $index, $widget) {
                if (is_callable($attribute['value'])) {
                    $attribute['value'] = call_user_func($attribute['value'], $widget->model, $index, $widget);
                }

                $value = $widget->formatter->format($attribute['value'], $attribute['format']);

                return strtr("<tr><th>{label}</th><td>{value}</td></tr>", [
                    '{label}' => $attribute['label'],
                    '{value}' => $value,
                ]);
            },
            'condensed' => true,
            'enableEditMode' => false,
            'hover' => true,
            'mode' => static::MODE_VIEW,
            'fadeDelay' => 0,
            'hideIfEmpty' => false,
        ], $config);

        if (isset($config['disableAttributes']) && sizeof($config['disableAttributes']) > 0) {
            foreach ($config['attributes'] as $columnId => $gridColumnName) {
                if (isset($gridColumnName['attribute'])
                    && in_array($gridColumnName['attribute'], $config['disableAttributes'])
                ) {
                    unset($config['attributes'][$columnId]);
                    continue;
                }

                if (in_array($gridColumnName, $config['disableAttributes'])) {
                    unset($config['attributes'][$columnId]);
                    continue;
                }
            }
        }
        unset($config['disableAttributes']);
        return parent::widget($config);
    }

    public function init()
    {
        $model = $this->model;

        if ($model instanceof BoostActiveRecord) {
            foreach ($this->attributes as $key => $value) {
                if (is_int($key) && is_string($value)) {
                    if (in_array($value, $model::booleanAttributes())) {
                        $this->attributes[$key] = [
                            'attribute' => $value,
                            'value' => function ($form, $widget) use ($value) {
                                return Html::glyphIconBool($widget->model->$value);
                            },
                            'format' => 'raw'
                        ];
                    } elseif (in_array($value, $model::datetimeAttributes())) {
                        $this->attributes[$key] = [
                            'attribute' => $value,
                            'format' => 'datetime'
                        ];
                    } elseif (in_array($value, $model::dateAttributes())) {
                        $this->attributes[$key] = [
                            'attribute' => $value,
                            'format' => 'date'
                        ];
                    }
                }

                if (isset($this->attributes[$key]['format']) && $this->attributes[$key]['format'] == 'currency') {
                    $this->attributes[$key]['format'] = 'raw';
                    $this->attributes[$key]['value'] = function ($form, $widget) use ($model, $value) {
                        return CurrencyColumn::asCurrency($model, $model->{$value['attribute']});
                    };
                }
            }
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function renderDetailView()
    {
        foreach ($this->attributes as $key => $attribute) {
            if (!isset($attribute['attribute'])) {
                continue;
            }
            if ($this->model instanceof Model) {
                if ($hint = $this->model->getAttributeHint($attribute['attribute'])) {
                    $this->attributes[$key]['label'] = Html::tag('span', $attribute['label'], [
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => $hint
                    ]);
                }
            }
        }
        return parent::renderDetailView();
    }
}

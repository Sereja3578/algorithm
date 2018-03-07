<?php

namespace backend\helpers;

use yii\db\BaseActiveRecord;
use yii\base\Model;
use yii\helpers\BaseHtml;
use yii\helpers\Url;
use yii\web\Application;
use yii\widgets\ActiveField;
use ArrayObject;
use Yii;

class Html extends BaseHtml
{

    /**
     * @param string $name
     * @return string
     */
    public static function glyphIcon($name)
    {
        return static::tag('span', '', ['class' => 'glyphicon glyphicon-' . $name]);
    }

    /**
     * @return string
     */
    public static function glyphIconPlus()
    {
        return static::glyphIcon('plus');
    }

    /**
     * @return string
     */
    public static function glyphIconPencil()
    {
        return static::glyphIcon('pencil');
    }

    /**
     * @return string
     */
    public static function glyphIconMove()
    {
        return static::glyphIcon('move');
    }

    /**
     * @return string
     */
    public static function glyphIconFlag()
    {
        return static::glyphIcon('flag');
    }

    /**
     * @return string
     */
    public static function glyphIconEyeOpen()
    {
        return static::glyphIcon('eye-open');
    }

    /**
     * @return string
     */
    public static function glyphIconList()
    {
        return static::glyphIcon('list');
    }

    /**
     * @return string
     */
    public static function glyphIconFloppyDisk()
    {
        return static::glyphIcon('floppy-disk');
    }

    /**
     * @return string
     */
    public static function glyphIconFlash()
    {
        return static::glyphIcon('flash');
    }

    /**
     * @return string
     */
    public static function glyphIconFilter()
    {
        return static::glyphIcon('filter');
    }

    /**
     * @return string
     */
    public static function glyphIconTrue()
    {
        return static::glyphIcon('ok text-success');
    }

    /**
     * @return string
     */
    public static function glyphIconFalse()
    {
        return static::glyphIcon('remove text-danger');
    }
    
    /**
     * @return string
     */
    public static function glyphIconDownload()
    {
        return static::glyphIcon('download-alt');
    }    

    /**
     * @return string
     */
    public static function glyphIconRemove()
    {
        return static::glyphIcon('remove');
    }

    /**
     * @param bool $value
     * @return string
     */
    public static function glyphIconBool($value)
    {
        return $value ? static::glyphIconTrue() : static::glyphIconFalse();
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public static function createButton($text = null, array $options = [])
    {
        if (is_null($text)) {
            $text = Yii::t('buttons', 'Добавить');
        }
        // url
        $url = 'create';
        if (array_key_exists('url', $options)) {
            $url = $options['url'];
            unset($options['url']);
        }
        // css
        static::addCssClass($options, ['btn', 'btn-success', 'btn-sm']);
        $options['data-pjax'] = '0';
        return static::a(static::glyphIconPlus() . ' ' . $text, [$url], $options);
    }

    /**
     * @param BaseActiveRecord $model
     * @return string
     */
    public static function updateButton(BaseActiveRecord $model)
    {
        return static::a(
            static::glyphIconPencil() . ' ' . Yii::t('buttons', 'Редактировать'),
            array_merge(['update'], $model->getPrimaryKey(true)),
            ['class' => 'btn btn-primary btn-sm']
        );
    }

    /**
     * @param BaseActiveRecord $model
     * @param array $options
     * @return string
     */
    public static function switchButton(BaseActiveRecord $model, array $options = [])
    {
        if ($model->hasAttribute('enabled')) {
            $enabled = $model->getAttribute('enabled');
            // url
            $url = 'switch';
            if (array_key_exists('url', $options)) {
                $url = $options['url'];
                unset($options['url']);
            }
            // css
            static::addCssClass($options, ['btn', 'btn-default', 'btn-sm']);
            // method
            $method = 'POST';
            $methodKey = 'data-method';
            if (array_key_exists($methodKey, $options)) {
                $method = strtoupper($options[$methodKey]);
                unset($options[$methodKey]);
            }
            if (array_key_exists('data-pjax', $options) && $options['data-pjax']) {
                $methodKey = 'data-pjax-method';
            }
            $options[$methodKey] = $method;
            return static::a(
                static::glyphIconBool(!$enabled) . ' ' . ($enabled
                    ? Yii::t('buttons', 'Выключить')
                    : Yii::t('buttons', 'Включить')
                ),
                array_merge((array)$url, $model->getPrimaryKey(true)),
                $options
            );
        } else {
            return '';
        }
    }

    /**
     * @param BaseActiveRecord $model
     * @param array $options
     * @return string
     */
    public static function switchMiniButton(BaseActiveRecord $model, array $options = [])
    {
        if ($model->hasAttribute('enabled')
            && (!$model->hasAttribute('is_default') || !$model->getAttribute('is_default'))) {
            // url
            $url = 'switch';
            if (array_key_exists('url', $options)) {
                $url = $options['url'];
                unset($options['url']);
            }
            // css
            static::addCssClass($options, ['btn', 'btn-default', 'btn-xs']);
            // method
            $method = 'POST';
            $methodKey = 'data-method';
            if (array_key_exists($methodKey, $options)) {
                $method = strtoupper($options[$methodKey]);
                unset($options[$methodKey]);
            }
            if (array_key_exists('data-pjax', $options) && $options['data-pjax']) {
                $methodKey = 'data-pjax-method'; // look for 'pjax:beforeSend' at 'backend\web\js\site.js'
            }
            $options[$methodKey] = $method;
            return static::a(
                $model->getAttribute('enabled') ? Yii::t('buttons', 'Выкл') : Yii::t('buttons', 'Вкл'),
                array_merge((array)$url, $model->getPrimaryKey(true)),
                $options
            );
        } else {
            return '';
        }
    }

    /**
     * @param BaseActiveRecord $model
     * @param array $options
     * @return string
     */
    public static function defaultSwitchMiniButton(BaseActiveRecord $model, array $options = [])
    {
        if ($model->hasAttribute('is_default') && !$model->getAttribute('is_default')
            && (!$model->hasAttribute('enabled') || $model->getAttribute('enabled'))) {
            // url
            $url = 'default';
            if (array_key_exists('url', $options)) {
                $url = $options['url'];
                unset($options['url']);
            }
            // css
            static::addCssClass($options, ['btn', 'btn-default', 'btn-xs']);
            // method
            $method = 'POST';
            $methodKey = 'data-method';
            if (array_key_exists($methodKey, $options)) {
                $method = strtoupper($options[$methodKey]);
                unset($options[$methodKey]);
            }
            if (array_key_exists('data-pjax', $options) && $options['data-pjax']) {
                $methodKey = 'data-pjax-method'; // look for 'pjax:beforeSend' at 'backend\web\js\site.js'
            }
            $options[$methodKey] = $method;
            return static::a(
                Yii::t('buttons', 'Вкл'),
                array_merge((array)$url, $model->getPrimaryKey(true)),
                $options
            );
        } else {
            return '';
        }
    }

    /**
     * @param BaseActiveRecord $model
     * @return string
     */
    public static function viewButton(BaseActiveRecord $model)
    {
        return static::a(
            static::glyphIconEyeOpen() . ' ' . Yii::t('buttons', 'Просмотр'),
            array_merge(['view'], $model->getPrimaryKey(true)),
            ['class' => 'btn btn-success btn-sm']
        );
    }

    /**
     * @param string $text
     * @return string
     */
    public static function indexButton($text = null)
    {
        if (is_null($text)) {
            $text = Yii::t('buttons', 'Список');
        }
        return static::a(
            static::glyphIconList() . ' ' . $text,
            ['index'],
            ['class' => 'btn btn-default btn-sm']
        );
    }

    /**
     * @param BaseActiveRecord|Model $model
     * @return string
     */
    public static function saveButton(Model $model)
    {
        if ($model instanceof BaseActiveRecord) {
            $isNewRecord = $model->getIsNewRecord();
            return static::submitButton(
                static::glyphIconFloppyDisk() . ' ' . ($isNewRecord
                    ? Yii::t('buttons', 'Создать')
                    : Yii::t('buttons', 'Сохранить')
                ),
                ['class' => $isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            );
        } else {
            return static::submitButton(
                static::glyphIconFloppyDisk() . ' ' . Yii::t('buttons', 'Сохранить'),
                ['class' => 'btn btn-success']
            );
        }
    }

    /**
     * @param string $content
     * @return string
     */
    public static function performButton($content = null)
    {
        $content = $content ?: Yii::t('buttons', 'Выполнить');
        return static::submitButton(
            static::glyphIconFlash() . ' ' . $content,
            ['class' => 'btn btn-success']
        );
    }

    /**
     * @return string
     */
    public static function filterButton()
    {
        return static::submitButton(
            static::glyphIconFilter() . ' ' . Yii::t('buttons', 'Фильтр'),
            ['class' => 'btn btn-primary']
        );
    }

    /**
     * @param $label
     * @param string $class
     * @return string
     */
    public static function customButton($label, $class = 'btn-default', $submit = true, $options = [])
    {
        if ($submit) {
            return static::submitButton(
                Yii::t('buttons', $label),
                ['class' => 'btn ' . $class]
            );
        } else {
            return static::a(
                $label,
                null,
                array_merge(['class' => "btn $class"], $options)
            );
        }
    }

    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public static function exportButton($text = null, $options = [])
    {
        // url
        $url = '#';
        if (array_key_exists('url', $options)) {
            $url = $options['url'];
            unset($options['url']);
        }
        // css
        static::addCssClass($options, ['btn', 'btn-success', 'btn-sm', 'btn-main-modal', 'btn-primary']);
        return static::a(static::glyphIconDownload() . '&nbsp;&nbsp;' . $text, $url, $options);        
    }

    /**
     * @param string $permissionPrefix
     * @param string $createButtonText
     * @return string[]
     */
    public static function indexViewButtons($permissionPrefix, $createButtonText = null)
    {
        $buttons = [];

        if (!(Yii::$app instanceof Application)) {
            return [];
        }

        if (Yii::$app->getUser()->can($permissionPrefix . 'create')) {
            $buttons[] = Html::createButton($createButtonText);
        }
        return $buttons;
    }

    /**
     * @param string $permissionPrefix
     * @param BaseActiveRecord $model
     * @param string $indexButtonText
     * @return string[]
     */
    public static function viewViewButtons($permissionPrefix, BaseActiveRecord $model, $indexButtonText = null)
    {
        if (!(Yii::$app instanceof Application)) {
            return [];
        }

        $buttons = [];
        $webUser = Yii::$app->getUser();
        $params = $model->getPrimaryKey(true);
        $params['model'] = $model;
        if ($model->hasAttribute('enabled') && $webUser->can($permissionPrefix . 'switch', $params)) {
            $buttons[] = Html::switchButton($model);
        }
        if ($webUser->can($permissionPrefix . 'update', $params)) {
            $buttons[] = Html::updateButton($model);
        }
        if ($webUser->can($permissionPrefix . 'create')) {
            $buttons[] = Html::createButton();
        }
        if ($webUser->can($permissionPrefix . 'index')) {
            $buttons[] = Html::indexButton($indexButtonText);
        }
        return $buttons;
    }

    /**
     * @param string $permissionPrefix
     * @param BaseActiveRecord $model
     * @param string $indexButtonText
     * @return string[]
     */
    public static function updateViewButtons($permissionPrefix, BaseActiveRecord $model, $indexButtonText = null)
    {
        if (!(Yii::$app instanceof Application)) {
            return [];
        }

        $buttons = [];
        $webUser = Yii::$app->getUser();
        $params = $model->getPrimaryKey(true);
        $params['model'] = $model;
        if ($webUser->can($permissionPrefix . 'view', $params)) {
            $buttons[] = Html::viewButton($model);
        }
        if ($webUser->can($permissionPrefix . 'index')) {
            $buttons[] = Html::indexButton($indexButtonText);
        }
        return $buttons;
    }

    /**
     * @param string $permissionPrefix
     * @param string $indexButtonText
     * @return string[]
     */
    public static function createViewButtons($permissionPrefix, $indexButtonText = null)
    {
        if (!(Yii::$app instanceof Application)) {
            return [];
        }

        $buttons = [];
        if (Yii::$app->getUser()->can($permissionPrefix . 'index')) {
            $buttons[] = Html::indexButton($indexButtonText);
        }
        return $buttons;
    }

    /**
     * @param string $permissionPrefix
     * @param BaseActiveRecord $model
     * @param ActiveField[] $fields
     * @return ActiveField[]
     */
    public static function filterFormFieldsByBusinessRules($permissionPrefix, BaseActiveRecord $model, array $fields)
    {
        if (!(Yii::$app instanceof Application)) {
            return [];
        }

        $formFields = new ArrayObject(array_combine(array_map(function (ActiveField $field) {
            return $field->attribute;
        }, $fields), $fields));
        $webUser = Yii::$app->getUser();
        if ($model->getIsNewRecord()) {
            $webUser->can($permissionPrefix . 'create', [
                'model' => $model,
                'formFields' => $formFields
            ]);
        } else {
            $params = $model->getPrimaryKey(true);
            $params['model'] = $model;
            $params['formFields'] = $formFields;
            $webUser->can($permissionPrefix . 'update', $params);
        }
        return (array)$formFields;
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function defaultButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-default btn-sm'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function primaryButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-primary btn-sm'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function successButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-success btn-sm'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function infoButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-info btn-sm'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function dangerButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-danger btn-sm'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function defaultXSButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-default btn-xs'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function primaryXSButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-primary btn-xs'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function successXSButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-success btn-xs'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function infoXSButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-info btn-xs'], $options)
        );
    }

    /**
     * @param string $content
     * @param [] $to
     * @param [] $options
     * @return string
     */
    public static function dangerXSButton($content, $to, $options = [])
    {
        return static::a(
            $content,
            $to,
            array_merge(['class' => 'btn btn-danger btn-xs'], $options)
        );
    }

    /**
     * @param BaseActiveRecord $model
     * @param array $options
     * @return string
     */
    public static function switchItem(BaseActiveRecord $model, array $options = [])
    {
        if ($model->hasAttribute('enabled')) {
            $enabled = $model->getAttribute('enabled');
            // url
            $url = 'switch';
            if (array_key_exists('url', $options)) {
                $url = $options['url'];
                unset($options['url']);
            }

            // method
            $method = 'POST';
            $methodKey = 'data-method';
            if (array_key_exists($methodKey, $options)) {
                $method = strtoupper($options[$methodKey]);
                unset($options[$methodKey]);
            }
            if (array_key_exists('data-pjax', $options) && $options['data-pjax']) {
                $methodKey = 'data-pjax-method';
            }
            $options[$methodKey] = $method;
            return [
                'label' => ($enabled
                        ? Yii::t('buttons', 'Выключить')
                        : Yii::t('buttons', 'Включить')
                    ),
                'url' => array_merge((array)$url, $model->getPrimaryKey(true)),
                'linkOptions' => $options,
                'encode' => false
            ];
        }
        return null;
    }

    /**
     * @param BaseActiveRecord $model
     * @param array $options
     * @return string
     */
    public static function switchDeleteItem(BaseActiveRecord $model, array $options = [])
    {
        if ($model->hasAttribute('deleted')) {
            $deleted = $model->getAttribute('deleted');

            // method
            $method = 'POST';
            $methodKey = 'data-method';
            if (array_key_exists($methodKey, $options)) {
                $method = strtoupper($options[$methodKey]);
                unset($options[$methodKey]);
            }
            if (array_key_exists('data-pjax', $options) && $options['data-pjax']) {
                $methodKey = 'data-pjax-method';
            }
            $options[$methodKey] = $method;

            if (!$deleted)  {
                $options['data-confirm'] = Yii::t('kvgrid', 'Вы действительно хотите удалить элемент?');
                $label = Yii::t('buttons', 'Удалить');
                $url = 'delete';
            } else {
                $options['data-confirm'] = Yii::t('kvgrid', 'Вы действительно хотите восстановить элемент?');
                $label = Yii::t('buttons', 'Восстановить');
                $url = 'restore';
            }

            if (array_key_exists('url', $options)) {
                $url = $options['url'];
                unset($options['url']);
            }



            return [
                'label' => $label,
                'url' => array_merge((array)$url, $model->getPrimaryKey(true)),
                'linkOptions' => $options,
                'encode' => false
            ];
        }
        return null;
    }

    /**
     * @return string
     */
    public static function languageLinks()
    {
        $languages = [
            'ru' => Yii::t('const', 'Русский'),
            'en' => Yii::t('const', 'Английский'),
        ];

        $links = [];
        foreach ($languages as $code => $name) {
            $links[] = static::a($name, Url::current(['language' => $code]));
        }

        return implode(' &bull; ', $links);
    }

    public static function languageItemsArray()
    {
        return [
            'ru' => Yii::t('const', 'Русский'),
            'en' => Yii::t('const', 'Английский'),
        ];
    }
}

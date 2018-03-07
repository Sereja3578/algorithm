<?php

namespace backend\widgets;

use backend\components\View;
use yii\helpers\Html;
use kartik\grid\GridView as KartikGridView;
use backend\components\grid\ButtonColumn;
use Yii;
use yii\helpers\Inflector;

class GridView extends KartikGridView
{
    public static $exportFilename = null;

    public $showPageSummary = true;

    public $showCustomPageSummary = false;
    public $showCustomBeforeHeader = false;
    public $customBeforeHeader = [];
    public $beforeSummary = [];
    public $afterSummary = [];

    public $captionOptions = [
        'style' => 'display: none'
    ];

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $index => $column) {
            /* @var DataColumn $column */
            if ($this->resizableColumns && $this->persistResize) {
                $column->headerOptions['data-resizable-column-id'] = "kv-col-{$index}";
            }
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition == self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition == self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }
        return "<thead>\n" .
            $this->renderBeforeHeader() . "\n" .
            $content . "\n" .
            $this->generateRows($this->afterHeader) . "\n" .
            "</thead>";
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());

        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows)) {
            $colspan = count($this->columns);

            $content = "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        } else {
            $content =  "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
        }

        if (!$this->showPageSummary && $this->showCustomPageSummary) {
            return $content . $this->renderPageSummary();
        }
        return $content;
    }

    /**
     * Custom renders the table page summary.
     *
     * @return string the rendering result.
     */
    public function renderBeforeHeader()
    {
        $content = $this->beforeHeader;

        if ($this->showCustomBeforeHeader) {
            if (!$content) {
                $content = "<div class='custom-before-header'></div>";
            }

            if ($this->customBeforeHeader) {
                foreach ($this->customBeforeHeader as &$row) {
                    if (!isset($row['options'])) {
                        $row['options'] = $this->pageSummaryRowOptions;
                    }
                }
            }

            return strtr(
                $content,
                [
                    "<div class='custom-before-header'>" => "<div class='custom-before-header'>\n" . parent::generateRows($this->customBeforeHeader),
                    "</div>" => "\n</div>",
                ]
            );
        }

        return parent::generateRows($content);
    }

    /**
     * Custom renders the table page summary.
     *
     * @return string the rendering result.
     */
    public function renderPageSummary()
    {
        $content = parent::renderPageSummary();

        if ($this->showCustomPageSummary) {
            if (!$content) {
                $content = "<tfoot></tfoot>";
            }

            if ($this->beforeSummary) {
                foreach ($this->beforeSummary as &$row) {
                    if (!isset($row['options'])) {
                        $row['options'] = $this->pageSummaryRowOptions;
                    }
                }
            }

            if ($this->afterSummary) {
                foreach ($this->afterSummary as &$row) {
                    if (!isset($row['options'])) {
                        $row['options'] = $this->pageSummaryRowOptions;
                    }
                }
            }

            return strtr(
                $content,
                [
                    '<tfoot>' => "<tfoot>\n" . parent::generateRows($this->beforeSummary),
                    '</tfoot>' => parent::generateRows($this->afterSummary) . "\n</tfoot>",
                ]
            );
        }

        return $content;
    }

    public static $exportCustomConfig = [
        self::CSV => [
            'config' => [
                'colDelimiter' => ";",
                'mime' => "application/csv",
                'encoding' => 'utf-8',
            ],
            'mime' => "application/csv",
        ],
        self::JSON => [],
        self::EXCEL => []
    ];

    /**
     * @param array $config
     * @return string
     */
    public static function widget($config = [])
    {
        if (isset($config['exportFilename'])) {
            static::$exportFilename = $config['exportFilename'];
        } else {
            $class = 'Export';
            if (isset($config['filterModel'])) {
                $class = get_class($config['filterModel']);
            }

            static::$exportFilename = $class;
        }

        unset($config['exportFilename']);

        foreach (static::$exportCustomConfig as $format => $settings) {
            static::$exportCustomConfig[$format]['filename'] = static::$exportFilename;
        }

        if (!isset($config['toolbar'])) {
            $config['toolbar'] = [
                [
                    'content' => \yii\helpers\Html::a(
                        '<i class="glyphicon glyphicon-repeat"></i>',
                        ['index'],
                        ['data-pjax' => 0,
                            'class' => 'btn btn-default btn-sm',
                            'title' => Yii::t('info', 'Сбросить')
                        ]
                    ),
                ],
                '{export}',
            ];
        }

        if (!isset($config['toggleDataOptions'])) {
            $config['toggleDataOptions'] = [
                'all' => [
                    'icon' => 'resize-full',
                    'class' => 'btn btn-default btn-sm',
                ],
                'page' => [
                    'icon' => 'resize-small',
                    'class' => 'btn btn-default btn-sm',
                ],
            ];
        }

        $config = \yii\helpers\ArrayHelper::merge([
            'exportConfig' => static::$exportCustomConfig,
            'export' => [
                'encoding' => 'utf-8',
            ],
        ], [
            'pjax' => true,
            'bordered' => true,
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'summary' => true,
            'hover' => false,
            'showPageSummary' => true,
            'persistResize' => true,
            'resizableColumns' => false,
            'perfectScrollbar' => false,
            'panel' => [
                'heading' => false,
            ],
            'panelTemplate' => '
                {panelBefore}
                {items}
                {panelAfter}
                {panelFooter}
            ',
            'export' => [
                'target' => GridView::TARGET_SELF,
            ],
            'panelBeforeTemplate' => '
            <div class="pull-left"></div>
            <div class="pull-right">
                <div class="btn-toolbar kv-grid-toolbar" role="toolbar">{toolbar}</div>
            </div>
            <div class="clearfix"></div>',
        ], $config);

        if (isset($config['disableColumns']) && sizeof($config['disableColumns']) > 0) {
            foreach ($config['columns'] as $columnId => $column) {
                if (is_array($column)) {
                    if (isset($column['attribute'])) {
                        $attribute = $column['attribute'];
                    } else {
                        $classPath = explode('\\', str_replace('Column', '', $column['class']));
                        $className = array_pop($classPath);
                        $attribute = Inflector::camel2id($className, '_');
                    }
                } else {
                    $attribute = $column;
                }

                if (in_array($attribute, $config['disableColumns'])) {
                    unset($config['columns'][$columnId]);
                }
            }
        }

        unset($config['disableColumns']);

        return parent::widget($config);
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if (!$this->id) {
            $this->id = uniqid();
        }
        if (!isset($this->pjaxSettings['options'])) {
            $this->pjaxSettings['options'] = [];
        }
        $pjaxOptions = [
            'id' => $this->id,
            'timeout' => false,
            'clientOptions' => [
                'url' => Yii::$app->request->url,
                'method' => 'POST'
            ]
        ];
        $this->pjaxSettings['options'] = array_replace_recursive($pjaxOptions, $this->pjaxSettings['options']);
        $this->caption = $this->view->title;
        $js = "
$(document).on('pjax:click', function(e) {
    e.preventDefault();
    $.pjax.reload({container: '" . $this->id . "'});
});
";
        $view = $this->getView();
        $view->registerJs($js, View::POS_END);
        parent::run();

    }
}

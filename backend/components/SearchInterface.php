<?php

namespace backend\components;

interface SearchInterface
{
    /**
     * Список колонок Grid
     * @return mixed
     */
    public function getGridColumns();

    /**
     * Заголовок GridView
     * @return mixed
     */
    public function getGridTitle();

    /**
     * Список безопасных атрибутов
     * @return mixed
     */
    public function getSafeAttributes();

    /**
     * Список включенных атрибутов
     * @return mixed
     */
    public function getEnableColumns();

    /**
     * Список выключенных атрибутов
     * @return mixed
     */
    public function getDisableColumns();

    /**
     * Разделение по листам при экспорте
     * @return bool
     */
    public function getSeparateBySheets();

    /**
     * Заголовок листа
     * @return string
     */
    public function getSheetTitle();

    /**
     * Показывать заголовок таблицы
     * @return string
     */
    public function getShowTableTitle();

    /**
     * @return array
     */
    public function getAfterSummaryColumns();
}

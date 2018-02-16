<?php

namespace common\components;

use Yii;

class QuoteChunkSearch extends KeyValue
{

    const CHUNK_SIZE = 1800;

    public $assetName;
    public $dateInterval;

    public $timestamp;
    public $asset;
    public $ask;
    public $bid;
    public $diff;
    public $repeat;
    public $source;
    public $microTimestamp;


    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->pureRedisQuotes;
    }
    /**
     * @inheritdoc
     */
    public static function primaryKey($asArray = false)
    {
        return $asArray ? ['quote_chunk'] : 'quote_chunk';
    }

    /**
     * @return KeyValueDataProvider
     */
    public function search()
    {
        $query = self::find();
        $range = explode(' - ', $this->dateInterval);
        $range[0] = $this->getChunk($range[0]);
        $range[1] = $this->getChunk($range[1]) ?: $range[0];
        $assets = $this->assetName ? [$this->assetName] : $this->getAsset();
        while($range[0] <= $range[1]) {
            foreach($assets as $asset) {
                if($this->source) {
                    $asset .= '_' . $this->source;
                }
                $query->andWhere([$asset, $range[0]]);
            }
            $range[0] += self::CHUNK_SIZE;
        }
        return new KeyValueDataProvider([
            'query' => $query,
            'decorator' => function ($data) {
                return array_map(function ($line) {
                    $row = explode(',', $line);
                    return [
                        'timestamp' => date('Y-m-d H:i:s', $row[0]),
                        'microTimestamp' => preg_match('/^\d+\.\d+/', $row[0]) ? $row[0] : $row[0] . '.0000',
                        'ask' => $row[1],
                        'bid' => $row[2],
                        'asset' => $row[3],
                        'diff' => $row[4],
                        'repeat' => $row[5],
                        'source' => $row[6]
                    ];
                }, $data);
            }
        ]);
    }

    /**
     * @return array
     */
    public function getAsset()
    {
        $keys = self::find()->where(['*'])->keys();
        foreach($keys as &$key) {
            preg_match('/^[^:]+\:([^:]+)\:?/', $key, $matches);
            if($matches) {
                $key = $matches[1];
            }
        }
        return array_unique($keys);
    }


    /**
     * @param string $date
     * @return int
     */
    public function getChunk($date)
    {
        $timeZone = new \DateTimeZone('+0000');
        $date = new \DateTime($date, $timeZone);
        $unixTime = $date->getTimestamp();
        $modulo = $unixTime % self::CHUNK_SIZE;
        if ($modulo === 0) {
            return $unixTime;
        } else {
            return $unixTime + self::CHUNK_SIZE - $modulo;
        }
    }
}
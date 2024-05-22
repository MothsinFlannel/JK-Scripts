<?php


namespace app\components;


use vr\core\ArrayHelper;
use vr\core\Inflector;
use Yii;
use yii\db\ActiveQuery;
use yii\web\RangeNotSatisfiableHttpException;

/**
 * Class ExportQueryTrait
 * @package app\components
 */
trait ExportQueryTrait
{
    /**
     * @param ActiveQuery $query
     * @param string $filename
     * @throws RangeNotSatisfiableHttpException
     */
    protected function queryToCsv(ActiveQuery $query, string $filename = 'export.csv'): void
    {
        $query->limit(-1)->offset(0)->asArray();

        $stream = fopen('php://memory', 'w');
        fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));

        /**
         * @var int $i
         * @var array $record
         */
        foreach ($query->each() as $i => $record) {
            if (!$i) {
                $this->putLine($stream, array_keys($record), true);
            }

            $this->putLine($stream, $record);
        }

        Yii::$app->response->sendStreamAsFile($stream, $filename, ['mimeType' => 'application/csv', 'inline' => true]);
    }

    /**
     * @param $handle
     * @param array $content
     * @param bool $capitalize
     */
    private function putLine($handle, array $content, bool $capitalize = false): void
    {
        foreach ($content as $key => $item) {
            if (is_array($item)) {
                $content[$key] = null;
            }

            if ($capitalize) {
                $content[$key] = strtoupper(is_numeric($key) ? Inflector::camel2words($item) : $item);
            }
        }

        fputcsv($handle, $content);
        if (0 === fseek($handle, -1, SEEK_CUR)) {
            fwrite($handle, "\r\n");
        }
    }

    /**
     * @param array $data
     * @param array $headers
     * @param bool $filterByHeaders
     * @param bool $sendStream
     * @return mixed
     * @throws RangeNotSatisfiableHttpException
     */
    protected function arrayToCsv(array $data, array $headers = [], bool $filterByHeaders = false, $sendStream = true): mixed
    {
        $stream = fopen('php://memory', 'w');
        fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if ($headers) {
            $this->putLine($stream, $headers, true);
        }

        if ($filterByHeaders) {
            $data = $this->filter($data, $headers);
        }

        /**
         * @var int $i
         * @var array $record
         */
        foreach ($data as $record) {
            $this->putLine($stream, $record);
        }

        if ($sendStream) {
            Yii::$app->response->sendStreamAsFile($stream, 'export.csv', ['mimeType' => 'application/csv', 'inline' => true]);
        }

        fseek($stream, 0);
        return stream_get_contents($stream);
    }

    /**
     * @param array $results
     * @param array $headers
     * @return array
     */
    protected function filter(array $results, array $headers): array
    {
        $keys = [];
        foreach ($headers as $index => $header) {
            $keys[] = is_int($index) ? $header : $index;
        }

        return ArrayHelper::getColumn($results, function ($record) use ($keys) {
            return ArrayHelper::filter($record, $keys);
        });
    }
}
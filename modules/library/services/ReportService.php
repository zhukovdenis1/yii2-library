<?php

namespace app\modules\library\services;

use Yii;
use yii\data\ArrayDataProvider;

/**
 * Report Service
 * Contains all business logic for reports
 */
class ReportService
{
    /**
     * Get TOP-10 authors by year
     *
     * @param int $year
     * @return ArrayDataProvider
     */
    public function getTopAuthorsByYear($year)
    {
        $sql = "
            SELECT
                a.id,
                a.first_name,
                a.last_name,
                a.middle_name,
                COUNT(DISTINCT b.id) as book_count
            FROM {{%author}} a
            INNER JOIN {{%book_author}} ba ON a.id = ba.author_id
            INNER JOIN {{%book}} b ON ba.book_id = b.id
            WHERE b.year = :year
            GROUP BY a.id
            ORDER BY book_count DESC, a.last_name ASC
            LIMIT 10
        ";

        $authors = Yii::$app->db->createCommand($sql, [':year' => $year])->queryAll();

        $data = array_map(function ($row) {
            return [
                'id' => $row['id'],
                'full_name' => trim($row['last_name'] . ' ' . $row['first_name'] . ' ' . ($row['middle_name'] ?? '')),
                'book_count' => (int)$row['book_count'],
            ];
        }, $authors);

        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
        ]);
    }

    /**
     * Get available years from books
     *
     * @return array
     */
    public function getAvailableYears(): ?array
    {
        return Yii::$app->db->createCommand(
            "SELECT DISTINCT year FROM {{%book}} ORDER BY year DESC"
        )->queryColumn();
    }
}

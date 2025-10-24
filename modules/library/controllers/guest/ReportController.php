<?php

namespace app\modules\library\controllers\guest;

use Yii;
use app\modules\library\controllers\BaseController;

/**
 * ReportController for guests (unauthenticated users)
 * Handles TOP-10 authors report
 */
class ReportController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'report';
    }

    /**
     * @return ReportService
     */
    protected function getReportService()
    {
        return Yii::$app->reportService;
    }

    /**
     * Displays TOP-10 authors by year
     */
    public function actionTopAuthors($year = null)
    {
        $availableYears = $this->getReportService()->getAvailableYears();

        if ($year === null && !empty($availableYears)) {
            $year = $availableYears[0];
        }

        $dataProvider = null;
        if ($year !== null) {
            $dataProvider = $this->getReportService()->getTopAuthorsByYear($year);
        }

        return $this->render('top-authors', [
            'dataProvider' => $dataProvider,
            'year' => $year,
            'availableYears' => $availableYears,
        ]);
    }
}

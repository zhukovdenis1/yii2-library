<?php

namespace app\modules\library\controllers\guest;

use Yii;
use yii\web\NotFoundHttpException;
use app\modules\library\controllers\BaseController;

/**
 * BookController for guests (unauthenticated users)
 * Handles read-only operations
 */
class BookController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'book';
    }

    /**
     * @return BookService
     */
    protected function getBookService()
    {
        return Yii::$app->bookService;
    }

    /**
     * Lists all books
     */
    public function actionIndex()
    {
        $dataProvider = $this->getBookService()->getDataProvider();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single book
     */
    public function actionView($id)
    {
        $model = $this->getBookService()->findById($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
}

<?php

namespace app\modules\library\controllers\guest;

use app\modules\library\controllers\BaseController;
use app\modules\library\models\forms\SubscriptionForm;
use app\modules\library\services\AuthorService;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * AuthorController for guests (unauthenticated users)
 * Handles read-only operations and subscriptions
 */
class AuthorController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'author';
    }

    /**
     * @return AuthorService
     */
    protected function getAuthorService(): AuthorService
    {
        return Yii::$app->authorService;
    }

    /**
     * Lists all authors
     */
    public function actionIndex()
    {
        $dataProvider = $this->getAuthorService()->getDataProvider();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single author with subscription form
     */
    public function actionView($id)
    {
        $model = $this->getAuthorService()->findById($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $booksDataProvider = $this->getAuthorService()->getAuthorBooksDataProvider($model);
        $subscriptionForm = new SubscriptionForm();

        return $this->render('view', [
            'model' => $model,
            'booksDataProvider' => $booksDataProvider,
            'subscriptionForm' => $subscriptionForm,
        ]);
    }

    /**
     * Subscribe to author notifications
     */
    public function actionSubscribe($id)
    {
        $model = $this->getAuthorService()->findById($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $subscriptionForm = new SubscriptionForm();


        if ($subscriptionForm->load(Yii::$app->request->post())) {
            $subscriptionForm->author_id = $id;
            if ($subscriptionForm->validate()) {
                try {
                    $subscription = $this->getAuthorService()->subscribe($id, $subscriptionForm->phone);

                    if ($subscription && !$subscription->hasErrors()) {
                        Yii::$app->session->setFlash('success', Yii::t('app', 'You have been successfully subscribed to author updates.'));
                        return $this->redirect(['view', 'id' => $id]);
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Error subscribing to author.'));
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Error: {error}', ['error' => $e->getMessage()]));
                }
            }
        }

        $booksDataProvider = $this->getAuthorService()->getAuthorBooksDataProvider($model);

        return $this->render('view', [
            'model' => $model,
            'booksDataProvider' => $booksDataProvider,
            'subscriptionForm' => $subscriptionForm,
        ]);
    }
}

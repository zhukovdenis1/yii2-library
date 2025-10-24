<?php

namespace app\modules\library\controllers\user;


use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\modules\library\models\Author;
use app\modules\library\controllers\BaseController;
use app\modules\library\services\AuthorService;

/**
 * AuthorController for authenticated users
 */
class AuthorController extends BaseController
{
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


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all authors
     */
    public function actionIndex()
    {
        $dataProvider = $this->authorService->getDataProvider();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single author
     */
    public function actionView($id)
    {
        $model = $this->authorService->findById($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $booksDataProvider = $this->authorService->getAuthorBooksDataProvider($model);

        return $this->render('view', [
            'model' => $model,
            'booksDataProvider' => $booksDataProvider,
        ]);
    }

    /**
     * Creates a new author
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('createAuthor')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }

        $model = new Author();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $author = $this->authorService->create(['Author' => $model->attributes]);

                    if ($author->hasErrors()) {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Error creating author.'));
                    } else {
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Author has been created successfully.'));
                        return $this->redirect(['view', 'id' => $author->id]);
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Error creating author: {error}', ['error' => $e->getMessage()]));
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing author
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('updateAuthor')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }

        $model = $this->authorService->findById($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $this->getAuthorService()->update($model, ['Author' => $model->attributes]);
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Author has been updated successfully.'));
                    return $this->redirect(['view', 'id' => $model->id]);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Error updating author: {error}', ['error' => $e->getMessage()]));
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing author
     */
    public function actionDelete($id)
    {
        $model = $this->authorService->findById($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        try {
            $this->getAuthorService()->delete($model);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Author has been deleted successfully.'));
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Error deleting author: {error}', ['error' => $e->getMessage()]));
        }

        return $this->redirect(['index']);
    }
}

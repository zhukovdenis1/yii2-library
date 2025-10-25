<?php

namespace app\modules\library\controllers\user;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\modules\library\models\forms\BookForm;
use app\modules\library\controllers\BaseController;
use app\modules\library\services\BookService;
use app\modules\library\services\AuthorService;

class BookController extends BaseController
{
    public function getViewPath()
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'book';
    }

    protected function getBookService(): BookService
    {
        return Yii::$app->bookService;
    }

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

    public function actionIndex()
    {
        $dataProvider = $this->getBookService()->getDataProvider();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->getBookService()->findByIdWithAuthors($id);

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->can('createBook')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }

        $form = new BookForm('create');
        $authors = $this->getAuthorService()->getAllAuthors();

        if (empty($authors)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Error creating book: {error}', [
                'error' => Yii::t('app', 'Create authors first')
            ]));
            return $this->redirect(['author/index']);
        }

        if ($form->load(Yii::$app->request->post())) {
            $form->imageFile = UploadedFile::getInstance($form, 'imageFile');

            if ($form->validate()) {
                try {
                    $book = $this->getBookService()->createFromForm($form);

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Book has been created successfully.'));
                    return $this->redirect(['view', 'id' => $book->id]);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Error creating book: {error}', [
                        'error' => $e->getMessage()
                    ]));
                }
            }
        }

        return $this->render('create', [
            'model' => $form,
            'authors' => $authors,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('updateBook')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }

        $book = $this->getBookService()->findByIdWithAuthors($id);
        if (!$book) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        // Проверяем наличие авторов в системе
        $authors = $this->getAuthorService()->getAllAuthors();
        if (empty($authors)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'No authors available. Please create authors first.'));
            return $this->redirect(['author/index']);
        }

        $form = new BookForm('update', $book);

        if ($form->load(Yii::$app->request->post())) {
            $form->imageFile = UploadedFile::getInstance($form, 'imageFile');

            if ($form->validate()) {
                try {
                    $this->getBookService()->updateFromForm($book, $form);

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Book has been updated successfully.'));
                    return $this->redirect(['view', 'id' => $book->id]);
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Error updating book: {error}', [
                        'error' => $e->getMessage()
                    ]));
                }
            }
        }

        return $this->render('update', [
            'model' => $form,
            'authors' => $authors,
            'book' => $book,
        ]);
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('deleteBook')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }

        $model = $this->getBookService()->findById($id);
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        try {
            $this->getBookService()->delete($model);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Book has been deleted successfully.'));
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Error deleting book: {error}', [
                'error' => $e->getMessage()
            ]));
        }

        return $this->redirect(['index']);
    }
}
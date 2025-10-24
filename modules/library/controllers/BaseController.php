<?php

namespace app\modules\library\controllers;

use Yii;
use yii\web\Controller;

/**
 * Base controller for library module
 * Provides common methods for all controllers
 */
class BaseController extends Controller
{
    /**
     * Get common view parameters
     *
     * @return array
     */
    protected function getCommonViewParams()
    {
        return [
            'isGuest' => Yii::$app->user->isGuest,
            'userId' => Yii::$app->user->id,
            'canCreateBook' => !Yii::$app->user->isGuest && Yii::$app->user->can('createBook'),
            'canUpdateBook' => !Yii::$app->user->isGuest && Yii::$app->user->can('updateBook'),
            'canDeleteBook' => !Yii::$app->user->isGuest && Yii::$app->user->can('deleteBook'),
            'canCreateAuthor' => !Yii::$app->user->isGuest && Yii::$app->user->can('createAuthor'),
            'canUpdateAuthor' => !Yii::$app->user->isGuest && Yii::$app->user->can('updateAuthor'),
            'canDeleteAuthor' => !Yii::$app->user->isGuest && Yii::$app->user->can('deleteAuthor'),
        ];
    }

    /**
     * Render view with common parameters
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        // Set common params for layout
        Yii::$app->view->params['isGuest'] = Yii::$app->user->isGuest;
        Yii::$app->view->params['userId'] = Yii::$app->user->id;
        Yii::$app->view->params['username'] = Yii::$app->user->isGuest ? null : Yii::$app->user->identity->username;

        return parent::render($view, array_merge($this->getCommonViewParams(), $params));
    }
}

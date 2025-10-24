<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * Base controller for application
 * Provides common methods for all controllers
 */
class BaseController extends Controller
{
    /**
     * Render view with common parameters for layout
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

        return parent::render($view, $params);
    }
}

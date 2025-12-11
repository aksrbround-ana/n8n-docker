<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FrontendController extends Controller
{
    public $layout = false;
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $filePath = $filePath = \Yii::getAlias('@app/web/index.html');
        if (!is_file($filePath)) {
            throw new NotFoundHttpException('Frontend index.html file not found. Have you built the React application and placed it in ' . $filePath . '?');
        }
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'inline');
        $response->data = file_get_contents($filePath);
        return $response;
    }
}

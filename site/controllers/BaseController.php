<?php

namespace app\controllers;

use Yii;
use \yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function renderPage($data, $view = 'page')
    {
        $html = $this->render($view, $data);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'data' => $html,
        ];
        return $response;
    }

    protected function renderLogout($data = [])
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'logout',
            'code' => 403,
            'message' => 'Invalid or expired token.',
            'data' => $data,
        ];
        return $response;
    }
}

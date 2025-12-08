<?php

namespace app\controllers;

class TaskController extends BaseController
{
    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $data = [];
        return $this->renderPage($data);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}

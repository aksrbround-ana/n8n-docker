<?php

namespace app\controllers;
use app\models\Accountant;
class SettingsController extends BaseController
{
    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        $data = [
            'user' => $accountant,
        ];
        return $this->renderPage($data);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}

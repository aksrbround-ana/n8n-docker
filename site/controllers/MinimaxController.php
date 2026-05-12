<?php

namespace app\controllers;

use app\models\Accountant;

class MinimaxController extends BaseController
{
    public function actionSend()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
        } else {
            return $this->renderLogout();
        }
    }

    public function actionView()
    {
        return $this->render('view');
    }

}

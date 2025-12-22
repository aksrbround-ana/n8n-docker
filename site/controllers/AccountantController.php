<?php

namespace app\controllers;

use app\models\Accountant;
use app\models\Task;

class AccountantController extends BaseController
{
    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = [];
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionView($id = 0)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $veiwer = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($veiwer->isValid()) {
            $accountant = Accountant::findOne($id);
            if ($accountant) {
                $taskQuery = Task::find()->where(['accountant_id' => $accountant->id]);
                $tasks = $taskQuery->all();
                $data = [
                    'user' => $veiwer,
                    'accountant' => $accountant,
                    'tasks' => $tasks,
                    'id' => $id,
                ];
                return $this->renderPage($data, 'view');
            } else {
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionProfile()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $veiwer = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($veiwer->isValid()) {
            // $taskQuery = Task::find()->where(['accountant_id' => $accountant->id]);
            // $tasks = $taskQuery->all();
            $data = [
                'user' => $veiwer,
                // 'tasks' => $tasks,
            ];
            return $this->renderPage($data, 'view');
        } else {
            return $this->renderLogout();
        }
    }
}

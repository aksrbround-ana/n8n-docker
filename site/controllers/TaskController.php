<?php

namespace app\controllers;

use app\models\Accountant;

class TaskController extends BaseController
{

    public function getDataForPage($token)
    {
        $accountant = Accountant::findIdentityByAccessToken($token);
        $taskQuery = \app\models\Task::find();
        if (!$accountant->rule != 'ceo') {
            $taskQuery->andWhere(['accountant_id' => $accountant->id]);            
        }
        $tasks = $taskQuery->all();
        
        $data = [
            'user' => $accountant,
            'tasks' => $tasks,
        ];
        return $data;
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $data = $this->getDataForPage($token);
        return $this->renderPage($data);
    }

    public function actionView()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $data = $this->getDataForPage($token);
        return $this->renderPage($data, 'view');
    }

}

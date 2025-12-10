<?php

namespace app\controllers;

use app\models\Accountant;
use \app\models\Task;

class TaskController extends BaseController
{

    public function getDataForPage($accountant)
    {
        $taskQuery = Task::find();
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
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $data = $this->getDataForPage($accountant);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout([$accountant, $accountant->isValid()]);
        }
    }

    public function actionView()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $data = [
                'user' => $accountant,
                'task' => Task::findOne(['id' => $id]),
            ];
            return $this->renderPage($data, 'view');
        } else {
            return $this->renderLogout($accountant);
        }
    }
}

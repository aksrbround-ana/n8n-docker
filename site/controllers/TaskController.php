<?php

namespace app\controllers;

use yii\web\Response;
use app\models\Accountant;
use \app\models\Task;
use \app\models\TaskComment;
use app\controllers\BaseController;

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

    public function actionComment()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $commentText = $request->post('comment_text');
            $comment = new TaskComment();
            $comment->task_id = $id;
            $comment->accountant_id = $accountant->id;
            $comment->text = $commentText;
            $comment->save();
            $data = [
                'user' => $accountant,
                'task' => Task::findOne(['id' => $id]),
            ];
            return $this->renderPage($data, 'addComment');
        } else {
            return $this->renderLogout($accountant);
        }
    }

    public function actionFinish()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $task = Task::findOne(['id' => $id]);
            $task->status = Task::STATUS_DONE;
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            if ($task->save()) {
                $response->data = [
                    'status' => 'success',
                    'task' => Task::findOne(['id' => $id]),
                ];
            } else {
                $response->data = [
                    'status' => 'error',
                    // 'message' => implode("\n", $task->getErrors()),
                    'message' => $task->getErrors(),
                    'task' => $task,
                ];
            }
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }
}

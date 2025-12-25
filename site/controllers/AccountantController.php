<?php

namespace app\controllers;

use yii\web\Response;
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
                if (empty($tasks)) {
                    $tasks = [];
                }
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
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = [
                'user' => $accountant,
            ];
            return $this->renderPage($data, 'profile');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionEdit()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = [
                'user' => $accountant,
            ];
            return $this->renderPage($data, 'edit');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionSave($id = 0)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $errors = [];
            if ($accountant->id == $id) {
                $email = $request->post('email');
                $oldPassword = $request->post('old_password');
                $newPassword = $request->post('new_password');
                $confirmPassword = $request->post('confirm_password');

                if ($email) {
                    $accountant->email = $email;
                }

                if ($oldPassword && $newPassword && $confirmPassword) {
                    if ($accountant->getPassword() == \app\services\AuthService::encodePassword($oldPassword)) {
                        if ($newPassword == $confirmPassword) {
                            $accountant->setPassword($newPassword);
                        } else {
                            $errors[] = 'Password fields are incorrect';
                        }
                    }
                }
                $accountant->save();
            } else {
                $errors[] = 'You can edit only your profile';
            }
            $data = [
                'status' => empty($errors) ? 'success' : 'error',
                'errors' => $errors,
                'user' => $accountant,
            ];
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = $data;
            return $response;
        } else {
            return $this->renderLogout();
        }
    }
}

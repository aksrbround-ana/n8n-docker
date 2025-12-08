<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use \app\models\Accountant;
use yii\web\Controller;
use app\services\AuthService;

class AuthController extends Controller
{

    public function actionLogin()
    {
        $request = \Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');
        $accountant = Accountant::findOne(['email' => $username, 'password' => AuthService::encodePassword($password)]);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        if ($accountant) {
            $accountant->generateAccessToken();
            $accountant->update_at = date('Y-m-d H:i:s');
            $accountant->save();
            $token = $accountant->token;
            $session = Yii::$app->getSession();
            $session->set($token, $token);
            Yii::$app->user->login($accountant);
            $response->data =
                [
                    'status' => 'success',
                    'token' => $token,
                    'user' => $accountant,
                    'code' => 200,
                ];
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => AuthService::ACCESS_TOKEN_NAME,
                'value' => $token,
                'httpOnly' => true,
            ]));
        } else {
            $response->data = [
                'status' => 'error',
                'message' => 'Invalid credentials',
                'code' => 401,
                'email' => $username,
                'hash' => AuthService::encodePassword($password),
                'user' => $accountant,
            ];
        }
        return $response;
    }

    public function actionCheck()
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = null;
        if ($token !== null) {
            $accountant = \app\models\Accountant::findOne(['token' => $token]);
            // $session = Yii::$app->getSession();
            // $sToken = $session->get($token, null);
        }
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'token' => $token,
            'accountant' => $accountant,
            // 'session' => $sToken,
            // 'session_valid' => $token == $sToken,
        ];
        return $response;
    }

    public function actionToken()
    {
        $request = \Yii::$app->request;
        $username = $request->post('username');
        $accountant = Accountant::findOne(['email' => $username,]);
        $token = $accountant->token;
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'token' => $token,
        ];
        return $response;
    }

    public function actionLogout()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findOne(['token' => $token]);
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        if ($accountant) {
            $accountant->token = '';
            $accountant->update_at = date('Y-m-d H:i:s');
            $accountant->save();
            $session = Yii::$app->getSession();
            $session->remove($token);
            $response->data = [
                'status' => 'success',
                'message' =>
                'Logged out successfully',
                'code' => 200,
            ];
            Yii::$app->response->cookies->remove(AuthService::ACCESS_TOKEN_NAME);
        } else {
            $response->data = ['status' => 'error', 'message' => 'Invalid token', 'code' => 401];
        }
        return $response;
    }
}

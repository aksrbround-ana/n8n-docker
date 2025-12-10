<?php

namespace app\controllers;

use app\models\Accountant;
use app\models\Document;

class DocumentController extends BaseController
{

    public function getDataForPage($accountant)
    {
        $docs = Document::find()->all();
        $data = [
            'user' => $accountant,
            'documents' => $docs,
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
            return $this->renderLogout();
        }
    }

    public function actionView()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $docId = $request->post('id');
            $doc = Document::findOne(['id' => $docId]);
            $data = [
                'user' => $accountant,
                'documents' => $doc,
            ];
            return $this->renderPage($data, 'view');
        } else {
            return $this->renderLogout();
        }
    }
}

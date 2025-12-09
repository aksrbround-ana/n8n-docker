<?php

namespace app\controllers;
use app\models\Accountant;
use app\models\Document;

class DocumentController extends BaseController
{

    public function getDataForPage($token)
    {
        $accountant = Accountant::findIdentityByAccessToken($token);
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
        $data = $this->getDataForPage($token);
        return $this->renderPage($data);
    }

    public function actionView()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $docId = $request->post('id');
        $doc = Document::findOne(['id' => $docId]);
        $data = [
            'user' => Accountant::findIdentityByAccessToken($token),
            'documents' => $doc, 
        ];
        return $this->renderPage($data, 'view');
    }

}

<?php

namespace app\controllers;

use yii\web\Response;
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
                'document' => $doc,
            ];
            return $this->renderPage($data, 'view');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionFile($id)
    {
        $this->layout = false;
        $response = \Yii::$app->response;
        $document = Document::findOne(['id' => $id]);
        if ($document) {
            $response->format = Response::FORMAT_RAW;
            $response->headers->add('Content-Type', $document->mimetype);
            $response->headers->add('Content-Disposition', "inline; filename=\"{$document->filename}\"");
            $response->content = stream_get_contents($document->content);
            return $response;
        } else {
            throw new \yii\web\NotFoundHttpException('Document not found');
        }
    }
}

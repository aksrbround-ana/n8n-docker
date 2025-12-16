<?php

namespace app\controllers;

use yii\web\Response;
use yii\db\Query;
use app\models\Accountant;
use app\models\Document;
use app\models\DocumentStep;
use app\components\DocViewActivityWidget;
use app\models\DocumentComment;

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

    public function actionUpload()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $data = [
                'user' => $accountant,
            ];
            return $this->renderPage($data, 'upload');
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

    public function actionChangeStatus()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $response = \Yii::$app->response;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant->isValid()) {
            $docId = $request->post('id');
            $newStatus = $request->post('status');
            $newStatus = DocumentStep::$steps[$newStatus] ?? $newStatus;
            $document = Document::findOne(['id' => $docId]);
            if ($document) {
                $document->status = $newStatus;
                $query = new Query();
                $res = $query->createCommand()->update(Document::tableName(), ['status' => $newStatus], ['id' => $docId])->execute();
                if ($res) {
                    $document->addActivity($accountant->id, $newStatus);
                    $response->format = Response::FORMAT_JSON;
                    $response->data =
                        [
                            'status' => 'success',
                            'data' => $this->renderPartial('_doc_status_block', [
                                'user' => $accountant,
                                'document' => $document,
                            ]),
                            'activity' => DocViewActivityWidget::widget([
                                'user' => $accountant,
                                'document' => $document,
                            ]),
                        ];
                    return $response;
                } else {
                    $response->format = Response::FORMAT_JSON;
                    $response->data =
                        [
                            'status' => 'error',
                            'errors' => $document->getErrors(),
                        ];
                    return $response;
                }
            } else {
                throw new \yii\web\NotFoundHttpException('Document not found');
            }
        } else {
            return $this->renderLogout();
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
            $comment = new DocumentComment();
            $comment->document_id = $id;
            $comment->accountant_id = $accountant->id;
            $comment->text = $commentText;
            $comment->save();
            $data = [
                'user' => $accountant,
                'document' => Document::findOne(['id' => $id]),
            ];
            return
            $page = $this->renderPage($data, 'addComment');
            // $response = \Yii::$app->response;
            // $response->format = Response::FORMAT_JSON;
            // $response->data =
            return
                [
                    'status' => 'success',
                    'data' => $page
                ];
            return $response;
        } else {
            return $this->renderLogout($accountant);
        }
    }
}

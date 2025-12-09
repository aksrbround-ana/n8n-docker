<?php

namespace app\controllers;

use Yii;
use \yii\db\Query;
use yii\web\Response;
use app\controllers\BaseController;
use app\models\Company;
use app\models\Accountant;
use app\models\CompanyActivities;
use app\models\Customer;
use app\components\CompanyNotesWidget;
use app\models\Task;

class CompanyController extends BaseController
{
    public static $statuses = [
        'statusActive',
        'statusOnboarding',
        'statusPaused',
        'statusInactive',
    ];

    protected function getDataForPage($token)
    {
        $accountant = Accountant::findIdentityByAccessToken($token);
        $companiesQuery = (new Query())
            ->select([
                'c.id AS company_id',
                'c.name AS company_name',
                'ct.name AS company_type',
                'ca.name AS company_activity',
                'c.is_pdv',
                'c.pib',
                'c.status AS company_status',
                'a.id AS accountant_id',
                'a.firstname',
                'a.lastname'
            ])
            ->distinct()
            ->from(['c' => 'company'])
            ->leftJoin(['ct' => 'company_type'], 'ct.id = c.type_id')
            ->leftJoin(['ca' => 'company_activities'], 'ca.id = c.activity_id')
            ->leftJoin(['t' => 'task'], 't.company_id = c.id')
            ->leftJoin(['a' => 'accountant'], 'a.id = t.accountant_id');
        $companies = $companiesQuery->all();
        foreach ($companies as &$company) {
            $id = $company['company_id'];
            $openTasks = (new Query())
                ->select('count(*) as tasks')
                ->from('task')
                ->where('id = ' . $id)
                ->andWhere('status = \'inProgress\'')
                ->one()['tasks'];
            $company['openTasks'] = $openTasks;
            $overdueTasks = (new Query())
                ->select('count(*) as tasks')
                ->from('task')
                ->where('id = ' . $id)
                ->andWhere('status != \'done\'')
                ->andWhere('due_date < :now', [':now' => date('Y-m-d')])
                ->one()['tasks'];
            $company['overdueTasks'] = $overdueTasks;
        }
        $data = [
            'user' => $accountant,
            'companies' => $companies,
        ];
        return $data;
    }

    protected function getDataForProfile($token, $id)
    {
        $accountant = Accountant::findIdentityByAccessToken($token);
        $company = Company::findOne(['id' => $id]);
        $customers = Customer::findAll(['company_id' => $id]);
        $activity = CompanyActivities::findOne(['id' => $company->activity_id]);
        $taskCount = Task::find(['company_id' => $company->id])->count();
        $tasks = Task::find(['company_id' => $company->id])->all();
        $tasksOverdue = Task::find(['company_id' => $company->id, ['<', 'due_date', date('Y-m-d')]])->count();
        $data = [
            'user' => $accountant,
            'company' => $company,
            'activity' => $activity,
            'customers' => $customers,
            'taskCount' => $taskCount,
            'tasks' => $tasks,
            'tasksOverdue' => $tasksOverdue,
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

    public function actionProfile()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $companyId = $request->post('id');
        $data = $this->getDataForProfile($token, $companyId);
        return $this->renderPage($data, 'profile');
    }

    public function actionAddNote()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $companyId = $request->post('company_id');
        $company = Company::findOne(['id' => $companyId]);
        $noteText = $request->post('note_text');
        $accountant = Accountant::findIdentityByAccessToken($token);
        if ($accountant && $companyId && $noteText) {
            $note = new \app\models\CompanyNotes();
            $note->company_id = $companyId;
            $note->accountant_id = $accountant->id;
            $note->note = $noteText;
            $note->status = 'active';
            if ($note->save()) {
                $out = [
                    'status' => 'success',
                    'code' => 200,
                    'data' => CompanyNotesWidget::widget(['user' => $accountant, 'company' => $company]),
                ];
            } else {
                $errors = $note->getErrors();
                $out = [
                    'success' => 'error',
                    'message' => 'Failed to save note: ' . implode('; ', array_map(function ($v) {
                        return implode(', ', $v);
                    }, $errors)),
                ];
            }
        } else {
            $out = [
                'success' => 'error',
                'message' => 'Invalid input data.',
                'input' => [
                    'token' => $token,
                    'companyId' => $companyId,
                    'noteText' => $noteText,
                ],
            ];
        }
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = $out;
        return $response;
    }

    public function actionIndex()
    {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = ['method' => __METHOD__];
        return $response;
        // return $this->render('index');
    }

    public function actionView()
    {
        $json = $this->getJson();
        $id = $json['id'] ?? null;
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = ['method' => __METHOD__, 'id' => $id];
        return $response;
    }

    public function actionList()
    {
        $json = $this->getJson();
        // $responsibleId = $json['responsibleId'] ?? null;
        $status = $json['status'] ?? null;
        $sort = $json['sort'] ?? null;
        $query = new Query();
        $query->select('*')->from('company');
        // if ($responsibleId !== null) {
        //     $query->andWhere(['responsible_id' => $responsibleId]);
        // }
        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }
        if ($sort !== null) {
            $query->orderBy($sort);
        }
        $companies = $query->all();
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = $companies;
        // $response->data = [
        //     'method' => __METHOD__,
        //     'companies' => $companies,
        // ];
        return $response;
    }
}

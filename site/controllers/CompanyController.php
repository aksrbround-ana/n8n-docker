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
use app\models\TaxCalendar;
use app\models\Reminder;
use app\models\Task;

class CompanyController extends BaseController
{
    public static $statuses = [
        'statusActive',
        'statusOnboarding',
        'statusPaused',
        'statusInactive',
    ];

    protected function getDataForPage($accountant, $status = null)
    {
        $companiesQuery = (new Query())
            ->select([
                'c.id AS company_id',
                'c.name AS company_name',
                'ct.name AS company_type',
                'ca.name AS company_activity',
                'c.is_pdv',
                'c.pib',
                'c.status AS company_status'
            ])
            ->distinct()
            ->from(['c' => 'company'])
            ->leftJoin(['ct' => 'company_type'], 'ct.id = c.type_id')
            ->leftJoin(['ca' => 'company_activities'], 'ca.id = c.activity_id')
            // ->leftJoin(['t' => 'task'], 't.company_id = c.id')
            // ->leftJoin(['a' => 'accountant'], 'a.id = t.accountant_id')
        ;
        $companies = $companiesQuery->all();
        foreach ($companies as &$company) {
            $openTasks = Task::find()
                ->where(['company_id' => $company['company_id']])
                ->andWhere(['!=', 'status', '\'done\''])
                ->count();
            $company['openTasks'] = $openTasks;
            $overdueTasks = Task::find()
                ->where(['company_id' => $company['company_id']])
                ->andWhere(['!=', 'status', 'done'])
                ->andWhere(['<', 'due_date', date('Y-m-d')])
                ->count();

            $company['overdueTasks'] = $overdueTasks;
        }
        $data = [
            'user' => $accountant,
            'companies' => $companies,
            'back' => $status !== null,
        ];
        return $data;
    }

    public function actionPage($status = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = $this->getDataForPage($accountant, $status);
            return $this->renderPage($data);
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
            $id = $request->post('id');
            $company = Company::findOne(['id' => $id]);
            $customers = Customer::findAll(['company_id' => $id]);
            $activity = CompanyActivities::findOne(['id' => $company->activity_id]);
            $taskQuery = Task::find()
                ->where(['company_id' => $company->id]);
            $taskCount = $taskQuery->count();
            $tasks = $taskQuery
                ->orderBy('due_date ASC')
                ->all();
            $tasksOverdue = $taskQuery
                ->andWhere(['!=', 'status', 'done'])
                ->andWhere(['<', 'due_date', date('Y-m-d')])
                ->count();
            $data = [
                'user' => $accountant,
                'company' => $company,
                'activity' => $activity,
                'customers' => $customers,
                'taskCount' => $taskCount,
                'tasks' => $tasks,
                'tasksOverdue' => $tasksOverdue,
            ];

            return $this->renderPage($data, 'profile');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionAddNote()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $companyId = $request->post('company_id');
        $company = Company::findOne(['id' => $companyId]);
        $noteText = $request->post('note_text');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $accountant = $accountant;
            if ($companyId && $noteText) {
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
        } else {
            $this->renderLogout();
        }
    }

    public function actionListToCalendar()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $eventId = $request->post('id');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $rows = (new Query())
                ->select(['c.id', 'c.name', 'count(r.id) AS count'])
                ->from(['c' => 'company'])
                ->leftJoin(['r' => Reminder::tableName()], 'r.company_id = c.id AND r.type = \'calendar\' AND r.template_id = ' . intval($eventId))
                ->orderBy('c.name ASC')
                ->groupBy(['c.id', 'c.name']);

            $companies = $rows->all();
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'list' => $companies
                ]
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionUpdateCalendarReminders()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('reminder_id');
            $checkedCompanies = $request->post('checked_companies');
            $uncheckedCompanies = $request->post('uncheked_companies');
            // Удаляем существующие напоминания для этой компании и события
            if (!empty($uncheckedCompanies)) {
                Reminder::deleteAll([
                    'type' => 'calendar',
                    'template_id' => $reminderId,
                    'company_id' => $uncheckedCompanies,
                ]);
            }
            // Добавляем новые напоминания
            if (!empty($checkedCompanies)) {
                for ($i = 0; $i < count($checkedCompanies); $i++) {
                    $ps = TaxCalendar::findOne(['id' => $reminderId]);
                    $reminder = new Reminder();
                    $reminder->company_id = $checkedCompanies[$i];
                    $reminder->type = 'calendar';
                    $reminder->template_id = $reminderId;
                    $reminder->created_at = date('Y-m-d H:i:s');
                    $reminder->updated_at = date('Y-m-d H:i:s');
                    $reminder->send_date = date('Y-m-d H:i:s', strtotime($ps->notification_date));
                    // $reminder->message = $ps->activity_text;
                    $reminder->save();
                }
            }
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Reminders updated successfully.',
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionDeleteCalendarReminder()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('id');
            Reminder::deleteAll(['type' => 'calendar', 'template_id' => $reminderId]);
            TaxCalendar::deleteAll(['id' => $reminderId]);
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Calendar reminder deleted successfully.',
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionUpdateReminderDetails()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $reminderId = $request->post('id');
            $text = $request->post('text');
            $tc = TaxCalendar::findOne(['id' => $reminderId]);
            if ($tc) {
                $tc->activity_text = $text;
                $tc->save();
            }
            $response = \Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Reminder details updated successfully.',
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
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
}

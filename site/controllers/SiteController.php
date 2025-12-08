<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ErrorAction;
use app\services\AuthService;
use app\models\Accountant;
use app\models\Company;
use app\models\Customer;
use app\models\Task;
use app\models\Document;
use yii\db\Query;

class SiteController extends BaseController
{

    protected function putHtml($label = '')
    {
        $filePath = $filePath = \Yii::getAlias('@app/web/index.html');
        if (!is_file($filePath)) {
            throw new NotFoundHttpException('Frontend index.html file not found. Have you built the React application and placed it in ' . $filePath . '?');
        }
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'inline');
        $response->data = $label . file_get_contents($filePath);
        return $response;
    }


    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'main';
        $token = Yii::$app->response->cookies->has(AuthService::ACCESS_TOKEN_NAME) ? Yii::$app->response->cookies->get(AuthService::ACCESS_TOKEN_NAME) : null;
        return $this->render('empty');
    }

    protected function getDataForPage($token)
    {
        $accountant = \app\models\Accountant::findOne(['token' => $token]);

        $accountantQuery = (new Query())
            ->select(['c.id', 'c.firstname', 'c.lastname', 'c.rule', 'c.lang', 'c.email', 'COUNT(t.id) AS tasks'])
            ->from(['c' => Accountant::tableName()])
            ->leftJoin(['t' => Task::tableName()], 't.accountant_id = c.id')
            ->groupBy(['c.id', 'c.firstname', 'c.lastname', 'c.rule', 'c.lang', 'c.email'])
            ->orderBy(['tasks' => SORT_DESC, 'c.lastname' => SORT_ASC, 'c.firstname' => SORT_ASC]);
        $accountantsRaw = $accountantQuery->all();
        $accountants = [];
        foreach ($accountantsRaw as $accountantOne) {
            $accountants[$accountantOne['id']] = $accountantOne;
        }

        $overdueTasksQuery = (new Query())
            ->select(['a.id', 'COUNT(t.id) AS "overdue_tasks"'])
            ->from(['a' => 'accountant'])
            ->leftJoin(['t' => Task::tableName()], 't.accountant_id = a.id AND t.status != \'done\' AND t.due_date < CURRENT_DATE')
            ->groupBy('a.id')
            ->orderBy(['a.id' => SORT_ASC]);
        if ($accountant->rule !== 'ceo') {
            $overdueTasksQuery->andWhere('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $overdueTasks = $overdueTasksQuery->all();
        foreach ($overdueTasks as $overdueTask) {
            $accountants[$overdueTask['id']]['overdueTasks'] = $overdueTask['overdue_tasks'];
        }

        $upcomingDeadlinesQuery = (new Query())
            ->select([
                't.id as task_id',
                'c.id as company_id',
                'c.name as company_name',
                'a.id as accountant_id',
                '(a.firstname || \' \' || a.lastname) as accountant_name',
                't.category',
                't.request',
                't.status',
                't.due_date',
                't.priority',
                'DATE_PART(\'day\', due_date - CURRENT_DATE) AS time_left'

            ])
            ->from(['t' => 'task'])
            ->rightJoin(['c' => 'company'], 'c.id=t.company_id')
            ->leftJoin(['a' => 'accountant'], 'a.id = t.accountant_id')
            ->where(['!=', 't.status', 'done'])
            ->orderBy(['due_date' => SORT_ASC])
            ->limit(5);
        if ($accountant->rule !== 'ceo') {
            $upcomingDeadlinesQuery->andWhere('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $upcomingDeadlines = $upcomingDeadlinesQuery->all();

        // $customers = Customer::find()->count();
        $companies = Company::find()->count();
        $activeTasksQuery = Task::find()->where(['status' => 'active']);
        if ($accountant->rule !== 'ceo') {
            $activeTasksQuery->andWhere('accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $activeTasks = $activeTasksQuery->count();

        $overdueTasksQuery = Task::find()->where(['<', 'due_date', date('Y-m-d')]);
        if ($accountant->rule !== 'ceo') {
            $overdueTasksQuery->andWhere('accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
        }
        $overdueTasks = $overdueTasksQuery->count();
        $docsToCheck = Document::find()->where(['status' => 'new'])->count();
        $data = [
            'user' => $accountant,
            'data' => [
                'accountants' => $accountants,
                'clents' => $companies,
                'activeTasks' => $activeTasks,
                'overdueTasks' => $overdueTasks,
                'upcomingDeadlines' => $upcomingDeadlines,
                'docsToCheck' => $docsToCheck,
            ]
        ];
        return $data;
    }

    public function actionLoad()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = null;
        if ($token !== null) {
            $accountant = \app\models\Accountant::findOne(['token' => $token]);
        }
        if (!$token || !$accountant) {
            $data = $this->render('login');
        } else {
            $dataForRendering = $this->getDataForPage($token);
            $data = $this->render('index', $dataForRendering);
        }
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = [
            'status' => 'success',
            'code' => 200,
            'data' => $data,
        ];
        return $response;
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $data = $this->getDataForPage($token);
        return $this->renderPage($data);
    }

    public function actionError()
    {
        $ex = Yii::$app->getErrorHandler()->exception;
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->data = ['status' => 'error', 'message' => $ex->getMessage(), 'exception' => get_class($ex)];
        return $response;
    }
}

<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use yii\web\ErrorAction;
use app\services\AuthService;
use app\models\Accountant;
use app\models\Company;
use app\models\Task;
use app\models\Document;
use app\models\TaskActivity;

class SiteController extends BaseController
{

    public $user;
    public $mainMenu = [
        'dashboard' => [
             'url' => '/site/page',
             'picture' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard h-5 w-5 flex-shrink-0">
<rect width="7" height="9" x="3" y="3" rx="1"></rect>
<rect width="7" height="5" x="14" y="3" rx="1"></rect>
<rect width="7" height="9" x="14" y="12" rx="1"></rect>
<rect width="7" height="5" x="3" y="16" rx="1"></rect>
</svg>',
            ],
        'companies' => [
            'url' => '/company/page',
            'picture' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 h-5 w-5 flex-shrink-0">
<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
<path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
<path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
<path d="M10 6h4"></path>
<path d="M10 10h4"></path>
<path d="M10 14h4"></path>
<path d="M10 18h4"></path>
</svg>',
        ],
        'tasks' => [
            'url' => '/task/page',
            'picture' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-todo h-5 w-5 flex-shrink-0">
<rect x="3" y="5" width="6" height="6" rx="1"></rect>
<path d="m3 17 2 2 4-4"></path>
<path d="M13 6h8"></path>
<path d="M13 12h8"></path>
<path d="M13 18h8"></path>
</svg>',
        ],
        'documents' => [
            'url' => '/document/page',
            'picture' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 flex-shrink-0">
<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
<path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
<path d="M10 9H8"></path>
<path d="M16 13H8"></path>
<path d="M16 17H8"></path>
</svg>',
        ],
        'settings' => [
            'url' => '/settings/page',
            'picture' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings h-5 w-5 flex-shrink-0">
<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
<circle cx="12" cy="12" r="3">
</circle>
</svg>',
        ],
    ];

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
        $cookies = Yii::$app->request->cookies;

        if (($cookie = $cookies->get('token')) !== null) {
            $token = $cookie->value;
        } else {
            $token = 'token_not_found';
        }

        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        Yii::$app->view->params['accountant'] = $accountant;
        $data = [
            'user' => $accountant,
        ];
        return $this->render('empty',$data);
    }

    protected function getDataForPage($accountant)
    {
        $permissions = AuthService::getPermissions($accountant);
        $viewAccountants = array_key_exists('viewAccountants', $permissions);
        if ($viewAccountants) {
            $accountantQuery = (new Query())
                ->select(['c.id', 'c.firstname', 'c.lastname', 'c.rule', 'c.lang', 'c.email', 'COUNT(t.id) AS tasks'])
                ->from(['c' => Accountant::tableName()])
                ->leftJoin(['t' => Task::tableName()], 't.accountant_id = c.id')
                ->where(['!=', 'c.rule', 'bot'])
                ->andWhere(['!=', 'c.rule', 'admin'])
                ->andWhere(['!=', 'c.rule', 'ceo'])
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
                ->where(['!=', 'a.rule', 'bot'])
                ->andWhere(['!=', 'a.rule', 'admin'])
                ->andWhere(['!=', 'a.rule', 'ceo'])
                ->groupBy('a.id')
                ->orderBy(['a.id' => SORT_ASC]);
            if ($accountant->rule !== 'ceo') {
                $overdueTasksQuery->andWhere('t.accountant_id = :accountant_id', ['accountant_id' => $accountant->id]);
            }
            $overdueTasks = $overdueTasksQuery->all();
            foreach ($overdueTasks as $overdueTask) {
                $accountants[$overdueTask['id']]['overdueTasks'] = $overdueTask['overdue_tasks'];
            }
            $recentActivity = [];
        } else {
            $accountants = [];
            $recentActivity = TaskActivity::find()
                ->where('accountant_id = :accountant_id', ['accountant_id' => $accountant->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(10)
                ->all();
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
        $activeTasksQuery = Task::find()->where(['status' => 'inProgress']);
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
                'activities' => $recentActivity,
                'viewAccountants' => $viewAccountants,                
            ]
        ];
        return $data;
    }

    public function actionLoad()
    {
        $this->layout = false;
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if (!$accountant->isValid()) {
            $data = $this->render('login');
        } else {
            $dataForRendering = $this->getDataForPage($accountant);
            $dataForRendering['menu'] = $this->mainMenu;
            $data = $this->render('index', $dataForRendering);
        }
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
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $data = $this->getDataForPage($accountant);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
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

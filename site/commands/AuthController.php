<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\rbac\DbManager;

class AuthController extends Controller
{
    private $rules = [
        'accountant',
        'admin',
        'ceo'
    ];
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        echo get_class($auth)."\n\n";
        foreach ($this->rules as $rule) {
            $permissions = $auth->getPermissionsByRole($rule);
            echo $rule.":\n\n";
            $names = array_keys($permissions);
            sort($names);
            foreach ($names as $name) {
                echo $name . "\n";
            }
            echo "\n\n";
        }

        // $ceo = $auth->getRole('ceo');
        // var_dump($ceo);
        // $viewDashboard = $auth->getRole('viewDashboard');
        // var_dump($viewDashboard);
        // $viewDashboard = $auth->getRule('viewDashboard');
        // var_dump($viewDashboard);

        return ExitCode::OK;
    }
}

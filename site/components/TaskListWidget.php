<?php

namespace app\components;

use yii\base\Widget;
use app\models\Task;

class TaskListWidget extends Widget
{
    public $user;
    public $company;

    public function run()
    {
        $tasks = Task::find(['company_id' => $this->company->id])->all();

        return $this->render('tasklist', [
            'user' => $this->user,
            'company' => $this->company,
            'tasks' => $tasks,
        ]);
    }
}

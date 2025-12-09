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
        if ($this->company) {
            $tasksQuery = Task::find(['company_id' => $this->company->id]);
        } else {
            $tasksQuery = Task::find();
        }
        if ($this->user) {
            if ($this->user->rule != 'ceo') {
                $tasksQuery->andWhere(['accountant_id' => $this->user->id]);
            }
        }
        $tasks = $tasksQuery->all();

        return $this->render('tasklist', [
            'user' => $this->user,
            'company' => $this->company,
            'tasks' => $tasks,
        ]);
    }
}

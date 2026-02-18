<?php

namespace app\components;

use app\models\TaxCalendar;
use yii\base\Widget;

class RemindersRegTableWidget extends Widget
{
    public $user;
    public $reminders;

    public function run()
    {
        if (!$this->reminders) {
            $this->reminders = [];
        }
        return $this->render('regreminderstable', [
            'user' => $this->user,
            'reminders' => $this->reminders,
        ]);
    }
}

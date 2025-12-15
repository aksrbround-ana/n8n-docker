<?php

namespace app\components;

use app\models\PoreskiKalendar;
use yii\base\Widget;

class SettingsCalendarWidget extends Widget
{
    public $user;

    public function run()
    {
        $items = PoreskiKalendar::find()->orderBy(["input_date" => SORT_ASC, 'activity_type' => SORT_ASC])->all();
        return $this->render('settingscalendar', [
            'user' => $this->user,
            'items' => $items,
        ]);
    }
}

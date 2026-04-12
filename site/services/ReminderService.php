<?php

namespace app\services;

use yii\db\Query;
use app\models\Company;
use app\models\ReminderSchedule;

class ReminderService
{
    public static function doneCount()
    {
        $today = date('Y-m-d');
        $remindersQuery = ReminderSchedule::find()
            ->where(['status' => 'pending'])
            ->andWhere(['done_by_user' => 1])
            ->andWhere(
                [
                    'OR',
                    'reminder_1_date=:today',
                    'reminder_2_date=:today',
                ],
                ['today' => $today]
            );
        return $remindersQuery->count();
    }

    public static function doneList()
    {
        $today = date('Y-m-d');
        $remindersQuery = (new Query())
            ->select([
                'rs.id',
                'rs.deadline_date',
                'rs.type',
                'rs.message',
                'c.name',
            ])
            ->from(['rs' => ReminderSchedule::tableName()])
            ->innerJoin(['c' => Company::tableName()], 'c.id=rs.company_id')
            ->where(['rs.status' => 'pending'])
            ->andWhere(['rs.done_by_user' => 1])
            ->andWhere(
                [
                    'OR',
                    'rs.reminder_1_date=:today',
                    'rs.reminder_2_date=:today',
                ],
                ['today' => $today]
            );
        return $remindersQuery->all();
    }

    public static function done($id)
    {
        $reminder = ReminderSchedule::findOne($id);
        $reminder->status = 'done';
        return $reminder->save();
    }
}

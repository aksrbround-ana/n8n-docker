<?php

namespace app\controllers;

use yii\db\Query;
use app\models\Accountant;
use app\models\TaxCalendar;
use app\components\SettingsCalendarWidget;
use app\components\SettingsCalendarBodyWidget;
use app\models\Company;
use app\models\ReminderRegular;
use app\models\ReminderSchedule;
use yii\db\QueryBuilder;

class ReminderController extends BaseController
{

    public function actionCancel()
    {
        return $this->render('cancel');
    }

    private function getDataToPage(Accountant $accountant, $month)
    {
        $monthsQuery = (new Query())
            ->select(['month' => 'EXTRACT(MONTH FROM input_date)', 'year' => 'EXTRACT(YEAR FROM input_date)'])
            ->distinct()
            ->from(TaxCalendar::tableName())
            ->orderBy(['year' => SORT_DESC, 'month' => SORT_DESC]);
        $monthsRaw = $monthsQuery->all();
        $monthList = [];
        foreach ($monthsRaw as $value) {
            if ($value['month'] < 10) {
                $value['month'] = '0' . $value['month'];
            }
            $monthList[] = $value['year'] . '-' . $value['month'];
        }
        if (!in_array($month, $monthList) && count($monthList) > 0) {
            $month = $monthList[0];
        }
        $firstDay = date('Y-m-01', strtotime($month));
        $lastDay = date('Y-m-t', strtotime($month));
        $taxCalendar = TaxCalendar::find()
            ->where(['between', 'input_date', $firstDay, $lastDay])
            ->orderBy(["input_date" => SORT_ASC, 'activity_type' => SORT_ASC])
            ->all();
        $data = [
            'user' => $accountant,
            'taxCalendar' => $taxCalendar,
            'month' => date('m', strtotime($month)),
            'year' => date('Y', strtotime($month)),
            'monthList' => $monthList,
        ];
        return $data;
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $month = date('Y-m');
            $data = $this->getDataToPage($accountant, $month);
            return $this->renderPage($data);
        } else {
            return $this->renderLogout();
        }
    }

    public function actionReg()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');
            $reminders = (new Query)
                ->select(['r.*'])
                ->from(['r' => ReminderRegular::tableName()])
                ->orderBy('r.deadline_day')
                ->all();
            $data = [
                'user' => $accountant,
                'reminders' => $reminders,
                'debug' => [
                    $firstDay,
                    $lastDay,
                ],
            ];
            return $this->renderPage($data, 'reg');
        } else {
            return $this->renderLogout();
        }
    }

    public function actionRegReminderCreate() {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'message' => 'Reminder created successfully',
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionCancelRegular()
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $id = $request->post('id');
            $reminder = ReminderSchedule::findOne($id);
            if ($reminder) {
                $r = (new Query())->createCommand()->update(ReminderSchedule::tableName(), ['status' => 'cancelled'], ['id' => $id])->execute();
                // $reminder->status = 'cancelled';
                // $reminder->save();
                $response = \Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = [
                    'status' => 'success',
                    'r' => $r,
                ];
                return $response;
            } else {
                $response = \Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = [
                    'status' => 'error',
                    'message' => 'Reminder not found',
                ];
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }

    public function actionTaxCalendar($month = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            if (!$month) {
                $month = date('Y-m');
            }
            $data = $this->getDataToPage($accountant, $month);
            $html = SettingsCalendarWidget::widget($data);
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'data' => $html,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionTaxCalendarTable($month = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            if (!$month) {
                $month = date('Y-m');
            }
            $data = $this->getDataToPage($accountant, $month);
            $html = SettingsCalendarBodyWidget::widget([
                'taxCalendar' => $data['taxCalendar'],
                'user' => $accountant,
            ]);
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'status' => 'success',
                'data' => $html,
            ];
            return $response;
        } else {
            return $this->renderLogout();
        }
    }

    public function actionTaxCalendarMonthLoad()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            list($year, $month) = explode('-', $request->post('month'));
            $data = [
                'year' => $year,
                'month' => $month,
            ];
            $out = $this->makeN8nWebhookCall('tax-calendar-month-load', $data);
            if ($out && isset($out['status']) && $out['status'] == 'success' && $out['code'] == 200) {
                $response->data = [
                    'status' => 'success',
                    'data' => $out,
                ];
                return $response;
            } else {
                $response->data = $out;
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }
}

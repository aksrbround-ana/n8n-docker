<?php

namespace app\controllers;

use app\components\SettingsCalendarBodyWidget;
use app\models\Accountant;
use app\models\TaxCalendar;
use yii\db\Query;

class SettingsController extends BaseController
{
    public function actionPage($month = null)
    {
        $this->layout = false;
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            if (!$month) {
                $month = date('Y-m');
            }
            $firstDay = date('Y-m-01', strtotime($month));
            $lastDay = date('Y-m-t', strtotime($month));
            $taxCalendar = TaxCalendar::find()
                ->where(['between', 'input_date', $firstDay, $lastDay])
                ->orderBy(["input_date" => SORT_ASC, 'activity_type' => SORT_ASC])
                ->all();
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
            $data = [
                'user' => $accountant,
                'taxCalendar' => $taxCalendar,
                'month' => date('m', strtotime($month)),
                'year' => date('Y', strtotime($month)),
                'monthList' => $monthList,
            ];
            return $this->renderPage($data);
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
            $firstDay = date('Y-m-01', strtotime($month));
            $lastDay = date('Y-m-t', strtotime($month));
            $taxCalendar = TaxCalendar::find()
                ->where(['between', 'input_date', $firstDay, $lastDay])
                ->orderBy(["input_date" => SORT_ASC, 'activity_type' => SORT_ASC])
                ->all();
            $data = [
                'user' => $accountant,
                'taxCalendar' => $taxCalendar,
            ];
            $html = SettingsCalendarBodyWidget::widget($data);
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

    public function actionTaxCalendarPageParse()
    {
        $request = \Yii::$app->request;
        $token = $request->post('token');
        $accountant = Accountant::findIdentityByAccessToken(['token' => $token]);
        if ($accountant->isValid()) {
            $page = $request->post('page');
            $data = [
                'page' => $page,
            ];
            $out = $this->makeN8nWebhookCall('tax-calendar-page', $data);
            if ($out && isset($out['status']) && $out['status'] == 'success') {
                $html = SettingsCalendarBodyWidget::widget($data);
                $response = \Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = [
                    'status' => 'success',
                    'data' => $html,
                ];
                return $response;
            }
        } else {
            return $this->renderLogout();
        }
    }
}

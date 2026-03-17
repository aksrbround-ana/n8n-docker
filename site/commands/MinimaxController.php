<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\components\MinimaxComponent;
use app\models\Company;

class MinimaxController extends Controller
{

    public function actionSync()
    {
        /** @var MinimaxComponent $minimax */
        $created = 0;
        $updated = 0;
        $minimax = Yii::$app->minimax;
        $orgs = $minimax->organisation()->list();
        foreach ($orgs['Rows'] as $row) {
            echo $row['Organisation']['Name'] . ' - ';
            $companyMinimax = $minimax->organisation()->get($row['Organisation']['ID']);
            $company = Company::findOne(['pib' => $companyMinimax['TaxNumber']]);
            if ($company) {
                $company->minimax_id = $companyMinimax['OrganisationId'];
                $company->name = $companyMinimax['Title'];
                $company->save();
                $updated++;
                echo 'updated; ';
            } else {
                $data = [
                    'pib' => $companyMinimax['TaxNumber'],
                    'minimax_id' => $companyMinimax['OrganisationId'],
                    'name' => $companyMinimax['Title']
                ];
                $company = new Company($data);
                $company->save();
                $created++;
                echo 'created; ';
            }
            echo "OK\n";
        }
        echo $created . ' companies created; ', $updated . ' companies updated.' . "\n";
        return ExitCode::OK;
    }
}

<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\services\AuthService;
use app\models\Document;

class DocTestController extends Controller
{
    public function actionIndex()
    {
        $testFileNames = [
            '265201031000971028 Izvod br. 43-Realizovani.pdf' => 'uploaded',
            'LSB7V7E4-LSB7V7E4-23101.pdf' => 'needsRevision',
            'photo_2025-12-09_16-27-53.jpg' => 'checked',
        ];
        foreach ($testFileNames as $testFileName => $status) {
            $filePath = dirname(__DIR__) . '/test/' . $testFileName;
            $docTest = [
                'company_id' => 86,
                'tg_id' => 280362924,
                'metadata' => '{}',
                'status' => $status,
                'filename' => $testFileName,
                'mimetype' => 'application/pdf',
                'content' => file_get_contents($filePath),
            ];
            $docs = (new Document($docTest));
            $docs->save();
            echo "Document with ID {$docs->id} has been inserted\n\n";
        }
        return ExitCode::OK;
    }

    public function actionClear()
    {
        Document::deleteAll(['company_id' => 86, 'tg_id' => 280362924]);
        echo "Test documents cleared\n\n";
        return ExitCode::OK;
    }
}

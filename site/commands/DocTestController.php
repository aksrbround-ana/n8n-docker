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
        $testFileName = 'test.pdf';
        $filePath = dirname(__DIR__) . '/test/' . $testFileName;
        echo $filePath . "\n";
        echo realpath($filePath) . "\n";
        $docTest = [
            'company_id' => 86,
            'tg_id' => 280362924,
            'metadata' => '{}',
            'create_at' => date('Y-m-d H:i:s'),
            'update_at' => date('Y-m-d H:i:s'),
            'status' => 'uploaded',
            'filename' => $testFileName,
            'mimetype' => 'application/pdf',
            'content' => file_get_contents($filePath),
        ];
        $docs = (new Document($docTest));
        $docs->save();
        echo "Document with ID {$docs->id} has been inserted\n\n";
        return ExitCode::OK;
    }
}

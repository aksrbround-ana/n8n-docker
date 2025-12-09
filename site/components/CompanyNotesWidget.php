<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class CompanyNotesWidget extends Widget
{
    public $user;
    public $company;

    public function run()
    {
        $docs = Document::find(['company_id' => $this->company->id])->all();
        return $this->render('companynotes', [
            'user' => $this->user,
            'docs' => $docs,
        ]);
    }
}

<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocListWidget extends Widget
{
    public $user;
    public $company;

    public function run()
    {
        if ($this->company) {
            $docs = Document::find()->where(['company_id' => $this->company->id])->all();
        } else {
            $docs = Document::find()->all();
        }
        return $this->render('doclist', [
            'user' => $this->user,
            'docs' => $docs,
        ]);
    }
}

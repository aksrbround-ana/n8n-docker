<?php

namespace app\components;

use app\models\Document;
use yii\base\Widget;

class DocViewActionsWidget extends Widget
{
    public $user;
    public $document;

    public function run()
    {
        return $this->render('docviewactions', [
            'user' => $this->user,
            'document' => $this->document,
        ]);
    }
}

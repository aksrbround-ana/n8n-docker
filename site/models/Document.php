<?php

namespace app\models;

use Yii;
use app\models\Company;
use app\models\DocumentType;
use app\models\DocumentActivity;
use app\models\DocumentStep;
use app\services\DictionaryService;

/**
 * This is the model class for table "documents".
 *
 * @property int $id
 * @property int|null $tg_id
 * @property int|null $company_id
 * @property int $type_id
 * @property string|null $content
 * @property string|null $metadata
 * @property string|null $embedding
 * @property string|null $filename
 * @property string|null $mimetype
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 */
class Document extends \yii\db\ActiveRecord
{

    const STATUS_UPLOADED = 'uploaded';
    const STATUS_CHECKED = 'checked';
    const STATUS_NEEDS_REVISION = 'needs_revision';

    public $statuses = [
        'uploaded' =>  self::STATUS_UPLOADED,
        'checked' =>  self::STATUS_CHECKED,
        'needsRevision' =>  self::STATUS_NEEDS_REVISION,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'metadata', 'embedding', 'tg_id', 'company_id', 'filename', 'mimetype'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'uploaded'],
            [['content', 'embedding'], 'string'],
            [['metadata', 'created_at', 'updated_at'], 'safe'],
            [['tg_id', 'company_id'], 'default', 'value' => null],
            [['tg_id', 'company_id', 'type_id'], 'integer'],
            [['filename'], 'string', 'max' => 512],
            [['mimetype'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'metadata' => 'Metadata',
            'embedding' => 'Embedding',
            'tg_id' => 'Tg ID',
            'company_id' => 'Company ID',
            'type_id' => 'Type ID',
            'filename' => 'Filename',
            'mimetype' => 'Mimetype',
            'created_at' => 'Create At',
            'updated_at' => 'Update At',
            'status' => 'Status',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $docActivity = new DocumentActivity();
        $docActivity->document_id = $this->id;
        $docActivity->accountant_id = $this->accountant_id;
        if (!$this->isNewRecord) {
            $oldDocument = Document::findOne(['id' => $this->id]);
            if ($oldDocument && $oldDocument->status !== $this->status) {
                $stepName = DocumentStep::$steps[$this->status] ?? null;
                if ($stepName) {
                    $step = DocumentStep::findOne(['name' => $stepName]);
                    if ($step) {
                        $docActivity->step_id = $step->id;
                        $docActivity->save();
                    }
                }
            }
        }
        $ret = parent::save($runValidation, $attributeNames);
        if ($docActivity->document_id === null) {
            $docActivity->document_id = $this->id;
            $step = DocumentStep::findOne(['name' => DocumentStep::STEP_UPLOADED]);
            if ($step) {
                $docActivity->step_id = $step->id;
                $docActivity->save();
            }
        }
        return $ret;
    }

    public function addActivity($accountantId, $stepName)
    {
        $docActivity = new DocumentActivity();
        $docActivity->document_id = $this->id;
        $docActivity->accountant_id = $accountantId;
        $step = DocumentStep::findOne(['name' => $stepName]);
        if ($step) {
            $docActivity->step_id = $step->id;
            $docActivity->save();
        }
    }

    public function getCompany()
    {
        return Company::find()->where(['id' => $this->company_id])->one();
    }

    public function getType()
    {
        return DocumentType::find()->where(['id' => $this->type_id])->one();
    }

    public function getTypeName($lang = 'ru')
    {
        $type = $this->getType();
        if ($type) {
            return DictionaryService::getWord('docType' . ucfirst($type->name), $lang);
        } else {
            return null;
        }
    }

    public function getStatusName($lang = 'ru', $status = null)
    {
        if ($status === null) {
            $status = $this->status;
        }
        $status = str_replace(' ', '', ucwords(str_replace('_', ' ', $status)));
        return DictionaryService::getWord('docStatus' . ucfirst($status), $lang);
    }

    public function getLength($format = true)
    {
        $length =  $this->content ? strlen($this->content) : 0;
        if ($format) {
            return Yii::$app->formatter->asShortSize($length);
        } else {
            return $length;
        }
    }

    public static function getStaticLength($id, $format = true)
    {
        $query = 'SELECT OCTET_LENGTH(content) FROM ' . self::tableName() . ' WHERE id = :id';
        $command = Yii::$app->db->createCommand($query);
        $command->bindValue(':id', $id);
        $length =  $command->queryScalar();
        if ($format) {
            return Yii::$app->formatter->asShortSize($length);
        } else {
            return $length;
        }
    }

    public function getActivities()
    {
        return $this->hasMany(DocumentActivity::class, ['document_id' => 'id']);
    }

    public function getComments()
    {
        return $this->hasMany(DocumentComment::class, ['document_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }
}

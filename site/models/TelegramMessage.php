<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "telegram_messages".
 *
 * @property int $id
 * @property string $message_type
 * @property int $chat_id
 * @property int|null $user_id
 * @property int|null $message_id
 * @property string $response
 * @property string|null $sent_at
 */
class TelegramMessage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'telegram_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'message_id'], 'default', 'value' => null],
            [['message_type', 'chat_id', 'response'], 'required'],
            [['chat_id', 'user_id', 'message_id'], 'default', 'value' => null],
            [['chat_id', 'user_id', 'message_id'], 'integer'],
            [['response'], 'string'],
            [['sent_at'], 'safe'],
            [['message_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_type' => 'Message Type',
            'chat_id' => 'Chat ID',
            'user_id' => 'User ID',
            'message_id' => 'Message ID',
            'response' => 'Response',
            'sent_at' => 'Sent At',
        ];
    }

}

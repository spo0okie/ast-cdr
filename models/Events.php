<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "events".
 *
 * @property int $id id
 * @property int|null $type
 * @property string|null $source
 * @property string|null $destination
 * @property string|null $trunk
 * @property string|null $call_key
 * @property int|null $call_id
 * @property string|null $created_at
 */
class Events extends \yii\db\ActiveRecord
{

	public $call_key;	//ключ вызова для поиска call_id

	public static $event_types=[
		'start.call'		=> 10, //поступление вызова на АТС
		'answer.call'		=> 20, //ответ на вызов (начало тарификации)
		'local.in.call'		=> 30, //вызов внутреннего
		'start.talk'		=> 40, //начало разговора с внутренним
		'end.local.call'	=> 50, //конец внутреннего вызова/раговора
		'end.call'			=> 60, //конец всего вызова
	];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'events';
    }

    public function behaviors(){
		return [
			[
				'class' => \yii\behaviors\TimestampBehavior::className(),
				'attributes' => [
					\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
					//\yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
				],
				// если вместо метки времени UNIX используется datetime:
				'value' => new \yii\db\Expression('NOW()'),
			],
		];
	}
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'call_id'], 'integer'],
            [['created_at'], 'safe'],
            [['source', 'destination', 'trunk'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'source' => 'Source',
            'destination' => 'Destination',
            'trunk' => 'Trunk',
            'call_id' => 'Call ID',
            'created_at' => 'Created At',
        ];
    }

	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			$this->call_id=\app\models\Calls::provideCall($this->call_key);
			return true;
		}
		return false;
	}
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_states".
 *
 * @property int $id
 * @property int|null $call_id
 * @property int $event_id
 * @property string|null $name
 * @property string|null $state
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property \app\models\ChanEvents $event
 */
class CallStates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_states';
    }

	/**
	 * {@inheritdoc}
	 */
	public function behaviors(){
		return [
			[
				'class' => \yii\behaviors\TimestampBehavior::class,
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
            [['call_id','event_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'state'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'call_id' => 'Call ID',
            'name' => 'Name',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEvent()
	{
		return $this->hasOne(ChanEvents::class, ['id'=>'event_id']);
	}

	/**
	 * Предоставляет ID по id звонка и внутреннему номеру (находит или создает новый статус)
	 * @param integer $call_id
	 * @param string $name
	 * @return integer|null
	 */
    static public function setState($call_id,$event_id,$name,$state) {
		$callState=\app\models\CallStates::find()
		->where([
			'call_id'=>$call_id,
			'name'=>$name
		])
		->orderBy(['id'=>SORT_DESC])
		->one();

		//если нашли то проверяем обновления
		if (!is_null($callState) && ($callState->state == $state)) {
			return $callState->id;
		}

		//иначе создаем новый
		$callState=new \app\models\CallStates();

		//прикручиваем ключ
		$callState->call_id=$call_id;
		$callState->name=	$name;
		$callState->state=	$state;
		$callState->event_id=$event_id;

		//if (!empty($uuid)) $call->uuid=$uuid;
		if ($callState->save()) return $callState->getPrimaryKey();
		return null;
	}
}

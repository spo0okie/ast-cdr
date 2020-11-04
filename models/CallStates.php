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
 * @property \app\models\Calls $call
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
	 * @return \yii\db\ActiveQuery
	 */
	public function getCall()
	{
		return $this->hasOne(Calls::class, ['id'=>'call_id']);
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
		if ($callState->save()) {
			static::sendData($callState);
			return $callState->getPrimaryKey();
		}

		return null;
	}

	/**
	 * @param \app\models\CallStates $state
	 * @return bool
	 */
	public static function sendData($state) {
		if (empty(Yii::$app->params['remoteAPI'])) {
			error_log('CallState/sendData: no remote API');
			return false;
		}



		//сюда складываем параметры для отправки в АПИ
		$uuid=$state->call->key;
		$tokens=explode('-',$uuid);
		$src=$tokens[2];
		$params=[
			'src_phone'=>$src,      //заполняем исходящий номер
			'call_id'=>$uuid,    //запоминаем имя файла как идентификатор вызова
		];
		$datastr=$src.' '.$state->state.' '.$state->name;

		//игнорируем ошбки в имени файла
		if (count($tokens)<2) {
			error_log('CallState/sendData: Channel update ignored (Call record file incorrect):' . $datastr);
			return false;
		}

		//заполняем городской номер
		$params['dst_phone']=$tokens[count($tokens)-1];

		//игнорируем исходящие вызовы
		if ($tokens[count($tokens)-2] !== 'IN') {
			error_log('CallState/sendData: Channel update ignored (Outgoing call):' . $datastr);
			return false;
		};

		//игнорируем вызовы с внутреннего
		if (strlen($src)<5) {
			error_log('CallState/sendData: Channel update ignored (Too short CallerID):' . $datastr);
			return false;
		}

		//если вызываемый номер длинный - то звонок на городской
		if (strlen($state->name)>4) {
			if ($state->state=='Ring')
				$params['event_name']='start.call'; //начало вызова
			if ($state->state=='Up')
				$params['event_name']='answer.call'; //гипотетическое событие ответа на городской номер до ответа живого человека
			if ($state->state=='Hangup')
				$params['event_name']='end.call';   //конец звонка
		} else {
			$params['real_local_number']=$state->name;
			if ($state->state=='Ring')
				$params['event_name']='local.in.call';
			if ($state->state=='Up')
				$params['event_name']='start.talk';
			if ($state->state=='Hangup')
				$params['event_name']='end.call';   //конец звонка
		}

		$event=[
			'type'=>'call_event',
			'params'=>$params
		];

		$data=json_encode($event,JSON_FORCE_OBJECT);

		error_log('CallState/sendData: ' . $data);

		$options = [
			'http' => [
				'header'  => "Content-type: application/json\r\n",
				'method'  => 'POST',
				'content' => $data,
			]
		];

		$context  = stream_context_create($options);
		$result = file_get_contents(Yii::$app->params['remoteAPI'].'/events/push', false, $context);
		//msg($this->p.'Data sent:' . $result);
	}

}

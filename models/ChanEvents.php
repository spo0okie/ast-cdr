<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chan_events".
 * @property int $id
 * @property string|null $uid
 * @property string|null $uuid
 * @property string|null $channel
 * @property int|null $channel_id
 * @property int|null $call_state_id
 * @property string|null $data
 * @property int|null $used
 * @property string|null $created_at
 * @property \app\models\Chans $chan
 */
class ChanEvents extends \yii\db\ActiveRecord
{

	public static $states=[
		//'Down'=>NULL,				//Channel is down and available
		//'Rsrvd'=>NULL,			//Channel is down, but reserved
		//'OffHook'=>NULL,			//Channel is off hook
		//'Dialing'=>NULL,			//The channel is in the midst of a dialing operation
		'Ring'=>'Ring',				//The channel is ringing
		'Ringing'=>'Ringing',		//The remote endpoint is ringing. Note that for many channel technologies, this is the same as Ring.
		'Up'=>'Up',					//A communication path is established between the endpoint and Asterisk
		//'Busy'=>NULL,				//A busy indication has occurred on the channel
		//'Dialing Offhook'=>NULL,	//Digits (or equivalent) have been dialed while offhook
		//'Pre-ring'=>NULL,			//The channel technology has detected an incoming call and is waiting for a ringing indication
		//'Unknown'=>NULL			//The channel is an unknown state
		'Hangup'=>'Hangup',			//Окончание разговора
	];

	private $parsed_pars=null; //распарсенные параметры

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chan_events';
    }

	/**
	 * {@inheritdoc}
	 */
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
			[['channel_id', 'used','uid'], 'integer'],
			[['data'], 'string'],
            [['created_at'], 'safe'],
            [['uuid'], 'string', 'max' => 32],
            [['channel'], 'string', 'max' => 128],
		];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'channel' => 'Channel',
            'channel_id' => 'Channel ID',
            'data' => 'Data',
            'created_at' => 'Время',
        ];
    }

	/**
	 * Параметры передаются и хранятся в JSON виде, а чтобы ими попользоваться - надо распарсить
	 */
	protected function initPars() {
		if (is_null($this->parsed_pars)) {
			if (strlen($this->data)) {
				$this->parsed_pars=json_decode($this->data,true);
			} else {
				$this->parsed_pars=[];
			}
		}
	}

	/**
	 * Возвращает наличие элемента $item в эвенте
	 * @param string $item имя параметра
	 * @return bool признак наличия элемента в эвенте
	 */
	public function exists($item) {
		$this->initPars();
		return
			isset($this->parsed_pars[$item])&&
			(
				is_array($this->parsed_pars[$item])||
				strlen($this->parsed_pars[$item])
			);
	}

	/**
	 * возвращает true, если итем есть и числовой
	 * @param string $item имя параметра
	 * @return bool признак наличия числового элемента в эвенте
	 */
	public function numeric($item) {
		$this->initPars();
		return $this->exists($item)&&is_numeric($this->parsed_pars[$item]);
	}

	/**
	 * получить значение элемента или "по умолчанию", если элемента нет
	 * @param string $name имя элемента
	 * @param string $default значение по умолчанию
	 * @return null|string
	 */
	public function getPar($name, $default=NULL) {
		$this->initPars();
		return $this->exists($name)?
			$this->parsed_pars[$name]:
			$default;
	}

	/**
	 * ищем номер звонящего абонента в параметрах ивента
	 * @return null|string
	 */
	public function getSrc() {
		return $this->getPar('CallerIDNum');
	}

	/**
	 * ищем номер вызываемого абонента в параметрах ивента
	 * @return null
	 */
	public function getDst() {
		if ($this->numeric('ConnectedLineNum'))	return $this->parsed_pars['ConnectedLineNum'];
		if ($this->numeric('Exten'))				return $this->parsed_pars['Exten'];
		return NULL;
	}

	/**
	 * возвращает имя файла записи звонка
	 * @return null
	 */
	public function getMonitor() {
		//если передано явно
		if ($this->getPar('Application')=='Monitor') {
			$parts=explode(',',$this->getPar('AppData'))[1];
			$tokens=explode('/',$parts);
			return $tokens[count($tokens)-1];
		}
		//если втолкано в ast-ami
		if (is_array($vars=$this->getPar('variables'))) {
			if (isset($vars['monitor'])) return $vars['monitor'];
		}
		return NULL;
	}

	/**
	 * возвращает имя файла записи звонка
	 * @return null
	 */
	public function getOrg() {
		//если втолкано в ast-ami
		if (is_array($vars=$this->getPar('variables'))) {
			if (isset($vars['org'])) return $vars['org'];
		}
		return NULL;
	}

	/**
	 * возвращает признак что звонок притворяется входящим, будучи на самом деле
	 * исходящим сделанным не с телефона а чере call файл. тогда сначала звонит аппарат
	 * вызывающего, и отображается CallerID вызываемого. Если это не обработать специально
	 * то такой вызов классифицируется как входящий. Поэтому все вызовы через call файлы
	 * помещаются в специальный контекст, который проверяется в этой функции
	 * - не вышло с контекстом, пробуем через caller ID
	 * @return bool
	 */
	public function isFakeIncoming()
	{	return ($this->getPar('CallerIDName')===(API_CALLOUT_PREFIX.$this->getPar('ConnectedLineNum')))
			||($this->getPar('CallerIDName')===(API_CALLOUT_PREFIX.$this->getPar('CallerIDNum')))
			||($this->getPar('ConnectedLineName')===(API_CALLOUT_PREFIX.$this->getPar('ConnectedLineNum')));
	}


	/**
	 * возвращает статус канала из параметров ивента,
	 * но только если этотстатус нас интересует
	 * можно раскоментить в static::$states и другие статусы, но нужно потом их обрабатывать
	 * @return mixed|null
	 */
	public function getState()
	{
		if 	(isset($this->parsed_pars['ChannelStateDesc'])&&strlen($state=$this->parsed_pars['ChannelStateDesc'])) //если статус в ивенте указан
			return isset(static::$states[$state])?		//возвращаем его если он есть в фильтре
				static::$states[$state]:
				NULL;

		return NULL; //на нет и суда нет
	}

	public function getChan() {
		return $this->hasOne(\app\models\Chans::class, ['id' => 'channel_id']);
	}


	public function getCallState() {
		return $this->hasOne(\app\models\CallStates::class, ['id' => 'call_state_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if ($insert) {
				$this->channel_id=\app\models\Chans::provideId($this);
			}
			return true;
		}
		return false;
	}

	public function beforeValidate() {

		if (parent::beforeValidate()) {
			//if (empty($this->uid))		$this->uid=$this->getPar('uid');
			if (empty($this->uuid))		$this->uuid=$this->getPar('Uniqueid');
			if (empty($this->channel))	$this->channel=$this->getPar('Channel');
			return true;
		}
		return false;
	}

	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);

		//если это собитие уже использовалось для обновления канала, значит все данные из него уже взяты
		//(события не меняются со временем)
		if ($this->used) return;

		//иначе передаем данные в канал
		$this->chan->upd($this);

		//если пригодились - отмечаем это
		if ($this->used) $this->save(false);

		//если канал обновился - сохраняем его
		if ($this->chan->updated) $this->chan->save(false);

	}
}

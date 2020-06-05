<?php

namespace app\models;

//на контекст куда бросаются вызовы из call файлов называется org1_api_outcall
define ('API_CALLOUT_PREFIX','Вызов ');

use Yii;

/**
 * This is the model class for table "chans".
 *
 * @property int $id
 * @property string|null $uuid
 * @property int|null $call_id
 * @property string|null $name
 * @property string|null $state
 * @property string|null $src
 * @property string|null $dst
 * @property string|null $smartSrc
 * @property string|null $smartDst
 * @property bool $isReversed
 * @property string|null $tech
 * @property string|null $bareName
 * @property int|null $wasRinging
 * @property int|null $reversed
 * @property int|null $deleted
 * @property string|null $vars
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property \app\models\Calls call
 */
class Chans extends \yii\db\ActiveRecord
{

	private $parsed_vars=null;
	public $updated=false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chans';
    }

	public function behaviors(){
		return [
			[
				'class' => \yii\behaviors\TimestampBehavior::className(),
				'attributes' => [
					\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
					\yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
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
			[['call_id', 'wasRinging', 'reversed', 'deleted'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['uuid'], 'string', 'max' => 16],
			[['name', 'state', 'src', 'dst'], 'string', 'max' => 64],
			[['vars'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
			'uuid' => 'Uuid',
			'call_id' => 'Call ID',
			'name' => 'Name',
            'vars' => 'Vars',
            'state' => 'State',
            'src' => 'Src',
            'dst' => 'Dst',
            'wasRinging' => 'Was Ringing',
            'reversed' => 'Reversed',
			'deleted' => 'Deleted',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
    }

	/**
	 * Нужно ли развернуть канал задом наперед
	 * @return bool
	 */
	public function getIsReversed() {
		return $this->wasRinging xor $this->reversed;
	}

	/**
	 * Иногда астер на поднятие трубки разворачивает $dst и $src
	 * @return bool
	 */
	public function eventReversed($CallerID) {
		if (!is_object($this->call)) return false;
		return $this->call->source==$CallerID;
	}

	/**
	 * Вернуть фактический источник с учетом разворота направления
	 * @return null|string
	 */
	public function getSmartSrc() {
		$src=($this->isReversed xor $this->eventReversed($this->src))? $this->dst: $this->src;
		if (strpos($src,'_')) {
			$src=substr($src,strpos($src,'_')+1);
		}
		return $src;
	}

	/**
	 * Вернуть фактическое направление с учетом разворота направления
	 * @return null|string
	 */
	public function getSmartDst() {
		$dst=$this->isReversed? $this->src: $this->dst;
		if (strpos($dst,'_')) {
			$dst=substr($dst,strpos($dst,'_')+1);
		}
		//обнаруживаем вызовы через local/500 XX YYY
		if (strlen($dst)>='8' && strlen($dst)<='10' && substr($dst,0,3)=='500') {
			$dst=substr($dst,5);
		}
		//обнаруживаем вызовы через local/05 XX YY ZZZ
		if (strlen($dst)>='9' && strlen($dst)<='11' && substr($dst,0,1)=='05') {
			$dst=substr($dst,6);
		}
		return $dst;
	}

	/**
	 * Вернуть фактическое состояние
	 * @return null|string
	 */
	public function getSmartState() {
		return ($this->state=='Ringing')?'Ring':$this->state;
	}

	/**
 	* возвращает технологию канала
 	* @return string технолгия канала
 	*/
	public function getTech() {
		//разбираем канал в соответствии с синтаксисом
		if (!($slash=strpos($this->name,'/'))) return NULL;			//несоотв синтаксиса
		return substr($this->name,0,$slash);
	}

	/**
	 * возвращает имя канала без технологии
	 * @return string технолгия канала
	 */
	public function getBareName() {
		//разбираем канал в соответствии с синтаксисом
		if (!($slash=strpos($this->name,'/'))) return NULL;			//несоотв синтаксиса
		return substr($this->name,$slash+1);
	}


	protected function initVars() {
		if (is_null($this->parsed_vars)) {
			if (strlen($this->vars)) {
				$this->parsed_vars=json_decode($this->vars,true);
			} else {
				$this->parsed_vars=[];
			}
		}
	}

	/**
	 * Возвращает значение переменной если такая в канале есть (иначе null)
	 * @param string $name имя переменной
	 * @param string $default значение, которое вернуть в случае отсутствия переменной
	 * @return string значение или $default
	 */
	public function getVar($name,$default=null){
		$this->initVars();
		return
			(isset($this->parsed_vars[$name])&&!is_null($this->parsed_vars[$name]))?
				$this->parsed_vars[$name]:$default;
	}

	/**
	 * Выставляет значение канальной переменной если есть что выставлять
	 * @param string $name имя переменной
	 * @param string $value значение переменной
	 */
	public function setVar($name,$value){
		$this->initVars();
		if (strlen($name)&&strlen($value))
			$this->parsed_vars[$name]=$value;
		$this->vars=json_encode($this->parsed_vars,JSON_UNESCAPED_UNICODE);
		$this->save();
	}

	public function getCall() {
		return $this->hasOne(\app\models\Calls::class, ['id' => 'call_id']);
	}

	public function fetchCall() {
		if (empty($this->call_id))
			$this->call_id=\app\models\Calls::provideCall($this->getVar('monitor'));
		return $this->call;
	}

	/**
	 * Если мы еще не знаем имя файла записи, то запоминаем его
	 * @param $value
	 * @return bool
	 */
	public function setMonitor($value) {
		if (!$this->getVar('monitor')) {
			$this->setVar('monitor',$value);
			$this->call_id=\app\models\Calls::provideCall($value);
			$this->updated=true;
			return true;
		}
		return false;
	}

	/**
	 * Нам надо обрабатывать дополнительно еще и события мониторинга
	 * чтобы отловить имя файла
	 * @param \app\models\ChanEvents $evt
	 */
	public function handleAppMonitor(&$evt) {
		/*
		 * Класс события полученного от AMI
			[Event] => Newexten
			[Privilege] => dialplan,all
			[Channel] => SIP/telphin_yamal-000008b7
			[Context] => macro-RecordCall
			[Extension] => s
			[Priority] => 6
			[Application] => Monitor
			[AppData] => wav,/home/record/yamal/_current/20170210-221016-+79193393655-IN-+79193393655,m
			[Uniqueid] => 1486746616.2615
		 */
		$path=explode(',',$evt->getPar('AppData'))[1];
		$tokens=explode('/',$path);
		$this->setMonitor($tokens[count($tokens)-1]);
	}


	/**
	 * @param \app\models\ChanEvents $evt
	 * @return bool
	 */
	public function upd(&$evt)
	{//обновляем информацию о канале новыми данными
		$oldState	=$this->state;			//запоминаем старый статус
		$oldSrc  	=$this->src;
		$oldDst  	=$this->dst;
		$oldRinging	=$this->wasRinging;

		if (($monitor=$evt->getMonitor()))
			$this->setMonitor($monitor);

		if ($evt->getPar('Application')=='Monitor') $this->handleAppMonitor($evt);
		if ($evt->getPar('Event')=='Rename') {
			$this->name=$evt->getPar('Newname');
			$this->updated=true;
			$evt->used=1;
		}

		/* обновляем информацию всегда, когда есть что обновить (больше обновлений) */
		if (!is_null($src=$evt->getSrc()))			$this->src=$src; //ищем вызывающего
		if (!is_null($dst=$evt->getDst())) 			$this->dst=$dst; //ищем вызываемого
		if (!is_null($newstate=$evt->getState())) {
			$this->state=$newstate;//устанавливаем статус
			if ($this->state==='Ringing') $this->wasRinging=true;
		}

		//пугает меня этот вызов не зафлудить бы АМИ этимим запросами
		//if (is_null($this->monitor)) $this->monitor=$this->getMonitorVar();

		//проверяем что это не исходящий звонок начинающийся со звонка на аппарат звонящего
		//с демонстрацией callerID абонента куда будет совершен вызов, если снять трубку
		//(костыль для обнаружения вызовов через call файлы)
		$this->reversed=$this->reversed||$evt->isFakeIncoming();

		//если чтото изменилось
		if (($this->src !== $oldSrc) ||	($this->dst !== $oldDst) ||	($this->state) !== $oldState || ($this->wasRinging) !== $oldRinging) {
			$this->updated=true;
			$evt->used=1;
		}

		//возвращаем флаг необходимости отправки данных (канал укомплектован и инфо обновилась)
		if (
			!is_null($this->src)&&
			!is_null($this->dst)&&
			!is_null($this->state)&&
			($oldState!==$this->state)&&
			is_object($this->call)&&
			($this->getTech()=='SIP')
		) {
			$this->call->setState(
				$this->smartDst,
				$this->smartState,
				$evt->id
			);
			return true;
		};
	}


	/**
	 * Собственно тут мы пытаемся сгенерить событие как это делает проект ast-ami
	 */
/*	private function generateCallEvent() {
		$event=new \app\models\Events();
		$event->destination=$this->dst;
		$event->source=$this->src;

		//сюда складываем параметры для отправки в АПИ
		$params=[
			'src_phone'=>$data['src'],      //заполняем исходящий номер
			'call_id'=>$data['monitor'],    //запоминаем имя файла как идентификатор вызова
		];

		//игнорируем незаписываемые вызовы
		if (!strlen($data['monitor'])) {
			msg($this->p.'Channel update ignored (Call not recorded):' . $datastr ,3);
			return true;
		}

		//разбираем имя файла на токены
		$mon_tokens=explode('-',$data['monitor']);

		//игнорируем ошбки в имени файла
		if (count($mon_tokens)<2) {
			msg($this->p.'Channel update ignored (Call record file incorrect):' . $datastr ,3);
			return true;
		}

		//заполняем городской номер
		$params['dst_phone']=$mon_tokens[count($mon_tokens)-1];

		//игнорируем исходящие вызовы
		if ($mon_tokens[count($mon_tokens)-2] !== 'IN') {
			msg($this->p.'Channel update ignored (Outgoing call):' . $datastr ,3);
			return true;
		};

		//игнорируем вызовы с внутреннего
		if (strlen($data['src'])<5) {
			msg($this->p.'Channel update ignored (Too short CallerID):' . $datastr ,3);
			return true;
		}

		//если вызываемый номер длинный - то звонок на городской
		if (strlen($data['dst'])>4) {
			if ($data['state']=='Ring')
				$params['event_name']='start.call'; //начало вызова
			if ($data['state']=='Up')
				$params['event_name']='answer.call'; //гипотетическое событие ответа на городской номер до ответа живого человека
			if ($data['state']=='Hangup')
				$params['event_name']='end.call';   //конец звонка
		} else {
			$params['real_local_number']=$data['dst'];
			if ($data['state']=='Ring')
				$params['event_name']='local.in.call';
			if ($data['state']=='Up')
				$params['event_name']='start.talk';
			if ($data['state']=='Hangup')
				$params['event_name']='end.call';   //конец звонка
		}

		$event=[
			'type'=>'call_event',

			'params'=>$params
		];

		$data=json_encode($event,JSON_FORCE_OBJECT);

		msg($this->p.'Sending data:' . $data);

		$options = [
			'http' => [
				'header'  => "Content-type: application/json\r\n",
				'method'  => 'POST',
				'content' => $data,
			]
		];

		$context  = stream_context_create($options);
		$result = file_get_contents('http://'.$this->url.'/push', false, $context);
		msg($this->p.'Data sent:' . $result);

	}*/

	/*
	 * суть: в зависимости от статуса Ring или Ringing меняется смысл кто кому звонит
	 * поэтому если вдруг у нас ringing, то мы меняем его на ring и меняем местами абонентов
	 * таким образом всегда понятно что src -> dst, что проще
	 */
	private function needReverse()
	{
		if ($this->state==='Ringing') $this->wasRinging=true;
		if ($this->dst===$this->call->source) $this->reversed=true;
		return ($this->wasRinging===true) xor ($this->reversed===true);
	}

	public function getState(){
		if ($this->state==='Ringing') return 'Ring';
		return $this->state;
	}

	public function getSrc(){
		return $this->needReverse()?$this->dst:$this->src;
	}

	public function getDst(){
		return $this->needReverse()?$this->src:$this->dst;
	}

	public function getData(){
		return [
			'src'	=>$this->getSrc(),
			'dst'	=>$this->getDst(),
			'state'	=>$this->getState(),
			'monitor'=>$this->monitor,
		];
	}

	/**
	 * Предоставляет ID по ключу (находит или создает новый вызов)
	 * @param \app\models\ChanEvents $evt
	 * @return integer|null
	 */
	static public function provideId($evt) {
		$chan=\app\models\Chans::findOne([
			'name'=>$evt->channel,
			'uuid'=>$evt->uuid
		]);
		//если нашли то и ОК
		if (!is_null($chan)) {
			//$chan->upd($evt);
			//if ($chan->updated) $chan->save(false);
			return $chan->id;
		}
		//иначе создаем новый
		$chan=new \app\models\Chans();
		//прикручиваем ключ
		$chan->name=$evt->channel;
		$chan->uuid=$evt->uuid;
		//$chan->upd($evt);
		if ($chan->save(false)) return $chan->getPrimaryKey();
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if (empty($this->call_id))
				if (strlen($monitor=$this->getVar('monitor')))
					$this->call_id=\app\models\Calls::provideCall($monitor);
			return true;
		}
		return false;
	}

}

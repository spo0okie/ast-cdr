<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 09.03.2020
 * Time: 22:59
 */

namespace app\modules\json\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;

class ChanEventsController extends Controller
{

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'create' => ['POST'],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action)
	{
		if ($action->id == 'create') {
			$this->enableCsrfValidation = false;
		}

		return parent::beforeAction($action);
	}

	/**
	 * Возвращает объект - ошибку
	 * @param $code //код ошибки
	 * @param $message //сообщение
	 * @return object
	 */
	static protected function resultError($code,$message) {	return (object)[
		'error'=>$code?'ERROR':'OK',
		'code'=>$code,
		'description'=>$message
	];}

	/**
	 * создает ивент из переданной структуры
	 * @param $data
	 * @return object
	 */
	public function createEvent($data,$raw) {
		/*{
		Класс события полученного от AMI
		[Event] => Newexten
		[Privilege] => dialplan,all
		[Channel] => SIP/telphin_yamal-000008b7
		[Context] => macro-RecordCall
		[Extension] => s
		[Priority] => 6
    	[Application] => Monitor
		[AppData] => wav,/home/record/yamal/_current/20170210-221016-+79193393655-IN-+79193393655
    	[Uniqueid] => 1486746616.2615		//}*/

		//if (empty($data->params)) 		return static::resultError(4,'Field params not defined');
		//if (!is_object($data->params)) 	return static::resultError(4,'Field params is not json object');

		//$params=$data->params;

		//if (empty($params->event_name)) return static::resultError(5,'Field params\event_name not defined');
		//if (!isset(\app\models\Events::$event_types[$params->event_name]))
		//	return static::resultError(5,'Field unknown params\event_name');

		//if (empty($params->call_id))	return static::resultError(6,'Field params\call_id not defined');

		$event=new \app\models\ChanEvents();

		$event->data=$raw;
		//$event->channel_id=0;
		//$event->uid='1';
		//$event->channel='2';
		//$event->type=\app\models\Events::$event_types[$params->event_name];

		if (!$event->save())	return static::resultError(10,'Cant save chan-event');

		return static::resultError(0,'Event saved');
	}

	public function actionCreate() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$requestRaw=\Yii::$app->request->getRawBody();
		$requestJson = json_decode($requestRaw, false);

		//*{
		//"type":"call_event",
		//"params":{
		//	"src_phone":"+73517214172",
		//	"call_id":"20200303-205705-+73517214172-IN-2020318",
		//	"dst_phone":"2020318",
		//	"real_local_number":"102",
		//	"event_name":"local.in.call"
		//	}
		//}
		$result=null;

		//проверка успешности парсинга
		if (!is_object($requestJson)) return static::resultError(1,'Unable to parse data :'.$requestRaw);

		//проверка корректности синтаксиса
		if (empty($requestJson->Event)) return static::resultError(2,'Field Event not defined');

		return $this->createEvent($requestJson,$requestRaw);
	}

	public function actionTest() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
		return 'OK: test OK';
	}
};


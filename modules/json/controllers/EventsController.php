<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 01.03.2020
 * Time: 18:24
 */

namespace app\modules\json\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;

class EventsController extends Controller
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
	public function createEvent($data) {
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

		if (empty($data->params)) 		return static::resultError(4,'Field params not defined');
		if (!is_object($data->params)) 	return static::resultError(4,'Field params is not json object');

		$params=$data->params;

		if (empty($params->event_name)) return static::resultError(5,'Field params\event_name not defined');
		if (!isset(\app\models\Events::$event_types[$params->event_name]))
			return static::resultError(5,'Field unknown params\event_name');

		if (empty($params->call_id))	return static::resultError(6,'Field params\call_id not defined');

		$event=new \app\models\Events();

		$event->call_key=$params->call_id;
		$event->type=\app\models\Events::$event_types[$params->event_name];

		if (!empty($params->src_phone))
			$event->source=$params->src_phone;

		if (!empty($params->dst_phone))
			$event->trunk=$params->dst_phone;

		if (!empty($params->real_local_number))
			$event->destination=$params->real_local_number;

		if (!$event->save())	return static::resultError(10,'Cant save event');

		return static::resultError(0,'Event saved');
	}

	public function actionCreate() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$requestJson = json_decode(\Yii::$app->request->getRawBody(), false);

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
		if (!is_object($requestJson)) return static::resultError(1,'Unable to parse data');

		//проверка корректности синтаксиса
		if (empty($requestJson->type)) return static::resultError(2,'Field type not defined');

		switch ($requestJson->type) {
			case 'call_event':
				return $this->createEvent($requestJson);
				break;
			default:
				return static::resultError(3,'Unknown type value');
		}
	}

	public function actionTest() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return 'OK: test OK';
	}
};


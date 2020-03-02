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

class CallController extends Controller
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

	public function actionCreate() {
		$requestJson = json_decode(\Yii::$app->request->getRawBody(), true);
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		return $requestJson ;
	}

	public function actionView() {

	}
};


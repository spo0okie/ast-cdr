<?php

namespace app\modules\rest;


/**
 * Rest module definition class
 */
class Rest extends \yii\base\Module
{
	/**
	 * @inheritdoc
	 */
	public $controllerNamespace = 'app\modules\rest\controllers';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		\Yii::$app->user->enableSession = false;
		// custom initialization code goes here
	}



}

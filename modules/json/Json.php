<?php

namespace app\modules\json;


/**
 * Rest module definition class
 */
class Json extends \yii\base\Module
{
	/**
	 * @inheritdoc
	 */
	public $controllerNamespace = 'app\modules\json\controllers';

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

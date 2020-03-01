<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 01.03.2020
 * Time: 14:53
 */

namespace app\modules\api\controllers;

use app\models\Events;


class EventsController extends \yii\rest\ActiveController
{
	public $modelClass='app\models\Events';
}
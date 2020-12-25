<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 01.03.2020
 * Time: 14:53
 */

namespace app\modules\rest\controllers;

use app\models\Orgs;


class OrgsController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\Orgs';

    public function actions()
    {
        return ['workTime'];
    }

    public function actionWorkTime($id) {
        if (is_null($org=Orgs::findOne($id))) throw new \yii\web\NotFoundHttpException("Organization '$id' not found");
        if (is_null($schedule=$org->getDateSchedule(date('Y-m-d',time())))) return '*';
        return $schedule->schedule;
    }
}
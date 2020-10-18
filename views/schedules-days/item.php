<?php
/**
 * Рендер элемента графика рабочего дня
 * Created by PhpStorm.
 * User: reviakin.a
 * Date: 18.10.2020
 * Time: 17:33
 */

/* @var $this yii\web\View */
/* @var $model app\models\SchedulesDays */

if (is_object($model))
    echo \yii\helpers\Html::a($model->description,['/schedules/view/','id'=>$model->schedule_id,'date'=>$model->date,'#'=>'day-'.$model->date]);
else
    echo ' - график не определен -';


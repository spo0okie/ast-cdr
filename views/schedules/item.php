<?php
/**
 * Рендер элемента расписания
 * Created by PhpStorm.
 * User: reviakin.a
 * Date: 18.10.2020
 * Time: 17:01
 */

/* @var $this yii\web\View */
/* @var $model app\models\Schedules */

if (is_object($model))
    echo \yii\helpers\Html::a($model->name,['/schedules/view','id'=>$model->id]);
else
    echo ' - расписание отсутстует -';

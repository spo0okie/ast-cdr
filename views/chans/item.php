<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 16.03.2020
 * Time: 20:55
 */

/* @var $this yii\web\View */
/* @var $model app\models\Chans */

if (is_object($model))
	echo \yii\helpers\Html::a($model->name,['/chans/view','id'=>$model->id]);
else
	echo '-';


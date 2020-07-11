<?php
/**
 * Рендер элемента организации
 * Created by PhpStorm.
 * User: spookie
 * Date: 11.07.2020
 * Time: 15:01
 */

/* @var $this yii\web\View */
/* @var $model app\models\Orgs */
if (is_object($model))
	echo \yii\helpers\Html::a($model->name,['/orgs/view','id'=>$model->id]);
elseif (isset($num))
	echo $num;
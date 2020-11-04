<?php
/**
 * Рендер элемента Вызов
 * User: spookie
 * Date: 07.03.2020
 * Time: 9:50
 */

/* @var $this yii\web\View */
/* @var $model app\models\Calls */
if (is_object($model))
echo \yii\helpers\Html::a(strlen($model->key)?$model->key:$model->uuid,['/calls/view','id'=>$model->id]);
if (!empty(Yii::$app->params['remoteAPI'])) {
    echo '&nbsp;';
    echo \yii\helpers\Html::a(
        '<span class="small glyphicon glyphicon-floppy-disk"></span>',
        Yii::$app->params['remoteAPI'].'/records/get/org'.$model->org_id.'/'.$model->key
    );
}
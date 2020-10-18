<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
$renderer=$this;
?>
<div class="orgs-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            'code',
            [
                'attribute' =>'name',
                'format'    =>'raw',
                'value'     =>function ($data) {
                    return \yii\helpers\Html::a($data->name,['view','id'=>$data->id]);
                }
			],
			[
				'attribute'	=>'lastIncoming.age',
				'header'    =>'Посл. входящий',
				'format'	=>'raw',
				'value'		=>function($data) use ($renderer) {
					return (is_object($data->lastIncoming))?\yii\helpers\Html::a($data->lastIncoming->age,['/calls/view','id'=>$data->lastIncoming->id]):null;
				}
			],
			[
				'attribute'	=>'lastOutgoing.age',
                'header'    =>'Посл. исходящий',
				'format'	=>'raw',
				'value'		=>function($data) use ($renderer) {
					return (is_object($data->lastOutgoing))?\yii\helpers\Html::a($data->lastOutgoing->age,['/calls/view','id'=>$data->lastOutgoing->id]):null;
				}
			],
            //'comment:ntext',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calls-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            [
            	'attribute'	=>'key',
				'format'	=>'raw',
				'value'		=>function($data) {
					return \yii\helpers\Html::a($data->key,['view','id'=>$data->id]);
				}
            ],
            'org_id',
            //'comment:ntext',
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

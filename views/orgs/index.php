<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orgs';
$this->params['breadcrumbs'][] = $this->title;
$renderer=$this;
?>
<div class="orgs-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Orgs', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            'code',
            'name',
			'lastIncoming.age',
			[
				'attribute'	=>'lastIncoming.age',
				'format'	=>'raw',
				'value'		=>function($data) use ($renderer) {
					return (is_object($data->lastIncoming))?
						\yii\helpers\Html::a($data->lastIncoming->age,['/calls/view','id'=>$data->lastIncoming->id])
						:null;
				}
			],
			'lastOutgoing.age',
            'comment:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

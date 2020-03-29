<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$renderer = $this;
if (!isset($columns)) $columns=['created_at','name','state','event_id'];
if (!isset($searchModel)) $searchModel=null;

//формируем список столбцов для рендера
$render_columns=[];
foreach ($columns as $column) {

	switch ($column) {
		case 'event_id':
			$render_columns[] = [
				'attribute' => 'event_id',
				'format' => 'raw',
				'value' => function ($data) use ($renderer) {
					if (is_object($data->event)) return \yii\helpers\Html::a(
						$data->event->chan->name.'::'.$data->event->getPar('Event'),
						['/chans/view','id'=>$data->event->channel_id,'#'=>'evt'.$data->event_id]
					);
					return $data->event_id;
				},
				'contentOptions'=>['class'=>$column.'_col']
			];
			break;

		default: $render_columns[]=$column;
	}
}

echo GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => $render_columns,
]);
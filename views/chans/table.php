<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$renderer = $this;
if (!isset($columns)) $columns=['created_at','updated_at','call_id','name','state','src','dst'];
if (!isset($searchModel)) $searchModel=null;

//формируем список столбцов для рендера
$render_columns=[];
foreach ($columns as $column) {
	switch ($column) {
		case 'call_id':
			$render_columns[] = [
				'attribute' => 'call_id',
				'format'=>'raw',
				'value' => function ($data) use($renderer) {return $renderer->render('/calls/item',['model'=>$data->call]);},
				'contentOptions'=>['class'=>$column.'_col'],
			];
			break;

		case 'name':
			$render_columns[] = [
				'attribute' => 'name',
				'format'=>'raw',
				'value' => function ($data) use($renderer) {return $renderer->render('/chans/item',['model'=>$data]);},
				'contentOptions'=>function ($data) use ($column) {return[
					'class'=>$column.'_col',
					'id'=>'chan'.$data->id,
				];},
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


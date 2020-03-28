<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChanEventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$renderer = $this;
if (!isset($columns)) $columns=['created_at','uid','chan','data'];
if (!isset($searchModel)) $searchModel=null;

//формируем список столбцов для рендера
$render_columns=[];
foreach ($columns as $column) {

	switch ($column) {
		case 'chan':
			$render_columns[] = [
				'attribute' => 'channel',
				'format' => 'raw',
				'value' => function ($data) use ($renderer) {
					return $renderer->render('/chans/item', ['model' => $data->chan]);
				},
				'contentOptions'=>['class'=>$column.'_col']
			];
			break;

		case 'data':
			$render_columns[] = [
				'attribute' => 'data',
				'format' => 'raw',
				'value' => function ($data) use ($renderer) {
					$params=json_decode($data->data,true);
					if (isset($params['Privilege'])) unset ($params['Privilege']); //ну это нам ваще неинтересно
					$output=[];
					foreach ($params as $key=>$value) {
						if (is_array($value)) {
							$output[]=htmlspecialchars("[$key] => [");
							foreach ($value as $subkey=>$item) $output[]="&nbsp;&nbsp;&nbsp;&nbsp;[$subkey] => $item";
							$output[]="]";
						} else $output[]=htmlspecialchars("[$key] => $value");
					}
					return '<span class="data_'.($data->used?'used':'unused').'">'.
						implode('<br />',$output).
						'</span>';
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
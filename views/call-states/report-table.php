<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $filter \app\models\ReportFilter */
/* @var $filter_action string */
/* @var $dataProvider yii\data\SqlDataProvider */

$numbers=[];
$data=[];
//var_dump($dataProvider->getModels());

foreach ($dataProvider->models as $model) {
	if (!isset($numbers[$model['name']]))
		$numbers[$model['name']]=$model['name'];

	if (!isset($data[$model['date']]))
		$data[$model['date']]=['date'=>$model['date']];

	if (!isset($data[$model['date']]['sum']))
		$data[$model['date']]['sum']=0;

	$data[$model['date']][$model['name']]=$model['count'];
	$data[$model['date']]['sum']+=$model['count'];
}

$columns=[
	[
		'attribute' => 'date',
		'format' => 'date',
	]
];
$columns+=$numbers;
$columns[]=[
	'attribute' => 'date',
	'format' => 'raw',
	'value' => function ($data) use ($filter) {
		if ($data['sum']>0) {
			$filter->date=$data['date'];

			return \yii\helpers\Html::a(
				$data['sum'],
				['/calls/index']+$filter->formData()
			);
		}
		return null;
	}
];
//var_dump($columns);

echo GridView::widget([
	'dataProvider' => new \yii\data\ArrayDataProvider([
		'allModels'=>array_values($data),
		'pagination' => ['pageSize' => 1000,],
	]),
	'columns' => $columns,
	'formatter' => [
		'class' => 'yii\i18n\Formatter',
		'nullDisplay' => '',
		'locale' => 'ru-RU',
		'dateFormat' => 'dd.MM.y (EE)',
		'datetimeFormat' => 'dd.MM.y HH:mm:ss',
	],
]);
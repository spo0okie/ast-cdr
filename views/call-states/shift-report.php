<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProviderDay yii\data\SqlDataProvider */
/* @var $dataProviderNight yii\data\SqlDataProvider */

$this->title = 'Отчет по сменам';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-states-shift-report">

    <h1><?= Html::encode($this->title) ?></h1>

	<div class="col-md-6">
		<h3>День</h3>
		<?= GridView::widget([
			'dataProvider' => $dataProviderDay,
			'columns' => [
				'date',
				'name',
				'count',
			],
		]); ?>
	</div>
	<div class="col-md-6">
		<h3>Ночь</h3>
		<?= GridView::widget([
			'dataProvider' => $dataProviderNight,
			'columns' => [
				'date',
				'name',
				'count',
			],
		]); ?>
	</div>


</div>

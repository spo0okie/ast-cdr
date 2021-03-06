<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $filter \app\models\ReportFilter */
/* @var $filter_action string */
/* @var $dataProviderDay yii\data\SqlDataProvider */
/* @var $dataProviderNight yii\data\SqlDataProvider */

$this->title = 'Отчет по сменам';
$this->params['breadcrumbs'][] = $this->title;

$filter_model_in = clone $filter;
$filter_model_in->innerInterval=true;

$filter_model_out = clone $filter;
$filter_model_out->innerInterval=false;

?>
<div class="call-states-shift-report">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('/report-filter/_form',['model'=>$filter,'action'=>$filter_action]) ?>

	<div class="col-md-6">
		<h3>День</h3>
		<?= $this->render('report-table',['dataProvider'=>$dataProviderDay,'filter'=>$filter_model_in]) ?>
	</div>
	<div class="col-md-6">
		<h3>Ночь</h3>
		<?= $this->render('report-table',['dataProvider'=>$dataProviderNight,'filter'=>$filter_model_out]) ?>
	</div>


</div>

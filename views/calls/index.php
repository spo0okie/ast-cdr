<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filter \app\models\ReportFilter */


$this->title = 'Calls';
$this->params['breadcrumbs'][] = $this->title;

$renderer=$this;
?>
<div class="calls-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('/report-filter/_form',['model'=>$filter,'action'=>'/web/calls/index']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            [
            	'attribute'	=>'key',
				'format'	=>'raw',
				'value'		=>function($data) use ($renderer) {
					return $renderer->render('item',['model'=>$data]);
				}
            ],
            'org_id',
            //'comment:ntext',
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

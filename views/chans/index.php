<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chans';
$this->params['breadcrumbs'][] = $this->title;
$renderer=$this;
?>
<div class="chans-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('table', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel,]); ?>


</div>

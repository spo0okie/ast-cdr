<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChanEventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ChanEvents';
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="chan-events-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('table', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel,]); ?>


</div>

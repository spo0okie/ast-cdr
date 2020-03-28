<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Chans */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Chans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="chans-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uuid',
            'name',
            //'type',
            'state',
            'src',
            'dst',
            'wasRinging',
            'reversed',
            'created_at',
        ],
    ]) ?>
	<?= $this->render('/chan-events/table', [
		'columns'=>['uid','created_at','data'],
		'dataProvider' => $dataProvider,
		'searchModel'=>null
	]); ?>

</div>

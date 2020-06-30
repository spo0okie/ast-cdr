<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Calls */

$this->title = $model->key;
$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="calls-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'key',
			'org_id',
			'source',
			'trunk',
            'comment:ntext',
        ],
    ]) ?>

	<?= \yii\bootstrap\Tabs::widget([
		'items'=>[
			[
				'label'=>'Каналы',
				//'active'=>true,
				'content' => $this->render('/chans/table',[
					'columns'=>['created_at','updated_at','name','state','smartSrc','smartDst'],
					'dataProvider'=>$chanDataProvider,
				]),
			],
			[
				'label'=>'Статусы',
				'active'=>true,
				'content' => $this->render('/call-states/table',[
					//'columns'=>['created_at','updated_at','name','state'],
					'dataProvider'=>$statesDataProvider,
				]),
			],
			[
				'label'=>'Канальные события',
				'content' => $this->render('/chan-events/table',[
					'dataProvider'=>$evtDataProvider,
				])
			],
		]
	]) ?>
</div>

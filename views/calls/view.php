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
            'comment:ntext',
        ],
    ]) ?>

	<h2>События</h2>
	<?php foreach ($model->events as $event) {
		echo $this->render('/events/item',['model'=>$event]).'<br />';
	}?>
</div>

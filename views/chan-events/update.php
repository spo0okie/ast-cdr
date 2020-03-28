<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ChanEvents */

$this->title = 'Update Chan Events: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Chan Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="chan-events-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

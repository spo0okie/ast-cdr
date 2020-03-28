<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Chans */

$this->title = 'Update Chans: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Chans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="chans-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

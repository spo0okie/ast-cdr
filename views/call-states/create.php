<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\callStates */

$this->title = 'Create Call States';
$this->params['breadcrumbs'][] = ['label' => 'Call States', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-states-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

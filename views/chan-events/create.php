<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ChanEvents */

$this->title = 'Create Chan Events';
$this->params['breadcrumbs'][] = ['label' => 'Chan Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chan-events-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

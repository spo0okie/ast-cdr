<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Chans */

$this->title = 'Create Chans';
$this->params['breadcrumbs'][] = ['label' => 'Chans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chans-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

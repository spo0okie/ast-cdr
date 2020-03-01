<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Orgs */

$this->title = 'Create Orgs';
$this->params['breadcrumbs'][] = ['label' => 'Orgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orgs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

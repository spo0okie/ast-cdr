<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Orgs */
/* @var $form yii\widgets\ActiveForm */

$schedules_list=app\models\Schedules::fetchNames();
$schedules_list['']='- не назначено -';
asort($schedules_list);


?>

<div class="orgs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'base_schedule_id')->dropDownList($schedules_list) ?>

    <?= $form->field($model, 'private_schedule_id')->dropDownList($schedules_list) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

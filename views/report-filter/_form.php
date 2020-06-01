<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 31.05.2020
 * Time: 18:42
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\callStates */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-filter-form">

    <?php $form = ActiveForm::begin([
    	'method'=>'get',
		'action'=>$action
	]); ?>

	<div class="row">
		<div class="col-md-3">
			<?= $form->field($model, 'date')->textInput() ?>
		</div>
		<div class="col-md-3">
			<?= $form->field($model, 'workTimeBegin')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-3">
			<?= $form->field($model, 'workTimeEnd')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-3">
			<?= $form->field($model, 'chanFilter')->textInput(['maxlength' => true]) ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?= $form->field($model, 'numInclude')->textarea() ?>
		</div>
		<div class="col-md-6">
			<?= $form->field($model, 'numExclude')->textarea() ?>
		</div>
	</div>







    <div class="form-group">
        <?= Html::submitButton('Сформировать', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
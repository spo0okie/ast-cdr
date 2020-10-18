<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;


/* @var $this yii\web\View */
/* @var $model app\models\SchedulesDays */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="schedules-days-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->schedule_id)
        echo $form->field($model, 'schedule_id')->hiddenInput()->label(false);
    else
        echo $form->field($model, 'schedule_id')->dropDownList(app\models\Schedules::fetchNames());
    ?>


    <?php if ($model->date)
        echo $form->field($model, 'date')->hiddenInput()->label(false);
    else {
        echo $form->field($model, 'date')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Введите дату / день...'],
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-mm-dd'
            ]
        ]);

        /*foreach (app\models\SchedulesDays::$days as $day=>$name) { ?>
                <span class="schedule-date-selector" onclick="$('#schedulesdays-date').val('<?= $day ?>')">
                <?= $name ?>
            </span> ::
            <?php }*/
    } ?>

    <?= $form->field($model, 'schedule')->textInput(['maxlength' => true]) ?>
    <p>
        Расписание должно быть вида ЧЧ:ММ-ЧЧ:ММ, например 8:00-16:30, или прочерк (минус) для
            <span class="schedule-date-selector" onclick="$('#schedulesdays-schedule').val('-')">
                выходного
            </span>

    </p>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

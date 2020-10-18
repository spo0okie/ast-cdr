<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orgs */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$renderer=$this;

?>
<div class="orgs-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Точно удалить организацию? Можно все сломать поспешив...',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <?= DetailView::widget([
        'model'=>$model,
        'attributes'=>[
            'code',
            'name',
            'comment:ntext',
            [
                'label' => 'Базовое расписание',
                'format' => 'raw',
                'value'=>function ($data) use ($renderer) {
                    return $renderer->render('/schedules/item',['model'=>$data->baseSchedule]);
                }
            ],
            [
                'label' => 'Индивидуальное(поправочное) расписание',
                'format' => 'raw',
                'value'=>function ($data) use ($renderer) {
                    return $renderer->render('/schedules/item',['model'=>$data->privateSchedule]);
                }
            ]
        ]
    ]) ?>

    <?php
        $weekAttr=[];
        $dateAttr=[];
        for ($i=0; $i<7; $i++) {
            $weekLabel=\app\models\SchedulesDays::$days[$i+1];
            $dateDay=date('Y-m-d',time()+86400*$i);
            $dateLabel='График работы на '.Yii::$app->formatter->asDate(time()+86400*$i,'full');
            $weekAttr[]=[
                'label' => $weekLabel,
                'format' => 'raw',
                'value'=> $this->render('/schedules-days/item',[
                    'model'=>$model->getWeekDaySchedule($i+1)
                ])
            ];
            $dateAttr[]=[
                'label' => $dateLabel,
                'format' => 'raw',
                'value'=> $this->render('/schedules-days/item',[
                    'model'=>$model->getDateSchedule($dateDay)
                ])
            ];
        }
    ?>

    <div class="row">
        <div class="col-md-4">
            <h2>Расписание на неделю</h2>
            <p>Без учета праздничных дней</p>
            <?= DetailView::widget(['model'=>$model,'attributes'=>$weekAttr]) ?>
        </div>
        <div class="col-md-8">
            <h2>Расписание на ближайшие 7 дней</h2>
            <p>С учетом праздничных дней</p>
            <?= DetailView::widget(['model'=>$model,'attributes'=>$dateAttr]) ?>
        </div>
    </div>

</div>

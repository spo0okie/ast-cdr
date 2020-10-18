<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Schedules */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Расписания', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="schedules-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Переименовать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /* Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */ ?>
    </p>

    <?php
        $attr=[
            'comment',
            //'created_at',
        ];
        foreach (app\models\SchedulesDays::$days as $day=>$name) {
            $attr[]=[
                'attribute'=>$name,
                //'header'=>$name,
                'format'=>'raw',
                'value'=>function ($data) use ($day,$name) {
                    if (is_object($sched=$data->findDay($day))) return
                        $sched->description.' '.
                        Html::a('Изменить', [
                            '/schedules-days/update',
                            'id' => $sched->id,
                        ], [
                            'class' => 'btn btn-primary btn-sm'
                        ]).' '.
                        Html::a('Удалить', [
                            '/schedules-days/delete',
                            'id' => $sched->id,
                        ], [
                            'class' => 'btn btn-danger btn-sm',
                             'data' => [
                                'confirm' => 'Удалить этот день в расписании?',
                                'method' => 'post',
                            ],
                        ]);
                    return (
                            $day=='def'?
                                'Не задано':
                                (
                                    is_object($sched=$data->findDay('def'))?$sched->description.' (по умолч.)':'На задано'
                                )
                        ).' '.

                        Html::a('Задать', [
                        '/schedules-days/create',
                        'schedule_id' => $data->id,
                        'date' => $day,
                    ], [
                        'class' => 'btn btn-primary btn-sm'
                    ]);
                },
                'contentOptions' => [
                    'class' => ($day==Yii::$app->request->get('date'))?'success':'',
                    'id'=>'day-'.$day
                ],
            ];
        }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attr
    ]) ?>



    <h2>Праздничные / внеочередные рабочие дни</h2>
    <?php
        $daysSearchModel = new app\models\SchedulesDaysSearch();
        $daysSearchModel->schedule_id = $model->id;
        $daysDataProvider = $daysSearchModel->search([]);

    ?>
    <?= GridView::widget([
        'dataProvider' => $daysDataProvider,
        'filterModel' => $daysSearchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'schedule_id',
            [
                'attribute'=>'date',
                'contentOptions' => function ($data) { return [
                    'class' => ($data->date==Yii::$app->request->get('date'))?'success':'',
                    'id'=>'day-'.$data->date
                ];},
            ],
            [
                'attribute'=>'schedule',
                'value'=>function($data){return $data->description;},
                'contentOptions' => function ($data) { return [
                    'class' => ($data->date==Yii::$app->request->get('date'))?'success':'',
                ];},
            ],
            [
                'attribute'=>'comment',
                'contentOptions' => function ($data) { return [
                    'class' => ($data->date==Yii::$app->request->get('date'))?'success':'',
                ];},
            ],

            ['class' => 'yii\grid\ActionColumn','template'=>'{update}{delete}'],
        ],
    ]); ?>

    <?= Html::a('Добавить праздничный / нестандартный рабочий день', [
        '/schedules-days/create',
        'schedule_id' => $model->id,
    ], [
        'class' => 'btn btn-success'
    ]);?>

</div>

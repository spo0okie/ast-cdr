<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "schedules".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $comment
 * @property string|null $created_at
 */
class Schedules extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'created_at' => 'Создано',
            'monEffectiveDescription' => 'Пон.',
            'tueEffectiveDescription' => 'Втр.',
            'wedEffectiveDescription' => 'Срд.',
            'thuEffectiveDescription' => 'Чтв.',
            'friEffectiveDescription' => 'Пят.',
            'satEffectiveDescription' => 'Суб.',
            'sunEffectiveDescription' => 'Вск.',
        ];
    }

    public function findDay($day) {
        return \app\models\SchedulesDays::findOne([
            'schedule_id'=>$this->id,
            'date'=>$day
        ]);
    }

    /**
     * Ищет эффективный день. если не задан конкретный - пытается сослаться на день по умолчанию
     * @param string $day
     * @return SchedulesDays|null
     */
    public function findEffectiveDay(string $day) {
        $schedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$this->id,
            'date'=>$day
        ]);
        if (is_object($schedule)) return $schedule;

        return \app\models\SchedulesDays::findOne([
            'schedule_id'=>$this->id,
            'date'=>'def'
        ]);
    }

    public function findEffectiveDescription($day) {
        if (is_object($schedule=$this->findEffectiveDay($day))) {
            return $schedule->description;
        } return 'не задано';
    }

    public function getMonEffectiveDescription() {
        return $this->findEffectiveDescription('1');
    }

    public function getTueEffectiveDescription() {
        return $this->findEffectiveDescription('2');
    }

    public function getWedEffectiveDescription() {
        return $this->findEffectiveDescription('3');
    }

    public function getThuEffectiveDescription() {
        return $this->findEffectiveDescription('4');
    }

    public function getFriEffectiveDescription() {
        return $this->findEffectiveDescription('5');
    }

    public function getSatEffectiveDescription() {
        return $this->findEffectiveDescription('6');
    }

    public function getSunEffectiveDescription() {
        return $this->findEffectiveDescription('7');
    }

    public static function fetchNames(){
        $list= static::find()
            ->select(['id','name'])
            ->all();
        return \yii\helpers\ArrayHelper::map($list, 'id', 'name');
    }

    /**
     * Ищет расписание на конкретную дату в формате ГГГГ-ММ-ДД
     * @param $schedule_id
     * @param $date
     * @return SchedulesDays|null
     */
    public static function getDaySchedule($schedule_id,$date)
    {
        //сначала ищем расписание прям на дату (если для этой даты отдельное расписание)
        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$schedule_id,
            'date'=>$date
        ]))) return $daySchedule;

        //выясняем день недели на эту дату
        $words=explode('-',$date);
        if (count($words)<3) return null; //ошибка. передана дата не в формате ГГГГ-ММ-ДД
        $weekday=date('N',mktime(0,0,0,$words[1],$words[2],$words[0]));

        //ищем расписание на этот день недели
        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$schedule_id,
            'date'=>$weekday
        ]))) return $daySchedule;

        //ищем по умолчанию, если вернет НУЛЛ значит и искать больше негде
        return \app\models\SchedulesDays::findOne([
            'schedule_id'=>$schedule_id,
            'date'=>'def'
        ]);
    }


    /**
     * Ищет график работы на конкретную дату в формате ГГГГ-ММ-ДД используя 2 расписания
     * @param $baseSchedule_id базовое расписание
     * @param $privSchedule_id поправочное расписание
     * @param $date дата в формате ГГГГ-ММ-ДД
     * @return SchedulesDays|null
     */
    public static function getWeekDay2Schedules($baseSchedule_id,$privSchedule_id,$weekday)
    {

        //ищем расписание на этот день недели
        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$privSchedule_id,
            'date'=>$weekday
        ]))) return $daySchedule;

        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$baseSchedule_id,
            'date'=>$weekday
        ]))) return $daySchedule;

        //ищем по умолчанию,
        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$privSchedule_id,
            'date'=>'def'
        ]))) return $daySchedule;

        //если вернет НУЛЛ значит и искать больше негде
        return \app\models\SchedulesDays::findOne([
            'schedule_id'=>$baseSchedule_id,
            'date'=>'def'
        ]);
    }
    /**
     * Ищет график работы на конкретную дату в формате ГГГГ-ММ-ДД используя 2 расписания
     * @param $baseSchedule_id базовое расписание
     * @param $privSchedule_id поправочное расписание
     * @param $date дата в формате ГГГГ-ММ-ДД
     * @return SchedulesDays|null
     */
    public static function getDay2Schedules($baseSchedule_id,$privSchedule_id,$date)
    {

        //если одного из расписаний нет, используем только второе

        if (is_null($baseSchedule_id)) return static::getDaySchedule($privSchedule_id,$date);
        if (is_null($privSchedule_id)) return static::getDaySchedule($baseSchedule_id,$date);

        //сначала ищем расписание прям на дату (если для этой даты отдельное расписание)
        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$privSchedule_id,
            'date'=>$date
        ]))) return $daySchedule;

        if (!is_null($daySchedule=\app\models\SchedulesDays::findOne([
            'schedule_id'=>$baseSchedule_id,
            'date'=>$date
        ]))) return $daySchedule;

        //выясняем день недели на эту дату
        $words=explode('-',$date);

        if (count($words)<3) return null; //ошибка. передана дата не в формате ГГГГ-ММ-ДД
        $weekday=date('N',(int)mktime(0,0,0, $words[1],$words[2],$words[0]));

        return static::getWeekDay2Schedules($baseSchedule_id,$privSchedule_id,$weekday);
    }
}

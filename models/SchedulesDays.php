<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "schedules_days".
 *
 * @property int $id
 * @property int|null $schedule_id
 * @property string|null $date
 * @property string|null $schedule
 * @property string|null $description
 * @property string|null $comment
 * @property string|null $day
 * @property string|null $created_at
 *
 * @property Schedules $master
 */
class SchedulesDays extends \yii\db\ActiveRecord
{

    public static $days=[
        'def' => "По умолч.",
        '1' => "Пон",
        '2' => "Втр",
        '3' => "Срд",
        '4' => "Чтв",
        '5' => "Птн",
        '6' => "Суб",
        '7' => "Вск",
    ];


    public function getDay() {
        if (isset(static::$days[$this->date])) return static::$days[$this->date];
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedules_days';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['schedule_id','date', 'schedule'], 'required'],
            [['schedule_id'], 'integer'],
            [['created_at'], 'safe'],
            [['date', 'schedule'], 'string', 'max' => 64],
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
            'schedule_id' => 'Расписание',
            'date' => 'День/Дата',
            'schedule' => 'Рабочее время',
            'description' => 'График',
            'comment' => 'Комментарий',
            'created_at' => 'Создано',
        ];
    }

    public function getMaster() {
        return \app\models\Schedules::findOne($this->schedule_id);
    }

    public function getDescription() {
        if ($this->schedule=='-') return 'Выходной день';
        return $this->schedule;
    }

}

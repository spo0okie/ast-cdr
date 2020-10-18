<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orgs".
 *
 * @property int $id id
 * @property int $base_schedule_id
 * @property int $private_schedule_id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $comment
 *
 * @property Schedules $baseSchedule
 * @property Schedules $privateSchedule
 */
class Orgs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orgs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment'], 'string'],
            [['base_schedule_id', 'private_schedule_id'], 'integer'],
            [['code'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Номер',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'base_schedule_id' => 'Базовое расписание',
            'private_schedule_id' => 'Индивидуальное расписание',
        ];
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLastIncoming()
	{
		return $this->hasOne(Calls::class, ['org_id'=>'code'])->where(['like','key','-IN-'])->orderBy(['id'=>SORT_DESC]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLastOutgoing()
	{
		return $this->hasOne(Calls::class, ['org_id'=>'code'])->where(['like','key','-OUT-'])->orderBy(['id'=>SORT_DESC]);
	}

    public static function fetchNames(){
        $list= static::find()
            ->select(['id','name'])
            ->all();
        return \yii\helpers\ArrayHelper::map($list, 'id', 'name');
    }

    public function getBaseSchedule() {
        return Schedules::findOne($this->base_schedule_id);
    }

    public function getPrivateSchedule() {
        return Schedules::findOne($this->private_schedule_id);
    }

    public function getWeekDaySchedule($wDay) {
        return \app\models\Schedules::getWeekDay2Schedules(
            $this->base_schedule_id,
            $this->private_schedule_id,
            $wDay
        );
    }

    public function getDateSchedule($date) {
        return \app\models\Schedules::getDay2Schedules(
            $this->base_schedule_id,
            $this->private_schedule_id,
            $date
        );
    }


}

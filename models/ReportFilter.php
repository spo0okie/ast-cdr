<?php
/**
 * Created by PhpStorm.
 * User: spookie
 * Date: 31.05.2020
 * Time: 18:35
 */

namespace app\models;

use yii\base\Model;

class ReportFilter extends Model
{
	public $date;
	public $workTimeBegin=8;
	public $workTimeEnd=17;
	public $innerInterval;
    public $numExclude;
    public $numInclude;
	public $statsInclude;
	public $statsName;
	public $chanFilter='SIP/';

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['date'], 'string', 'max' => 10],
			[['innerInterval'], 'boolean'],
			[['workTimeBegin','workTimeEnd'], 'integer', 'min'=>0,'max' => 24],
			[['chanFilter'], 'string', 'max' => 255],
			[['numExclude','numInclude','statsInclude','statsName'], 'safe'],
		];
	}

	public function attributeLabels()
	{
		return [
			'date' => 'Дата',
			'workTimeBegin' => 'Начало периода',
			'workTimeEnd' => 'Конец периода',
			'innerInterval' => 'Выбрать из интервала',
            'numInclude' => 'Исключить номера',
            'numExclude' => 'Исключить номера',
			'statsInclude' => 'Номера со статусами',
			'statsName' => 'Статус',
			'chanFilter' => 'Фильтр устройств',
		];
	}

	public function formData(){
		$request=[];
		foreach ($this->toArray() as $field=>$value) {
			$request["ReportFilter[$field]"]=$value;
		};
		return $request;
	}

	/*
     * Ограничивает поисковый запрос временем в течении суток (рабочий день)
     * Внутри периода или снаружи
     */
	static public function filterTimePeriod(\yii\db\ActiveQuery $query, \app\models\ReportFilter $filter_model) {

		if (empty($filter_model->workTimeBegin)||empty($filter_model->workTimeEnd)) return $query;

		if ($filter_model->innerInterval)	return $query
			->andWhere(['>=','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeBegin])
			->andWhere(['<','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeEnd]);
		else return $query
			->andWhere([
				'or',
				['>=','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeEnd],
				['<','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeBegin]
			]);
	}

	/*
 * Ограничивает поисковый запрос временем в течении суток (рабочий день)
 * Внутри периода или снаружи
 */
	static public function filterStates(\yii\db\ActiveQuery $query, \app\models\ReportFilter $filter_model)
	{

		if (empty($filter_model->statsInclude) || empty($filter_model->statsName)) return $query;

		return $query
			->andWhere(['call_states.state' => $filter_model->statsName])
			->andFilterWhere(['like', 'call_states.name', $filter_model->statsInclude, false]);
	}
}
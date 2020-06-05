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
	public $numExclude;
	public $numInclude;
	public $chanFilter='SIP/';

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['date'], 'string', 'max' => 10],
			[['workTimeBegin','workTimeEnd'], 'integer', 'min'=>0,'max' => 24],
			[['chanFilter'], 'string', 'max' => 255],
			[['numExclude','numInclude'], 'safe'],
		];
	}

	public function attributeLabels()
	{
		return [
			'date' => 'Дата',
			'workTimeBegin' => 'Начало периода',
			'workTimeEnd' => 'Конец периода',
			'numExclude' => 'Исключить номера',
			'numInclude' => 'Включить номера',
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
}
<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calls".
 *
 * @property int $id id
 * @property string|null $uuid
 * @property string|null $key
 * @property string|null $source
 * @property string|null $trunk
 * @property int|null $org_id
 * @property string|null $comment
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property \app\models\Events $events
 */
class Calls extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calls';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			[['org_id'], 'integer'],
			[['comment'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			['key', 'string', 'max' => 48],
			['key', 'unique'],
			[['uuid'], 'string', 'max' => 16]];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'org_id' => 'Org ID',
            'comment' => 'Comment',
        ];
    }

	/**
	 * @return array|\yii\db\ActiveRecord[]
	 */
	public function getEvents()
	{
		return \app\models\Events::find()
			->where(['call_id'=>$this->id])
			->orderBy('id')
			->All();
	}

	/**
	 * Источник вызова
	 * @return string
	 */
	public function getSource()
	{
		return explode('-',$this->key)[2];
	}

	/**
	 * CO вызова
	 * @return string
	 */
	public function getTrunk()
	{
		return explode('-',$this->key)[4];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStates()
	{
		return $this->hasMany(CallStates::class, ['call_id'=>'id']);
	}

	/**
	 * Предоставляет ID по ключу или UUID (находит или создает новый вызов)
	 * @param string $key
	 * @param null|string $org
	 * @return integer|null
	 */
	static public function provideCall($key=null,$org=null) {
		$call=\app\models\Calls::findOne(['key'=>$key]);

		//если нашли то и ОК
		if (!is_null($call)) {
			if (empty($call->org_id) && !empty($org) && mb_strlen($org)>3) {
				$call->org_id=mb_substr($org,3);
				$call->save();
			}
			return $call->id;
		}

		//иначе создаем новый
		$call=new \app\models\Calls();

		//прикручиваем ключ
		if (!empty($key)) $call->key=$key;
		if (!empty($org) && mb_strlen($org)>3) {
			$call->org_id=mb_substr($org,3);
		}
		//if (!empty($uuid)) $call->uuid=$uuid;
		if ($call->save()) return $call->getPrimaryKey();
		return null;
	}

	public function setState($num,$state,$event_id) {
		\app\models\CallStates::setState($this->id,$event_id,$num,$state);
	}
}

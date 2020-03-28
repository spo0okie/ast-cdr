<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calls".
 *
 * @property int $id id
 * @property string|null $uuid
 * @property string|null $key
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
			[['key'], 'string', 'max' => 48],
			[['uuid'], 'string', 'max' => 16],         ];
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
	 * Предоставляет ID по ключу или UUID (находит или создает новый вызов)
	 * @param string $uuid
	 * @param string $key
	 * @return integer|null
	 */
	static public function provideCall($key=null) {
		$call=\app\models\Calls::findOne(['key'=>$key]);

		//если нашли то и ОК
		if (!is_null($call)) return $call->id;

		//иначе создаем новый
		$call=new \app\models\Calls();

		//прикручиваем ключ
		if (!empty($key)) $call->key=$key;
		//if (!empty($uuid)) $call->uuid=$uuid;
		if ($call->save()) return $call->getPrimaryKey();
		return null;
	}

}

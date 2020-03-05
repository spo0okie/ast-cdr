<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calls".
 *
 * @property int $id id
 * @property string|null $key
 * @property int|null $org_id
 * @property string|null $comment
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
            [['key'], 'string', 'max' => 48],
        ];
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
	 * Предоставляет ID по ключу (находит или создает новый вызов)
	 * @param $key
	 * @return integer|null
	 */
	static public function provideCall($key) {
		$call=\app\models\Calls::findOne(['key'=>$key]);
		//если нашли то и ОК
		if (!is_null($call)) return $call->id;
		//иначе создаем новый
		$call=new \app\models\Calls();
		//прикручиваем ключ
		$call->key=$key;
		if ($call->save()) return $call->getPrimaryKey();
		return null;
	}

}

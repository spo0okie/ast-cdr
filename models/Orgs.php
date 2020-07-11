<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orgs".
 *
 * @property int $id id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $comment
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
            'code' => 'Code',
            'name' => 'Name',
            'comment' => 'Comment',
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

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_states".
 *
 * @property int $id
 * @property int|null $call_id
 * @property string|null $name
 * @property string|null $state
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class CallStates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_states';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['call_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'state'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'call_id' => 'Call ID',
            'name' => 'Name',
            'state' => 'State',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}

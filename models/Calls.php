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
}

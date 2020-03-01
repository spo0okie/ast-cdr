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
}

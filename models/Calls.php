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
 * @property int|false $length
 * @property string|null $comment
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property \app\models\Events[] $events
 * @property \app\models\CallStates $firstState
 * @property \app\models\CallStates $lastState
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
     * @return array|\yii\db\ActiveRecord
     */
    public function getFirstState()
    {
        return \app\models\CallStates::find()
            ->where(['call_id'=>$this->id])
            ->orderBy('id')
            ->One();
    }

    /**
     * @return array|\yii\db\ActiveRecord
     */
    public function getLastState()
    {
        return \app\models\CallStates::find()
            ->where(['call_id'=>$this->id])
            ->orderBy(['id'=>SORT_DESC])
            ->One();
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
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Orgs::class, ['code'=>'org_id']);
    }

    /**
     * Устанавливает организацию в звонке
     * @param null|string $org
     * @return bool
     */
    public function setOrg($org=null)
    {
        //если в звонке организации нет а в переданном параметре есть
        if (empty($this->org_id) && !empty($org)) {
            //если передали цифру - сохраняем цифру
            if (is_numeric($org)) {
                $this->org_id = $org;
                return true;
            //если строку длиннее 3х символов (orgXX)
            } elseif (mb_strlen($org)>3) {
                $this->org_id=mb_substr($org,3);
                return true;
            }
        }
        //иначе ничего не меняем
        return false;
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
			if ($call->setOrg($org)) $call->save();
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

	public function getAge() {
		$dt = new \DateTime(null, new \DateTimeZone(Yii::$app->params['timeZone'])); //first argument "must" be a string
		$age=$dt->getTimestamp() + $dt->getOffset() - strtotime($this->created_at);
		/*if ($age < 0) {
			return $dt->getTimestamp()+ $dt->getOffset() . ' - ' . strtotime($this->created_at). ' // '.date('r',$dt->getTimestamp()+ $dt->getOffset()).' - '.date('r',strtotime($this->created_at)).' // '.$dt->format('d.m.Y, H:i:s');
		}*/
		$days=floor($age/24/60/60);
		$age-=$days*24*60*60;
		$hours=floor($age/60/60);
		$age-=$hours*60*60;
		$minutes=floor($age/60);
		$age-=$minutes*60;
		$str=[];
		if ($days) $str[]=$days.'дн';
		if ($hours) $str[]=$hours.'ч';
		if ($minutes) $str[]=$minutes.'мин';
		$str[]=$age.'сек';
		return implode(' ',$str);// . ' //' . ($dt->getTimestamp()+ $dt->getOffset()) . ' - ' . strtotime($this->created_at);;
	}

	public function getLength() {
	    //if (!is_object($first=$this->firstEvent)) return 'first';
        //if (!is_object($last=$this->lastEvent)) return 'last';
        $first=$this->firstState;
        $last=$this->lastState;
        //return $first;
        return strtotime($last->created_at)-strtotime($first->created_at);
    }
}

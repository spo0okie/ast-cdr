<?php
/**
 * Элемент "событие"
 * User: spookie
 * Date: 07.03.2020
 * Time: 10:24
 */


/* @var $this yii\web\View */
/* @var $model app\models\Events */

switch ($model->type) {
	case 10:
		$text=$model->source.' ---&gt; '.$model->destination;
		break;
	case 20:
		$text=$model->source.' &lt;-^-&gt; '.$model->destination;
		break;
	case 30:
		$text=$model->source.' ---&gt; '.$model->trunk.' ---&gt; '.$model->destination;
		break;
	case 40:
		$text=$model->source.' --&gt; '.$model->trunk.' &lt;-^-&gt; '.$model->destination;
		break;
	case 50:
		$text=$model->source.' --&gt; '.$model->trunk.' --X--; '.$model->destination;
		break;
	case 60:
		$text=$model->source.' --X--; '.$model->destination;
		break;
}
echo $model->name.': '.$text;
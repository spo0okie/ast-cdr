<?php

namespace app\controllers;

use Yii;
use app\models\CallStates;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallStatesController implements the CRUD actions for callStates model.
 */
class CallStatesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all callStates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CallStates::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
     * Формирует поисковый запрос на основе стандартного фильтра
     */
    static public function searchQuery($filter_model){
		$query=\app\models\CallStates::find()
			->joinWith('event')
			->joinWith('call')
			->select([
				'name',
				'call_states.created_at',
				'DATE_FORMAT(call_states.created_at,"%Y-%m-%d") as date',
				'DATE_FORMAT(call_states.created_at,"%H") as hour',
				'COUNT(call_states.id) as count'
			])
			->where(['state'=>'Up'])
			->andWhere(['like','call_states.created_at',$filter_model->date.'%',false])
			->andWhere(['like','chan_events.channel',$filter_model->chanFilter.'%',false])
			->andFilterWhere(['like','call_states.name',$filter_model->numInclude,false]);
		if (strlen(trim($filter_model->numExclude)))
			$query->andFilterWhere(['not',['OR like','calls.key',explode(' ',$filter_model->numExclude)]]);
		$query
			->groupBy(['name','date'])
			->orderBy([
				'date'=>SORT_ASC,
				'name'=>SORT_ASC,
			]);
		return $query;
	}

	/*
	 * Ограничивает поисковый запрос временем в течении суток (рабочий день)
	 * Внутри периода или снаружи
	 */
	static public function searchTimePeriod(\yii\db\ActiveQuery $query, \app\models\ReportFilter $filter_model,$inner=true) {
		if ($inner)	return $query
			->andWhere(['>=','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeBegin])
			->andWhere(['<','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeEnd]);
		else return $query
			->andWhere([
				'or',
				['>=','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeEnd],
				['<','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeBegin]
			]);
	}

	/**
	 * Lists all callStates models.
	 * @return mixed
	 */
	public function actionShiftReport()
	{
		$filter_model = new \app\models\ReportFilter();
		$filter_model->date=date('Y-m');
		$filter_model->load(\Yii::$app->request->get());

		$queryDay=static::searchTimePeriod(
			static::searchQuery($filter_model),
			$filter_model
		);

		$queryNight=static::searchTimePeriod(
			static::searchQuery($filter_model),
			$filter_model,
			false
		);

		$dataProviderDay = new \yii\data\SqlDataProvider([
			'sql' => $queryDay
				->createCommand()
				->getRawSql(),
			'pagination' => ['pageSize' => 1000,],
		]);
		$dataProviderNight = new \yii\data\SqlDataProvider([
			'sql' => $queryNight
				->createCommand()
				->getRawSql(),
			'pagination' => ['pageSize' => 1000,],
		]);

		return $this->render('shift-report', [
			'filter' => $filter_model,
			'filter_action'=>'/web/call-states/shift-report',
			'dataProviderDay' => $dataProviderDay,
			'dataProviderNight' => $dataProviderNight,
		]);
	}

	/**
     * Displays a single callStates model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new callStates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new callStates();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing callStates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing callStates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the callStates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return callStates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = callStates::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

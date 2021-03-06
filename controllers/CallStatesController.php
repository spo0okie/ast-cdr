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
			->andWhere(['like','call_states.created_at',$filter_model->date.'%',false])
			->andWhere(['like','chan_events.channel',$filter_model->chanFilter.'%',false]);

		$query=\app\models\ReportFilter::filterTimePeriod($query,	$filter_model);

		$query=\app\models\ReportFilter::filterStates($query,	$filter_model);

		if (strlen(trim($filter_model->numExclude)))
			$query->andFilterWhere(['not',['OR like','calls.key',explode(' ',$filter_model->numExclude)]]);

		return $query
			->groupBy(['name','date'])
			->orderBy([
				'date'=>SORT_ASC,
				'name'=>SORT_ASC,
			]);

    }

	/**
	 * Lists all callStates models.
	 * @return mixed
	 */
	public function actionShiftReport()
	{
		$filter_day = new \app\models\ReportFilter();
		$filter_day->date=date('Y-m');
		$filter_day->load(\Yii::$app->request->get());
		$filter_day->innerInterval=1;

		$filter_night = new \app\models\ReportFilter();
		$filter_night->date=date('Y-m');
		$filter_night->load(\Yii::$app->request->get());
		$filter_night->innerInterval=0;

		$dataProviderDay = new \yii\data\SqlDataProvider([
			'sql' => static::searchQuery($filter_day)
				->createCommand()
				->getRawSql(),
			'pagination' => ['pageSize' => 1000,],
		]);
		$dataProviderNight = new \yii\data\SqlDataProvider([
			'sql' => static::searchQuery($filter_night)
				->createCommand()
				->getRawSql(),
			'pagination' => ['pageSize' => 1000,],
		]);

		return $this->render('shift-report', [
			'filter' => $filter_day,
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

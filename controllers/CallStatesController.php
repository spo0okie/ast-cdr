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

	/**
	 * Lists all callStates models.
	 * @return mixed
	 */
	public function actionShiftReport()
	{
		$filter_model = new \app\models\ReportFilter();
		$filter_model->date=date('Y-m');
		$filter_model->workTimeBegin=8;
		$filter_model->workTimeEnd=20;
		$filter_model->chanFilter='SIP/';
		$filter_model->load(\Yii::$app->request->get());

		$queryDay=\app\models\CallStates::find()
			->joinWith('event')
			->joinWith('call')
			->select([
				'name',
				'call_states.created_at',
				'DATE_FORMAT(call_states.created_at,"%Y-%m-%d") as date',
				//'DATE_FORMAT(call_states.created_at,"%d") as day',
				'DATE_FORMAT(call_states.created_at,"%H") as hour',
				'SUM(1) as count'
			])
			->where(['state'=>'Up'])
			->andWhere(['like','call_states.created_at',$filter_model->date.'%',false])
			->andWhere(['like','chan_events.channel',$filter_model->chanFilter.'%',false])
			->andWhere(['like','call_states.name',$filter_model->numInclude,false])
			->andWhere(['not',['OR like','calls.key',explode(' ',$filter_model->numExclude)]])
			->groupBy(['name','date'])
			->orderBy([
				'date'=>SORT_ASC,
				'name'=>SORT_ASC,
			]);

		$queryNight=clone $queryDay;
		$dataProviderDay = new \yii\data\SqlDataProvider([
			'sql' => $queryDay
				->andWhere(['>=','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeBegin])
				->andWhere(['<','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeEnd])
				->createCommand()
				->getRawSql(),
			'pagination' => ['pageSize' => 1000,],
		]);
		$dataProviderNight = new \yii\data\SqlDataProvider([
			'sql' => $queryNight
				->andWhere([
					'or',
					['>=','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeEnd],
					['<','DATE_FORMAT(call_states.created_at,"%H")',(int)$filter_model->workTimeBegin]
				])
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

<?php

namespace app\controllers;

use Yii;
use app\models\Calls;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallsController implements the CRUD actions for Calls model.
 */
class CallsController extends Controller
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
     * Lists all Calls models.
     * @return mixed
     */
    public function actionIndex()
    {
		$filter_model = new \app\models\ReportFilter();
		$filter_model->load(\Yii::$app->request->get());

		$query=Calls::find()
			->joinWith('states.event');

        $totalQuery=Calls::find()
            ->select('COUNT(DISTINCT(calls.id))')
            ->joinWith('states.event');

		if (strlen($filter_model->date)) {
            $query->andWhere(['like','calls.created_at',$filter_model->date.'%',false]);
            $totalQuery->andWhere(['like','calls.created_at',$filter_model->date.'%',false]);
        }

        if (strlen($filter_model->chanFilter))	{
            $query->andWhere(['like','chan_events.channel',$filter_model->chanFilter.'%',false]);
            $totalQuery->andWhere(['like','chan_events.channel',$filter_model->chanFilter.'%',false]);
        }

        if (strlen($filter_model->numInclude))  {
            $query->andWhere(['like','calls.key',$filter_model->numInclude]);
            $totalQuery->andWhere(['like','calls.key',$filter_model->numInclude]);
        }
		$query->groupBy(['calls.id']);



		$query=\app\models\ReportFilter::filterTimePeriod($query,$filter_model);
		$totalQuery=\app\models\ReportFilter::filterTimePeriod($totalQuery,$filter_model);

		$query=\app\models\ReportFilter::filterStates($query,$filter_model);
		$totalQuery=\app\models\ReportFilter::filterStates($totalQuery,$filter_model);

		if (strlen(trim($filter_model->numExclude))) {
			$query->andFilterWhere(['not',['OR like','calls.key',explode(' ',$filter_model->numExclude)]]);
			$totalQuery->andFilterWhere(['not',['OR like','calls.key',explode(' ',$filter_model->numExclude)]]);
		}

		$dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => ['pageSize' => 100,],
			'totalCount' => $totalQuery->scalar()
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
			'filter' => $filter_model
        ]);
    }

    /**
     * Displays a single Calls model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $call=$this->findModel($id);
        if (empty($call->org_id) && $call->fixOrg()) $call->save(false);

        return $this->render('view', [
            'model' => $this->findModel($id),
			'evtDataProvider' => new ActiveDataProvider([
				'query' => \app\models\ChanEvents::find()
					->joinWith('chan')
					->where(['chans.call_id' => $id]),
				'pagination' => ['pageSize' => 100,],
			]),
			'chanDataProvider' => new ActiveDataProvider([
				'query' => \app\models\Chans::find()
					->where(['call_id' => $id]),
				'pagination' => ['pageSize' => 100,],
			]),
			'statesDataProvider' => new ActiveDataProvider([
				'query' => \app\models\CallStates::find()
					->where(['call_id' => $id]),
				'pagination' => ['pageSize' => 100,],
			]),
		]);
    }

    /**
     * Creates a new Calls model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Calls();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Calls model.
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
     * Deletes an existing Calls model.
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
     * Finds the Calls model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Calls the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Calls::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

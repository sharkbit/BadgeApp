<?php

namespace backend\controllers;

use Yii;
use backend\models\RuleList;
use backend\models\search\RuleListSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\AdminController;

/**
 * RuleListController implements the CRUD actions for RuleList model.
 */
class RulesController extends AdminController {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

	public function actionCreate() {
		$model = new RuleList();
        if ($model->load(Yii::$app->request->post())){
			$model->rule_abrev = trim($model->rule_abrev);
			$model->rule_name = trim($model->rule_name);
			
			if ($model->save()) {
				$this->createLog($this->getNowTime(), $_SESSION['user'], 'Added Rule: '.$model->rule_abrev);
				Yii::$app->getSession()->setFlash('success', 'Rule '.$model->rule_abrev.' has been Added');
				return $this->redirect(['view', 'id' => $model->id]);
			} else {
				Yii::$app->getSession()->setFlash('error', 'Something Went Wrong?');
				return $this->render('create', [
					'model' => $model,
				]);
			}
		} else { 
			return $this->render('create', [
				'model' => $model,
			]);
		}
    }

	public function actionIndex() {
		$searchModel = new RuleListSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    public function actionUpdate($id=1) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
			$model->rule_abrev = trim($model->rule_abrev);
			$model->rule_name = trim($model->rule_name);
        	$model->save();
            return $this->redirect(['view', 'id' => $model->id]);  
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id) {
        if (($model = RuleList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

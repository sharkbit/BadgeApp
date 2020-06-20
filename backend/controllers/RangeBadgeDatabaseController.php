<?php

namespace backend\controllers;

use Yii;
use backend\models\BadgesDatabase;
use backend\models\search\BadgesDatabaseSearch;
use backend\controllers\SiteController;
use backend\controllers\BadgesController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RangeBadgeDatabaseController implements the CRUD actions for BadgesDatabase model.
 */
class RangeBadgeDatabaseController extends SiteController {
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

    public function actionIndex() {
        $searchModel = new BadgesDatabaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($badge_number) {
		$model = BadgesDatabase::find()->where(['badge_number'=>$badge_number])->one();
        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionUpdate($badge_number) {
		$model = BadgesDatabase::find()->where(['badge_number'=>$badge_number])->one();
		
		if ($model->load(Yii::$app->request->post())) {
			BadgesController::cleanBadgeData($model);

			if ($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Badge Updated');
				$this->createLog($this->getNowTime(), $this->getActiveUser()->username, 'Badges Database Updated : '.$model->badge_number);
				return $this->redirect(['view', 'badge_number' => $model->badge_number]);
			} else {
				Yii::$app->getSession()->setFlash('error', 'Failed to Update badge #'.$model->badge_number);
			}
        } 
		return $this->render('update', [
			'model' => $model,
		]);
    }

    public function actionDelete($id) {
		BadgesController::actionDelete($id,'admin');
		
        //Yii::$app->getSession()->setFlash('error', 'Delete function in Badge View');
        return $this->redirect(['index']);
    }

    protected function findModel($badge_number) {
        if (($model = BadgesDatabase::findOne($badge_number)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

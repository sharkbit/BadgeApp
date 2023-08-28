<?php

namespace backend\controllers;

use Yii;
use backend\models\Legelemail;
use backend\models\search\LegelemailSearch;
use backend\models\search\LegalGroupsSearch;
//use yii\web\Controller;
//use yii\web\NotFoundHttpException;
//use yii\filters\VerbFilter;
use backend\controllers\AdminController;

/**
 * ParamsController implements the CRUD actions for Params model.
 */
class LegelemailController extends AdminController {

    public function actionCreate() {
        $model = new Legelemail();
		if ($model->load(Yii::$app->request->post())) {
			$model->date_created = $this->getNowTime();
			$model->date_modified = $model->date_created;
			$model->display_order=999;
        	if($model->save()) {
				$this->UpdateGroups($model->contact_id,$model->groups);
				Yii::$app->getSession()->setFlash('success', 'Record Updated');
				return $this->render('update', [ 'model' => $model, ]);
			} else {
				Yii::$app->getSession()->setFlash('error', 'Record Failed to save');
			}
		}
		return $this->render('create', [ 'model' => $model, ]);
    }

	public function actionDelete($id=1) {
		$model = $this->findModel($id);
		if($model->delete()) {
			Yii::$app->getSession()->setFlash('success', 'Record Deleted');
		} else {
			
		}
		return $this->redirect('index');
	}

	public function actionGroups() {
		$searchModel = new LegalGroupsSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('groups', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

    public function actionUpdate($id=1) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
			$model->date_modified = $this->getNowTime();
			if($model->save()) {
				Yii::$app->getSession()->setFlash('success', 'Record Updated');
				$this->UpdateGroups($model->contact_id,$model->groups);
			} else {
				Yii::$app->getSession()->setFlash('error', 'Record did not update?');
			}
        }
        return $this->render('update', [
                'model' => $model,
            ]);
    }

    public function actionIndex() {
		$searchModel = new LegelemailSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		//fileImport
		
	   $model = new \yii\base\DynamicModel([
				'fileImport'=>'File Import',
			]);
		//$model = new Legelemail;
		$model->addRule(['fileImport'],'required');
		$model->addRule(['fileImport'],'file',['extensions'=>'ods,xls,xlsx']);
		return $this->render('index', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }

    protected function findModel($id) {
        if (($model = Legelemail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	function UpdateGroups($id,$groups){
		$sql = "DELETE FROM associat_agcnew.contact_groups WHERE contact_id=".$id;
		Yii::$app->db->createCommand($sql)->execute();

		$myGroups='';
		foreach($groups as $g_id){
			$myGroups .= "($g_id,$id),";
		}
		$sql = "INSERT INTO associat_agcnew.contact_groups (group_id,contact_id) VALUES ".rtrim($myGroups, ',');
		yii::$app->controller->createLog(false, 'trex-sql', var_export($sql ,true));
		Yii::$app->db->createCommand($sql)->execute();
	}
}
?>
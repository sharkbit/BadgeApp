<?php

namespace backend\controllers;

use Yii;
use backend\models\Legelemail;
use backend\models\search\LegelemailSearch;
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

	public function actionImport() {
	//	yii::$app->controller->createLog(false, 'trex_File',var_export($_FILES['Legelemail'],true));
		//yii::$app->controller->createLog(false, 'trex', var_export($_POST,true));
	//	echo  var_export($_POST,true);


	   $modelImport = new \yii\base\DynamicModel([
				'fileImport'=>'File Import',
			]);
		$modelImport->addRule(['fileImport'],'required');
		$modelImport->addRule(['fileImport'],'file',['extensions'=>'ods,xls,xlsx']);
		
		if(Yii::$app->request->post()){
            $modelImport->fileImport = \yii\web\UploadedFile::getInstance($modelImport,'fileImport');
            if($modelImport->fileImport && $modelImport->validate()){
				echo "horray 2x";
				 $inputFileType = \PHPExcel_IOFactory::identify($modelImport->fileImport->tempName);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				echo "woot toot";
			}
		}
exit;

        if(yii::$app->request->post()) {
            //$activeUser = $this->getActiveUser();
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time',6000000000);
            $Legelemails = $_FILES['Legelemail']['tmp_name']['fileImport'];
            $objPHPExcel = new \PHPExcel();
            $items = Excel::import($Legelemails);

            $validateLegelemails = $this->validateExcell('Legelemail',$items);
            if($validateLegelemails=='false') {
                $responce = [
                    'status'    =>  'error-file',
                    'remarks'   =>  'Not a valid file. Please click the Download Sample link below for more reference.',
                ];

                return json_encode($responce,true);
            }

// ### Drop Tables ###############################
// 
//TRUNCATE associat_agcnew.contacts;
//TRUNCATE associat_agcnew.contact_groups;

// -- Import New Contacts

            $errorArrayNotExist = [];
            $corrupted = [];
            $successful = 0;
            foreach ($items as $key => $item) {
                if(isset($item['badgenum']) && $item['badgenum']!=null) {
                    $isExist = $this->badgeIsExist($item['badgenum']);
                    if($isExist==true) {
                        $LegelemailsModel = new Legelemails();
                        $LegelemailsModel->badge_number = $item['badgenum'];
                        $LegelemailsModel->work_date = date('Y-m-d',strtotime($item['workdate']));
                        $LegelemailsModel->work_hours = $item['workhours'];
                        $LegelemailsModel->project_name = $item['project'];
                        $LegelemailsModel->status = '0';
                        $LegelemailsModel->remarks = $item['remarks']!=null ? $item['remarks']: '-blank-';
                        $LegelemailsModel->authorized_by = $item['auth'];
                        $LegelemailsModel->created_at = $this->getNowTime();
                        $LegelemailsModel->updated_at = $this->getNowTime();
                        $LegelemailsModel->created_by = $_SESSION['badge_name'];
                        if($LegelemailsModel->save()) {
                            $badge = Badges::find()->where(['badge_number'=>$LegelemailsModel->badge_number])->one();
                            $badge->work_credits = $badge->work_credits + $LegelemailsModel->work_hours;
                            if($badge->save(false)) {
                                $LegelemailsModel->status = '1';
                                $LegelemailsModel->save();
                             }
                        $successful++;

                        }
                    }
                    else {
                        $errorArrayNotExist [] = $item;
                    }

                }
                else {
                    $corrupted [] = $item;
                }

            }



// ### Process Groups ####################
/*

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 1, contact_id FROM associat_agcnew.contacts WHERE title = "Senator";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 2, contact_id FROM associat_agcnew.contacts WHERE title = "Delegate";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 3, contact_id FROM associat_agcnew.contacts WHERE committee = "EHEA";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 4, contact_id FROM associat_agcnew.contacts WHERE committee = "E&T";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 5, contact_id FROM associat_agcnew.contacts WHERE committee = "JPR";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 6, contact_id FROM associat_agcnew.contacts WHERE committee = "JUD";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 7, contact_id FROM associat_agcnew.contacts WHERE email like '%state.md.us%';

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 8, contact_id FROM associat_agcnew.contacts WHERE committee = "W&M";

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 9, contact_id FROM associat_agcnew.contacts WHERE committee = "HGO";
   
INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 16, contact_id FROM associat_agcnew.contacts WHERE email like '%baltimorecity.gov%';

INSERT INTO associat_agcnew.contact_groups (group_id, contact_id)
   SELECT 17, contact_id FROM associat_agcnew.contacts WHERE email like '%baltimorecountymd.gov%';
   */
            $responce = [
                'status' => 'success',
                'errorArrayNotExist' => count($errorArrayNotExist),
                'corrupted' => count($corrupted),
                'successful' => $successful,
            ];

            return json_encode($responce,true);

        }
        else {
			Yii::$app->getSession()->setFlash('error', 'No Post Data');
            return $this->redirect(['index']);
        }
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
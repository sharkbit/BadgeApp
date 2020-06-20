<?php
use backend\models\StoreItems;

    $QueryAll = $dataService->Query('SELECT * FROM Item'); // where Type ="Inventory"');
    $error = $dataService->getLastError();
    if ($error) {
        echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
        echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
        echo "The Response message is: " . $error->getResponseBody() . "\n";
    } else {

		if (!$QueryAll || (0==count($QueryAll))) {
			echo "No Records Found";
		} else {
			yii::$app->controller->createLog(true, $_SESSION['user'], "** Updating SKU Numbers! **");
			//  Truncate Store_Item table
			echo "Truncated table: Store_Item<hr>\n";
			$sql='TRUNCATE store_items';
			$connection = Yii::$app->getDb();
			$command = $connection->createCommand($sql);
			$exec = $command->execute();
			if ($exec) { var_dump($exec); }

			echo "Rec count: " .count($QueryAll)."<br />";
			$cnt=0;
			echo "<table border=1><tr><th>id</th><th>name</th><th>sku</th><th>up</th><th>type</th><th>parent</th></tr>";
			foreach ($QueryAll as $one) {
			//if($one->Sku) {
				echo "<tr><td>".$one->Id. "</td><td>".$one->Name.
					"</td><td>".$one->Sku.
					"</td><td>".$one->UnitPrice.
					"</td><td>".$one->Type.
					"</td><td>".$one->ParentRef.
					"</td>";

				//$model = new Badges();
				$model = new StoreItems();
				$model->item_id = $one->Id;
				$model->item=$one->Name;
				$model->sku= $one->Sku;
				$model->price=$one->UnitPrice;
				$model->type=$one->Type;
				$model->paren=$one->ParentRef;
				if($model->save()) {
				//	echo "Saved: ".$one->Id."<br/>";
				} else {
					echo "Failed: ".var_dump($model->errors)."<br/>";
				}
				$cnt ++;
				$sku_sql=false;
				switch ($one->Name) {
					case "Half Year Individual Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_half='.$one->Sku.' where membership_id=50'; break;
					case "Half Year Family Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_half='.$one->Sku.' where membership_id=51'; break;
					case "Half Year Junior Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_half='.$one->Sku.' where membership_id=52'; break;
					case "Full Year Individual Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_full='.$one->Sku.' where membership_id=50'; break;
					case "Full Year Family Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_full='.$one->Sku.' where membership_id=51'; break;
					case "Full Year Junior Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_full='.$one->Sku.' where membership_id=52'; break;
					case "15 Year Special Dues": $sku_sql= 'UPDATE BadgeDB.fees_structure set sku_half='.$one->Sku.',sku_full='.$one->Sku.' where membership_id=70'; break;
				}
				if($sku_sql){
					$command = $connection->createCommand($sku_sql);
					$exec = $command->execute();
				}
			}
			echo "</table> Recieved: ".$cnt."<br />";
		}
	}

	echo "Fin <br />\n";

?>
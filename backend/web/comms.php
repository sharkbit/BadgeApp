<?php
// comms.php - Will be used to talk to/from (agc.net <- -> badge.agc.com)

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* in .Htaccess
<Files \comms.php>
  <RequireAny>
	Require ip 72.170.251.9
	Require ip 96.234.172.160/29
  </RequireAny>
  <IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
  </IfModule>
</Files>  */

// CORS-compliant method
// Allow from any origin
header('Access-Control-Allow-Origin: *');

// -if (isset($_SERVER['HTTP_ORIGIN'])) {
	// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
	// you want to allow, and if so:
//-	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//-	header('Access-Control-Allow-Credentials: true');
//-	header('Access-Control-Max-Age: 86400');    // cache for 1 day
//-}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		// may also be using PUT, PATCH, HEAD etc
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}

function GetTestResults($email,$test_id) {
	global $mysqli;
	$sql = "SELECT statistic_ref_id, create_time, form_data, ".
		"(SELECT sum(correct_count) FROM wp_wp_pro_quiz_statistic as wp WHERE wp.statistic_ref_id=wp_ref.statistic_ref_id) as cor, ".
		"(SELECT count(*) FROM wp_wp_pro_quiz_statistic as wp WHERE wp.statistic_ref_id=wp_ref.statistic_ref_id) as cnt ".
		"FROM wp_wp_pro_quiz_statistic_ref as wp_ref ".
		"WHERE quiz_id=".$test_id." AND form_data like '%".$email."%' ORDER BY create_time DESC";
	//echo "<hr>".$sql."<hr>";

	$qurey = $mysqli->query($sql);
	if($mysqli->affected_rows>0) {$stat='success';} else {$stat='error';}
	//printf("%d records found.<br />\n", $mysqli->affected_rows);
	$data = new stdClass();
	$allData = [];
	foreach($qurey as $row) {
		$data->score = round(((int)$row['cor'] / (int)$row['cnt']) * 100)."%";
		$data->testdate = $row['create_time'];         //date("d/m/Y H:i:s",$row['create_time']);
		$data->right = $row['cor']." of ".$row['cnt'];

		$form_data = json_decode($row['form_data']);
		if( $test_id == 1 ) {
			$data->name = $form_data->{'1'};
			$data->email = $form_data->{'2'};
			$data->badge = $form_data->{'3'};
		} elseif( $test_id == 2 ) {
			$data->name = $form_data->{'4'};
			$data->email = $form_data->{'5'};
			$data->badge = $form_data->{'6'};
		} elseif( $test_id == 3 ) {
			$data->name = $form_data->{'7'};
			$data->email = $form_data->{'8'};
			$data->badge = $form_data->{'9'};
		} elseif( $test_id == 4 ) {
			$data->name = $form_data->{'10'};
			$data->email = $form_data->{'11'};
			$data->badge = $form_data->{'12'};
		}

		$allData[] = json_decode(json_encode($data),true);
	}

	// echo "<hr />\n";
	// echo var_export( json_encode($allData) );
	return json_encode(['status'=>$stat,'data'=>$allData]);
}

function GetOnlinePaymnets($badge_number) {
	global $mysqli;
	$sql = "SELECT * FROM associat_gunclubsnew.wp_postmeta".
		" WHERE post_id in (SELECT post_id FROM associat_gunclubsnew.wp_postmeta where meta_value like '%".$badge_number."%' and meta_key like 'WC_billing_field%')".
		" and (meta_key = '_paid_date' or meta_key = '_billing_first_name' or meta_key = '_billing_last_name' or meta_key = '_order_total') ".
		" order by post_id";

	$qurey = $mysqli->query($sql);
	if($mysqli->affected_rows>0) {$stat='success';} else {$stat='error';}

	$allData = []; $i=-1; $last='';

	foreach($qurey as $row) {
		if ($last != $row['post_id']) { $i++; $last=$row['post_id']; }
		switch ($row['meta_key']) {
			case '_paid_date': 			@$allData[$i]->tx_date = $row['meta_value']; break;
			case '_order_total': 		@$allData[$i]->total   = $row['meta_value']; break;
			case '_billing_first_name': @$allData[$i]->f_name = $row['meta_value']; break;
			case '_billing_last_name': 	@$allData[$i]->l_name = $row['meta_value']; break;
		}
	}
	return json_encode(['status'=>$stat,'data'=>$allData]);

}

function DumpDatabase($tbl_search,$tbl_rows=10) {
	global $mysqli;
	$sql = "show databases";

    $dbResult = $mysqli->query($sql);
	if($dbResult) {
		printf("%d databases found.<br />\n", $mysqli->affected_rows);
		foreach($dbResult as $row) {
			$currDB = $row['Database'];
			if($currDB <>'information_schema') {

				print_r("<b><h2> -> ".$currDB."</h2></b><br />\n");
				$mysqli->select_db($currDB);
				$sql = "show tables";

				$result = $mysqli->query($sql);
				if($result) {
					printf("%d Tables found.<br />\n", $mysqli->affected_rows);

					foreach($result as $row) {
						print_r(" - ".$row['Tables_in_'.$currDB]."<br />\n");

						if(strpos($row['Tables_in_'.$currDB],$tbl_search)) {
							$sql_tbl = "Select * from ".$row['Tables_in_'.$currDB]; //." limit 20";
							echo "SQL: ".$sql_tbl."<br />\n";
							$tbl = $mysqli->query($sql_tbl);
							if ($tbl) {PrintTable($tbl,$tbl_rows);}
							else {printf("Error: %s<br />\n", $mysqli->error);}
						}
					}
					$result->close();
				} else {
					printf("Error: %s<br />\n", $mysqli->error);
				}
			}
			echo "<hr />BreakBreakBreak<hr />\n";
		}
		$dbResult->close();
	} else {
		printf("Error: %s<br />\n", $mysqli->error);
    }
}

function PrintTable($tbl,$limit=2) {
	if($tbl->num_rows === 0) {
		echo "No results<br />\n";
    } else {
		echo "Found ".$tbl->num_rows." records.<br />\n<table border=1>\n<thead>\n<tr>\n<th></th>";

		$row = $tbl->fetch_assoc();
		foreach ($row as $col => $value) {
			echo "<th>";
			echo $col;
			echo "</th>";
		}
		echo "</tr>\n</thead>\n<tbody>\n";

		// Write rows
		$tbl->data_seek(0);
		$cnt = 0;
		while ($row = $tbl->fetch_assoc()) {
			$cnt = $cnt+1; if($cnt>$limit) {break;}
			echo "<tr>\n<td>$cnt</td>";

			foreach($row as $key => $value){
				echo "<td>";
				echo $value;
				echo "</td>";
			}
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
	}
}

require_once('../../comms_constants.php');

if(isset($_GET['verifyemail'])) {
	$email=$_GET['verifyemail'];
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

	echo "<!DOCTYPE html>\n<html lang='en-US'>".PHP_EOL .
		"<head><title>AGC Email Validation</title></head>".PHP_EOL .
		"<body><center><br /><br /><h3>Thank you for validating your email.</h3>\n".
		"<br /><a href='".WP_SITE."/'>Return to AGC<br/>\n".
		"<img src='/agc/images/AGC.gif' /></a>";

	// Run Command on Badge server
	$command = "wget -qO- '".BADGEAPP_SITE."/site/verify?email=".$email."'";
	exec('nohup ' . $command . ' > /dev/null 2>&1 &');

	// Run Command on tmp server
	$command = "wget -qO- '".TMP_SITE."/site/verify?email=".$email."'";
	exec('nohup ' . $command . ' > /dev/null 2>&1 &');
	}
}

elseif(isset($_GET['unsubscribe'])) {
	$email=$_GET['unsubscribe'];
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Run Command on Badge server
		$command = "wget -qO- '".BADGEAPP_SITE."/site/no-email?unsubscribe=".$email."'";
		exec('nohup ' . $command . ' > /dev/null 2>&1 &');

		// Run Command on tmp server
		$command = "wget -qO- '".TMP_SITE."/site/no-email?unsubscribe=".$email."'";
		exec('nohup ' . $command . ' > /dev/null 2>&1 &');

		echo "<!DOCTYPE html>\n<html lang='en-US'>".PHP_EOL;
		echo "<head><title>AGC Unsubscribe</title></head>".PHP_EOL;
		echo "The Associated Gun Clubs of Baltimore will miss you!<br />".
		"Your email address: ".$email." will be removed promptly.<br /><br />".
		"<a href='".WP_SITE."/'>The AGC</a></html>";
	} else { echo " The Email you entered is invalid."; }
	echo "<br /> Good Bye.";
}

else {
	// Only Allow AGC Web Servers Below
	if((strpos(" ".$_SERVER['REMOTE_ADDR'],"96.234.172.16")) ||
		(strpos(" ".$_SERVER['REMOTE_ADDR'],"72.170.251.9")) ||
	   	(strpos(" ".$_SERVER['REMOTE_ADDR'],"71.127.151.82")) ||
		(strpos(" ".$_SERVER['REMOTE_ADDR'],"2001:470:5:a64"))) {

	$mysqli = new mysqli('localhost',DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME);
	if(!$mysqli){ die("ERROR: Could not connect. " . $mysqli->connect_error); }

	//echo "koay";

	if (isset($_GET['email']) && $_GET['email'] <> '' && $_GET['id'] <> '') {  // ./comms.php?email=shar&id=1
		$email = $_GET['email'];  	//'sharkbit@hotmail.com';
		$test_id = $_GET['id'];		// 1=Holster, 2=Quiz, 3=Steal, 4=CIO
		echo GetTestResults($email,$test_id);
		exit;
	}

	elseif (isset($_GET['online']) && $_GET['online'] <> '') {  // ./comms.php?online=1174
		$online = $_GET['online'];  	//'sharkbit@hotmail.com';
		echo GetOnlinePaymnets($online);
		exit;
	}

	elseif (isset($_GET['search']) && $_GET['search'] <> '' ) {		// ./comms.php?search=woo&rows=15
		if(isset($_GET['rows']) && $_GET['rows'] > 0 ) { $myRows=$_GET['rows']; } else {$myRows=10; }
		DumpDatabase($_GET['search'],$myRows);

	}
	elseif (isset($_GET['CheckBadge']) && $_GET['CheckBadge'] <> '' ) {
		$mysqli->select_db("BadgeDB");

		$sql = "SELECT * FROM badges WHERE badge_number=".$_GET['CheckBadge'];
		$result = $mysqli->query($sql);
		if($result) {
			echo '{"status":"success"}';
		} else {
			echo '{"status":"error","err":"'.$mysqli->error.'"}';
		}

	} else {
		$mysqli->select_db("associat_gunclubsnew");
//echo "adsf";
//exit; } } } /
		//$sql =" Select count(*),post_type from wp_posts ".
		//	"GROUP BY post_type";

		$sql =" Select * from wp_posts ".
			"WHERE post_type = 'shop_order' ".
			"ORDER BY post_date desc";

		$sql = "SELECT * FROM wp_postmeta ".
			"INNER JOIN wp_posts ON wp_posts.ID=wp_postmeta.post_id ".
			"WHERE wp_posts.post_type ='shop_order' ".
			"ORDER BY meta_id DESC";

		$sql = "SELECT * from wp_postmeta ".
			"INNER JOIN wp_posts ON wp_posts.ID=wp_postmeta.post_id ".
			//"WHERE meta_key='_billing_address_index' ".
			"WHERE meta_value like '%Ernal%' ".
			"ORDER BY meta_id DESC";

	//	$sql = "SELECT count(*), meta_value ".
	//		"FROM wp_postmeta ".
			//"WHERE meta_key='_recorded_sales' ".
	//		"WHERE meta_key='_wc_intuit_qbms_charge_captured' ".
	//		"GROUP BY meta_value";

		$sql =" Select count(*),post_excerpt from wp_posts ".
			//"WHERE post_type = 'shop_order' ".
			"GROUP BY post_excerpt ".
			"ORDER BY post_date desc";


		echo $sql."<hr /> \n";
		$qurey = $mysqli->query($sql);
		if($qurey) { PrintTable($qurey,200); }
		else {echo "error: ".$mysqli->error; }

	}

	if($mysqli) { $mysqli->close(); }

	//phpinfo();
	} else { header('Location: /'); }
}
?>

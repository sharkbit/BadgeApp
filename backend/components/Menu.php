<?php
namespace backend\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/* Menu Colors:
	btn-info	= Light Blue
 ** btn-primary = Blue 		-> Default
	btn-success	= Green
	btn-warning	= Orange
	btn-danger	= Red
*/

class Menu extends Widget{
	public $type;
	public $is_disable;
	public $privilege;
	public $mainMenu = [
		[
			'label'=>'Add Guest',
			'url' => '/guest/create',
			'allow' => 'badges/restrict',
			'color' => 'btn-success',
		],
		[
			'label'=>'Issue New Range Badge',
			'url' => '/badges/create',
		],
		[
			'label'=>'Range Badges',
			'url' => '/badges/index',
		],
		[
			'label'=>'Guest Checkout',
			'url' => '/guest/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Events',
			'url' => '/events/index',
			'color' => 'btn-info',
		],
		[
			'label'=>'Range Violations',
			'url' => '/violations/index',
		],
		[
			'label'=>'Store',
			'url' => '/sales/index',
		],
		[
			'label'=>'Work Credits',
			'url' => '/work-credits/index',
		],
		[
			'label'=>'Admin Functions',
			'url' => '/badge/admin-function',
			'color' => 'btn-warning',
		],
	];
	
	public $mainCalendar = [
		[
			'label'=>'Calender',
			'url' => '/calendar/index',
			'color' => 'btn-danger',
		],
		[
			'label'=>'Calender Settings',
			'url' => '/cal-setup/facility',
			'color' => 'btn-danger',
		],
		[
			'label'=>'Legislative Emails',
			'url' => '/legelemail/index',
			'color' => 'btn-success',
		]
	];

	public $adminMenu = [
		[
			'label'=>'Authorized Users',
			'url' => '/accounts/index',
			'color' => 'btn-warning',
		],
		[
			'label'=>'Create Badge Rosters for Clubs',
			'url' => '/clubs/badge-rosters',
		],
		[
			'label'=>'Fee Schedules',
			'url' => '/fee-structure/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Mass Email',
			'url' => '/mass-email/index',
		],		
		[
			'label'=>'Member Club List',
			'url' => '/clubs/index',
		],
		[
			'label'=>'Post / Print Transactions',
			'url' => '/badges/post-print-transactions',
		],
		[
			'label'=>'Quickbooks Payment Setup',
			'url' => '/payment/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Privileges',
			'url' => '/privileges/index',
		],
		[
			'label'=>'Range Badge Database',
			'url' => '/range-badge-database/index',
		],
		[
			'label'=>'Rule List',
			'url' => '/rules/index',
		],
		[
			'label'=>'Scan Badge',
			'url' => '/badges/scan-badge',
		],
		[
			'label'=>'Settings',
			'url' => '/params/update',
		],
	];

	public $LastMenu = [
		[
			'label'=>'Logout',
			'url' => '/site/logout',
			'color' => 'btn-danger',
		],
	];

	public function run(){
		$menuGenerate = $this->generateMenu($this->type, $this->privilege);
		echo $menuGenerate;
	}

	protected function generateMenu($type,$privilege) {
		$html ='';
		if($type=='admin') {
			$print_menu =  array_merge($this->adminMenu, $this->LastMenu);
		} else {		
			if( strpos( strtolower(" ".$_SERVER['SERVER_NAME']), "badge" )) {
				if ((yii::$app->controller->hasPermission('calendar/showed')) && (yii::$app->params['cal_site']<>'')) {
					$print_menu =  array_merge($this->mainMenu,[['label'=>'Calender','url' => yii::$app->params['cal_site'].'/calendar/index','allow' => 'calendar/index','target'=>'cal','color' => 'btn-danger',],], $this->LastMenu);
				} else {
					$print_menu =  array_merge($this->mainMenu, $this->LastMenu);
				}
			} elseif ( strpos( strtolower(" ".$_SERVER['SERVER_NAME']), "calendar" )) {
				if (yii::$app->controller->hasPermission('cal-setup/index')) {
				$print_menu = array_merge($this->mainMenu, $this->mainCalendar, $this->LastMenu);
				} else {
					$print_menu = array_merge($this->mainCalendar, $this->LastMenu);
				}
			} else {
				$print_menu = array_merge($this->mainMenu, $this->mainCalendar, $this->LastMenu);
			}
		}

		$html .="<div class='container'>\n";
		foreach ($print_menu as $menu) {
			if(!isset($menu['allow'])) { $menu['allow'] = $menu['url']; }
			if(isset($menu['color'])) { $btn_color = $menu['color']; } else { $btn_color='btn-primary'; }
			if(!isset($menu['target'])) { $menu['target'] = '_self'; }
			if(yii::$app->controller->hasPermission(ltrim(str_replace("'","",$menu['allow']),'/'))) {
				$html.='<div class="col-sm-6 col-md-4">';
				$html.= "<a style='margin-bottom: 10px; margin-top: 20px; text-align: center' class='btn-lg btn-block $btn_color' href='".$menu['url']."' target=".$menu['target']."> <span> ".$menu['label']." </span> </a></div><div class='clearfix visible-xs'></div>".PHP_EOL;
			}
		}
		$html .="</div>\n";
		return $html;
	}
}
?>
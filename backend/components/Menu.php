<?php
namespace backend\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use backend\models\Badges;
use backend\models\Params;

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
			'loc'=>['prod'],
			'url' => '/guest/create',
			'allow' => 'badges/restrict',
			'color' => 'btn-success',
		],
		[
			'label'=>'Issue New Range Badge',
			'loc'=>['prod','dev'],
			'url' => '/badges/create',
		],
		[
			'label'=>'Range Badges',
			'loc'=>['prod','dev'],
			'url' => '/badges/index',
			'self'=>true,
		],
		[
			'label'=>'Guest Checkout',
			'loc'=>['prod','dev'],
			'url' => '/guest/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Calender',
			'loc'=>['prod'],
			'url' => false,
			'allow' => 'calendar/index',
			'target'=>'cal',
			'color' => 'btn-danger',
		],
		[
			'label'=>'Calender',
			'loc' => ['cal','dev'],
			'url' => '/calendar/index',
			'color' => 'btn-danger',
		],
		[
			'label'=>'Calender Settings',
			'loc' => ['cal','dev'],
			'url' => '/cal-setup/facility',
			'color' => 'btn-danger',
		],
		[
			'label'=>'Events',
			'loc'=>['prod','dev'],
			'url' => '/events/index',
			'color' => 'btn-info',
		],
		[
			'label'=>'Legislative Emails',
			'loc'=>['prod','dev'],
			'url' => '/legelemail/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Range Violations',
			'loc'=>['prod','dev'],
			'url' => '/violations/index',
		],
		[
			'label'=>'RSO Reports',
			'loc'=>['prod','dev'],
			'url' => '/rso-rpt/index',
		],
		[
			'label'=>'Store',
			'loc'=>['prod','dev'],
			'url' => '/sales/index',
			'self'=>true,
		],
		[
			'label'=>'Work Credits',
			'loc'=>['prod','dev'],
			'url' => '/work-credits/index',
		],
		[
			'label'=>'Admin Functions',
			'loc'=>['prod','cal','dev'],
			'url' => '/site/admin-menu',
			'color' => 'btn-warning',
		],

	];

	public $adminMenu = [
		[
			'label'=>'Authorized Users',
			'loc'=>['prod','dev'],
			'url' => '/accounts/index',
			'color' => 'btn-warning',
		],
		[
			'label'=>'Create Badge Rosters for Clubs',
			'loc'=>['prod','dev'],
			'url' => '/clubs/badge-rosters',
		],
		[
			'label'=>'Mass Email',
			'loc'=>['prod','dev'],
			'url' => '/mass-email/index',
		],
		[
			'label'=>'Member Club List',
			'loc'=>['prod','dev'],
			'url' => '/clubs/index',
		],
		[
			'label'=>'Membership Types',
			'loc'=>['prod','dev'],
			'url' => '/membership-type/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Payment Setup',
			'loc'=>['prod','dev'],
			'url' => '/payment/index',
			'color' => 'btn-success',
		],
		[
			'label'=>'Privileges',
			'loc'=>['prod','dev'],
			'url' => '/privileges/index',
		],
		[
			'label'=>'Range Badge Database',
			'loc'=>['prod','cal','dev'],
			'url' => '/range-badge-database/index',
		],
		[
			'label'=>'Rule List',
			'loc'=>['prod','dev'],
			'url' => '/rules/index',
		],
		[
			'label'=>'Scan Badge',
			'loc'=>['prod','cal','dev'],
			'url' => '/badges/scan-badge',
		],
		[
			'label'=>'Settings',
			'loc'=>['prod','cal','dev'],
			'url' => '/params/update',
		],
		[
			'label'=>'Stickers',
			'loc'=>['prod','dev'],
			'url' => '/rso-rpt/sticker',
		],
		[
			'label'=>'Store Items',
			'loc'=>['prod','dev'],
			'url' => '/sales/stock',
		],
	];

	public $LastMenu = [
		[
			'label'=>'Logout',
			'loc'=>['prod','cal','dev'],
			'url' => '/site/logout',
			'color' => 'btn-danger',
			'self' => true,
		],
	];

	public function run(){
		$menuGenerate = $this->generateMenu($this->type, $this->privilege);
		echo $menuGenerate;
	}

	protected function generateMenu($type,$privilege) {
		$html ='';
		$param = Params::find()->one();
		if($type=='admin') {
			$print_menu =  $this->adminMenu;
		} else {
			$print_menu =  $this->mainMenu;
			if ((file_exists($param->remote_users)) && (isset($_SESSION['r_user']) && ($_SESSION['r_user'] !=null))) {
				$print_menu = array_merge($print_menu, [['label'=>'Remote User Password','loc'=>['prod','dev'],'url' => '/params/password','color' => 'btn-warning',],]);
			}
		}
		$print_menu = array_merge($print_menu, $this->LastMenu);

		if (in_array(5,json_decode(yii::$app->user->identity->privilege))) {
			$member = (New Badges)->find()->where(['badge_number'=>$_SESSION['badge_number'],'status'=>'self'])->one();
			if ($member) { $mem_self=true; } else { $mem_self=false; }
		} else { $mem_self=false; }

		$html .="<div class='container'>\n";
		foreach ($print_menu as $menu) {
			if (in_array(Yii::$app->params['env'], $menu['loc'])) {
				if($mem_self) { if(!isset($menu['self'])) { continue(1); } }
				if(!isset($menu['allow'])) {
					$menu['allow'] = $menu['url'];
				}
				if(isset($menu['color'])) { $btn_color = $menu['color']; } else { $btn_color='btn-primary'; }
				if(!isset($menu['target'])) { $menu['target'] = '_self'; }
				if(yii::$app->controller->hasPermission(ltrim(str_replace("'","",$menu['allow']),'/'))) {
					if(!$menu['url']) {
						$menu['url'] = Yii::$app->params['cal_site'].'/calendar/index';
					}
					$html.='<div class="col-sm-6 col-md-4">';
					$html.= "<a style='margin-bottom: 10px; margin-top: 20px; text-align: center' class='btn-lg btn-block $btn_color' href='".$menu['url']."' target=".$menu['target']."> <span> ".$menu['label']." </span> </a></div><div class='clearfix visible-xs'></div>".PHP_EOL;
				}
			}
		}
		$html .="</div>\n";
		return $html;
	}
}
?>

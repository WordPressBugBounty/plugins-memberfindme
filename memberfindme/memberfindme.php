<?php
/*
Plugin Name: MembershipWorks - Membership, Events & Directory
Plugin URI: https://membershipworks.com
Description: Membership Works plugin
Version: 6.16
Author: MembershipWorks
Author URI: https://membershipworks.com
License: GPL2
*/

/*  Copyright 2013-2026  MembershipWorks  (email : info@membershipworks.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$SF_widgetid=0;
$SF_member_account=false;
$SF_member_labels=array();
$SF_member_folders=array();
$SF_page_data=false;
$SF_enqueue_css=false;

function sf_api($con,$act,$fn,$param) {
	if (!empty($_SERVER['HTTP_USER_AGENT'])&&preg_match("/yandex/i",$_SERVER['HTTP_USER_AGENT'])>0)
		return array('error'=>'Blocked');
	if ($act!='GET')
		return array('error'=>'Not supported');
	$q=array();
	foreach($param as $k=>$v) {
		if (is_array($v)) {
			foreach($v as $i=>$x) $v[$i]=urlencode($x);
			$q[]=$k.'='.implode(',',$v);
		} else {
			$q[]=$k.'='.urlencode($v);
		}
	}
	//$ctx=stream_context_create(array('http'=>array('method'=>'GET','header'=>"From: ".(isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']),'user_agent'=>$_SERVER['HTTP_USER_AGENT'])));
	//$rsp=file_get_contents($con.'://api.membershipworks.com/'.$fn.'?'.implode('&',$q),false,$ctx);
	//if (empty($rsp))
	//	return array('error'=>'No response');
	//else
	//	return json_decode($rsp,true);
	$args=array('headers'=>array('from'=>isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']),'user-agent'=>$_SERVER['HTTP_USER_AGENT']);
	for($try=0;$try<3;$try++) {
		$rsp=wp_remote_get($con.'://api.membershipworks.com/'.$fn.'?'.implode('&',$q),$args);
		if (is_wp_error($rsp)) usleep(100000); else break;
	}
	if (is_wp_error($rsp))
		return array('error'=>$rsp->get_error_message());
	else if (empty($rsp['body']))
		return array('error'=>'No response');
	else
		return json_decode($rsp['body'],true);
}

function sf_admin_menu() {
	add_menu_page('MembershipWorks Admin','Membership Works','edit_users','sf_admin_page','sf_admin_page','data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB2aWV3Qm94PSIwIDAgNDY2IDQ2NiI+CjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLDQ2Ni4wMDAwMDApIHNjYWxlKDAuMTAwMDAwLC0wLjEwMDAwMCkiCmZpbGw9IiNGRkZGRkYiIHN0cm9rZT0ibm9uZSI+CjxwYXRoIGQ9Ik0xNzU1IDQxMjQgYy0yNyAtMiAtOTAgLTkgLTE0MCAtMTUgLTU3OSAtNzEgLTEwMDggLTQ2MyAtMTEyMSAtMTAyNAotMjEgLTEwNyAtMjkgLTM1OCAtMTUgLTQ3OCBsMTEgLTk2IC0yNDYgLTUxMSBjLTEzNSAtMjgxIC0yNDQgLTUxMyAtMjQxIC01MTYKMyAtMyAyOSAyNyA1OSA2OCAzMCA0MCAzMDMgNDEzIDYwOCA4MjggMzA0IDQxNSA1NTggNzYxIDU2NSA3NjggOSAxMCAzMCAtNzcKOTUgLTQwNSA0NiAtMjMwIDg2IC00MjEgODkgLTQyNSA0IC00IDE0NSAxODEgMzE1IDQxMiAxNjkgMjMxIDMxMSA0MjAgMzE1CjQyMCA2IDAgMzMzIC0xNjMwIDMzMSAtMTY1MyAwIC00IC04NCAtNiAtMTg3IC01IGwtMTg3IDMgLTg0IDQyOCBjLTQ3IDIzNQotODcgNDI3IC05MSA0MjcgLTMgMCAtMTQ1IC0xOTEgLTMxNiAtNDI0IC0xNzAgLTIzMyAtMzEyIC00MjIgLTMxNCAtNDIwIC0yIDIKLTQxIDE5MSAtODYgNDE5IC00NSAyMjggLTg0IDQxNyAtODcgNDIxIC01IDUgLTM4OCAtMzExIC0zODggLTMyMCAwIC0xMSA2OQotMTUzIDEwMyAtMjExIDM1MCAtNjA5IDk1NSAtMTA3MCAxNjA4IC0xMjI1IDIxMSAtNTAgNTEzIC02NSA3MDQgLTM2IDYwMCA5MgoxMDEzIDQ4OSAxMTA5IDEwNjUgMTggMTA3IDIxIDM1OCA2IDQ2MyBsLTEwIDY2IDI1MCA1MTggYzEzNyAyODUgMjQ3IDUyMCAyNDUKNTIzIC02IDUgOTIgMTM4IC02MzEgLTg0OCBsLTYxOSAtODQ0IC04NSA0MjcgYy00NiAyMzQgLTg4IDQyNSAtOTIgNDI0IC0zIC0yCi0xNDYgLTE5MyAtMzE2IC00MjYgLTIyMCAtMjk4IC0zMTIgLTQxNyAtMzE2IC00MDUgLTggMjcgLTMyNiAxNjM3IC0zMjYgMTY1MQowIDkgNDUgMTIgMTg3IDEwIGwxODggLTMgODQgLTQzMiBjNDcgLTIzOCA4OCAtNDMzIDkxIC00MzMgMyAwIDE0NSAxOTEgMzE1CjQyNCAxNzEgMjMzIDMxMyA0MjEgMzE2IDQxNyA0IC0zIDQzIC0xOTIgODcgLTQyMCA0NSAtMjI3IDgzIC00MTYgODYgLTQxOCA2Ci02IDM4NiAzMTYgMzg2IDMyOCAwIDMxIC0xMzQgMjcwIC0yMjQgMzk5IC0xMzAgMTg4IC0zMzQgNDA2IC01MDYgNTQ0IC0zNDYKMjc2IC03NzAgNDY3IC0xMTU2IDUyMCAtMTE2IDE2IC0yODcgMjUgLTM2OSAyMHoiLz4KPC9nPgo8L3N2Zz4K','2.99');
	add_submenu_page('sf_admin_page','Members','Members','edit_users','sf_admin_members','sf_admin_page');
	add_submenu_page('sf_admin_page','Folders','Folders','edit_users','sf_admin_folders','sf_admin_page');
	add_submenu_page('sf_admin_page','Labels','Labels &amp; Membership','edit_users','sf_admin_labels','sf_admin_page');
	add_submenu_page('sf_admin_page','Event List','Event List','edit_users','sf_admin_event-list','sf_admin_page');
	add_submenu_page('sf_admin_page','Event Calendar','Event Calendar','edit_users','sf_admin_calendar','sf_admin_page');
	add_submenu_page('sf_admin_page','Forms Carts Donations','Forms Carts Donations','edit_users','sf_admin_forms','sf_admin_page');
	add_submenu_page('sf_admin_page','Jobs/Other Boards','Jobs/Other Boards','edit_users','sf_admin_boards','sf_admin_page');
	add_submenu_page('sf_admin_page','Customization','Customization','edit_users','sf_admin_custom','sf_admin_page');
	add_submenu_page('sf_admin_page','Help','Help','edit_users','sf_admin_help','sf_admin_page');
	add_submenu_page('sf_admin_page','Organization Settings','Organization Settings','edit_users','sf_admin_account','sf_admin_page');
	add_submenu_page('sf_admin_page','Plugin Settings','Plugin Settings','manage_options','sf_admin_options','sf_admin_options');
}

function sf_admin_init() {
	register_setting('sf_admin_group','sf_set','sf_admin_validate');
}

if (is_admin()) {
	add_action('admin_init','sf_admin_init');
	add_action('admin_menu','sf_admin_menu');
}

function sf_admin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	echo '<div class="wrap"><h1>MembershipWorks Plugin Settings</h1>'
		.'<form action="options.php" method="post">';
	settings_fields("sf_admin_group");
	$settings=get_option('sf_set',array());
	echo '<table class="form-table">'
		.'<tr valign="top"><th scope="row">Organization ID</th><td><input type="text" name="sf_set[org]" value="'.esc_attr(isset($settings['org'])?$settings['org']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Facebook App ID (optional)</th><td><input type="text" name="sf_set[fbk]" value="'.esc_attr(isset($settings['fbk'])?$settings['fbk']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Google Maps API key (optional)</th><td><input type="text" name="sf_set[map]" value="'.esc_attr(isset($settings['map'])?$settings['map']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Customize text for directory search button</th><td><input type="text" name="sf_set[fnd]" value="'.esc_attr(empty($settings['fnd'])?'Search':$settings['fnd']).'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Customize text for directory group email button</th><td><input type="text" name="sf_set[rsp]" placeholder="disabled" value="'.esc_attr(isset($settings['rsp'])?$settings['rsp']:'').'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Disable social share buttons</th><td><input type="checkbox" name="sf_set[scl]"'.(empty($settings['scl'])?'':' checked="1"').' /></td></tr>'
		.'<tr valign="top"><th scope="row">Open directory/listing links in new tab (referral information not passed)</th><td><input type="checkbox" name="sf_set[wgo]"'.(empty($settings['wgo'])?'':' checked="1"').' /></td></tr>'
		.'<tr valign="top"><th scope="row">Load js/css inline</th><td><input type="checkbox" name="sf_set[htm]"'.(empty($settings['htm'])?'':' checked="1"').' /></td></tr>'
		.'<tr valign="top"><th scope="row">URL redirect upon signing out</th><td><input type="text" name="sf_set[out]" value="'.esc_url(empty($settings['out'])?'':$settings['out']).'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Page top offset (pixels)</th><td><input type="text" name="sf_set[top]" value="'.esc_attr(empty($settings['top'])?'':$settings['top']).'" /></td></tr>'
		.'<tr valign="top"><th scope="row">Member only content login required message</th><td><textarea name="sf_set[mol]" style="width:500px">'.esc_textarea(empty($settings['mol'])?'The following content is accessible for members only, please sign in.':$settings['mol']).'</textarea></td></tr>'
		.'<tr valign="top"><th scope="row">Member only content membership past due message</th><td><textarea name="sf_set[moe]" style="width:500px">'.esc_textarea(empty($settings['moe'])?'The following content is not accessible because your membership has expired.':$settings['moe']).'</textarea></td></tr>'
		.'<tr valign="top"><th scope="row">Member only content account no access message</th><td><textarea name="sf_set[mon]" style="width:500px">'.esc_textarea(empty($settings['mon'])?'The following content is not accessible for your account.':$settings['mon']).'</textarea></td></tr>'
		.'<tr valign="top"><th scope="row">Member only content session expired message</th><td><textarea name="sf_set[moi]" style="width:500px">'.esc_textarea(empty($settings['moi'])?'Your session has expired, please sign in again.':$settings['moi']).'</textarea></td></tr>'
		.'<tr valign="top"><th scope="row">MembershipWorks data connection</th><td><select name="sf_set[ssl]"><option value=""'.(empty($settings['ssl'])?' selected':'').'>Connect via HTTPS</option><option value="1"'.(!empty($settings['ssl'])&&$settings['ssl']!='2'?' selected':'').'>Connect via HTTP for public data</option><option value="2"'.(!empty($settings['ssl'])&&$settings['ssl']=='2'?' selected':'').'>Connect via HTTP ** INSECURE ** MAY COMPROMISE MEMBER PASSWORDS AND DATA</option></td></tr>'
		.'</table>'
		//.(empty($settings['wpl'])?'':('<input type="hidden" name="sf_set[wpl]" value="'.$settings['wpl'].'" />'))
		.'<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>'
		.'</form></div>';
}

function sf_admin_validate($in) {
	if (!current_user_can('manage_options'))  {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	$raw=current_user_can('unfiltered_html');
	$in['org']=intval($in['org']);
	$in['org']=(is_int($in['org'])?strval($in['org']):'');
	if (!empty($in['fbk'])) $in['fbk']=trim($in['fbk']); else unset($in['fbk']);
	if (!empty($in['map'])) $in['map']=trim($in['map']); else unset($in['map']);
	if (!empty($in['fnd'])) $in['fnd']=trim($in['fnd']); else unset($in['fnd']);
	if (!empty($in['rsp'])) $in['rsp']=trim($in['rsp']); else unset($in['rsp']);
	if (!empty($in['scl'])) $in['scl']='1'; else unset($in['scl']);
	if (!empty($in['wgo'])) $in['wgo']='1'; else unset($in['wgo']);
	if (!empty($in['htm'])) $in['htm']='1'; else unset($in['htm']);
	if (!empty($in['out'])) $in['out']=trim($in['out']); else unset($in['out']);
	if (!empty($in['top'])) $in['top']=trim($in['top']); else unset($in['top']);
	if (!empty($in['mol'])) $in['mol']=$raw?trim($in['mol']):wp_kses_post(trim($in['mol'])); else unset($in['mol']);
	if (!empty($in['moe'])) $in['moe']=$raw?trim($in['moe']):wp_kses_post(trim($in['moe'])); else unset($in['moe']);
	if (!empty($in['mon'])) $in['mon']=$raw?trim($in['mon']):wp_kses_post(trim($in['mon'])); else unset($in['mon']);
	if (!empty($in['moi'])) $in['moi']=$raw?trim($in['moi']):wp_kses_post(trim($in['moi'])); else unset($in['moi']);
	if (!empty($in['ssl'])) $in['ssl']=trim($in['ssl']); else unset($in['ssl']);
	return $in; // preserve other fields for $in including wpl
}

function sf_admin_page() {
	global $plugin_page;
	$settings=get_option('sf_set',array());
	switch (substr($plugin_page,9)) {
		case 'members':		$ini='folder/Members'; $hme='folder/Members'; break;
		case 'labels':		$ini='labels'; $hme='labels'; break;
		case 'folders':		$ini='folders'; $hme='folders'; break;
		case 'event-list':	$ini='!event-list'; $hme='!event-list'; break;
		case 'calendar':	$ini='!calendar'; $hme='!calendar'; break;
		case 'forms':		$ini='forms'; $hme='forms'; break;
		case 'boards':		$ini='boards'; $hme='boards'; break;
		case 'custom':		$ini='custom'; $hme='custom'; break;
		case 'help':		$ini='!help'; $hme='!help'; break;
		case 'account': 	$ini='account/manage'; $hme='account'; break;
		default:			$ini='dashboard'; $hme='dashboard'; break;
	}
	echo '<div id="SFctr" class="SF" data-org="10000" data-hme="'.esc_attr($hme).'" data-ini="'.esc_attr($ini).'"'.(empty($settings['map'])?'':(' data-map="'.esc_attr($settings['map']).'"')).' data-typ="org" data-wpo="options.php" style="position:relative;padding:30px 20px 20px;"></div>'
		.'<script>function sf_admin(){'
			.'var t=document.getElementById("toplevel_page_sf_admin_page");'
			.'if (!t) return;'
			.'var a=t.querySelectorAll(".wp-submenu a"),i,x,n;'
			.'for(i=0;n=a[i];i++){'
				.'x=n.href.split("sf_admin_")[1];'
				.'n.parentNode.className="";'
				.'if (x=="options") continue;'
				.'else if (x=="page"){n.innerHTML="Dashboard";n.parentNode.id="SFhdrdbd";n.href="#dashboard";}'
				.'else if (x=="members"){n.parentNode.id="SFhdrdem";n.href="#folder/Members";}'
				.'else if (x=="labels"){n.parentNode.id="SFhdrlbl";n.href="#labels";}'
				.'else if (x=="folders"){n.parentNode.id="SFhdrdek";n.href="#folders";}'
				.'else if (x=="event-list"){n.parentNode.id="SFhdrevl";n.href="#!event-list";}'
				.'else if (x=="calendar"){n.parentNode.id="SFhdrevc";n.href="#!calendar";}'
				.'else if (x=="forms"){n.parentNode.id="SFhdrfrm";n.href="#forms";}'
				.'else if (x=="boards"){n.parentNode.id="SFhdrlbd";n.href="#boards";}'
				.'else if (x=="help"){n.parentNode.id="SFhdrhlp";n.href="#help";}'
				.'else if (x=="custom"){n.parentNode.id="SFhdrtpl";n.href="#custom";}'
				.'else if (x=="account"){n.parentNode.id="SFhdracc";n.href="#account";}'
			.'}'
		.'}sf_admin();</script>'
		.'<script type="text/javascript" src="https://cdn.membershipworks.com/all.js"></script>'
		.'<script>SF.init();</script>';
	if (empty($settings['org'])) {
		echo '<form id="SFwpo" style="display:none" action="options.php" method="post">';
		settings_fields("sf_admin_group");
		echo '</form>';
	}
}

function sf_enqueue_mfm_script() {
	global $SF_enqueue_css,$SF_page_data;
	wp_register_script('sf-mfm','https://cdn.membershipworks.com/mfm.js',array(),null,true);
	if (!empty($SF_enqueue_css)||!empty($SF_page_data)) {
		wp_register_style('sf-css','https://cdn.membershipworks.com/all.css',array(),null,'all');
		wp_enqueue_style('sf-css');
	}
}
add_action('wp_enqueue_scripts','sf_enqueue_mfm_script');

function sf_title() {
	global $SF_page_data;
	$arg=func_get_args();
	if (empty($SF_page_data)||empty($SF_page_data['ttl']))
		return empty($arg)?'':$arg[0];
	return $SF_page_data['ttl'];
}

function sf_mfm_init() {
	global $post,$SF_page_data,$SF_enqueue_css;
	add_filter('the_content','sf_shortcode',class_exists('ET_Builder_Element')?10:9,1);
	add_filter('the_content','sf_shortcode',99,1);
	add_filter('widget_text','sf_shortcode',10,1);
	add_filter('document_title_parts','sf_document_title_parts',20,1);
	if (empty($post)||!is_object($post)) return;
	$arg=func_get_args();
	$settings=get_option('sf_set',array());
	if (empty($settings['org']))
		return;
	for ($x=false,$i=0,$l=strlen($post->post_content),$mat=array();$i<$l&&(($x=strpos($post->post_content,'[memberfindme open=',$i))!==false||($x=strpos($post->post_content,'[mw open=',$i))!==false);$i=$x+1) {
		$y=strpos($post->post_content,']',$x);
		if ((!$x||substr($post->post_content,$x-1,1)!='[')&&$y!==false) break; // not escaped shortcode and shortcode is closed
	}
	if ($x!==false&&empty($settings['htm']))
		$SF_enqueue_css=true;
	if ($x!==false&&(isset($_GET['_escaped_fragment_'])||preg_match("/slurp|msnbot|facebook|meta/i",$_SERVER['HTTP_USER_AGENT'])>0)) {
		$str=substr($post->post_content,$x+1,$y-$x-1);
		$mat=array();
		$opt=array();
		if (preg_match_all('/\s([a-z\-]*)(=("|“|”|&[^;]*;)+.*?("|“|”|&[^;]*;))?/',$str,$mat,PREG_PATTERN_ORDER)&&!empty($mat)&&!empty($mat[1])) foreach($mat[1] as $key=>$val)
			$opt[$val]=empty($mat[2][$key])?'':trim(preg_replace('/^=("|“|”|&[^;]*;)*|("|“|”|&[^;]*;)*$/','',$mat[2][$key]));
		if (isset($_GET['_escaped_fragment_'])) {
			$pne=$_GET['_escaped_fragment_'];
			remove_action('wp_head','jetpack_og_tags');
			add_filter('jetpack_enable_open_graph','__return_false',99);
			add_filter('jetpack_disable_twitter_cards','__return_true',99);
			if (defined('WPSEO_VERSION')) { // Yoast SEO
				// add filters just in case
				add_filter('wpseo_canonical',function($p){global $SF_page_data;return isset($SF_page_data['rel'])?$SF_page_data['rel']:$p;});
				add_filter('wpseo_title',function($p){global $SF_page_data;return isset($SF_page_data['ttl'])?$SF_page_data['ttl']:$p;});
				add_filter('wpseo_metadesc',function($p){global $SF_page_data;return isset($SF_page_data['sum'])?$SF_page_data['sum']:$p;});
				add_filter('wpseo_opengraph_url',function($p){global $SF_page_data;return isset($SF_page_data['rel'])?$SF_page_data['rel']:$p;});
				add_filter('wpseo_opengraph_title',function($p){global $SF_page_data;return isset($SF_page_data['ttl'])?$SF_page_data['ttl']:$p;});
				add_filter('wpseo_opengraph_desc',function($p){global $SF_page_data;return isset($SF_page_data['sum'])?$SF_page_data['sum']:$p;});
				add_filter('wpseo_opengraph_image',function($p){global $SF_page_data;return isset($SF_page_data['img'])?$SF_page_data['img']:$p;});
				// remove wpseo from wp_head and wp_title
				if (class_exists('Yoast\WP\SEO\Integrations\Front_End_Integration')) {
					global $wp_filter;
					if (!empty($wp_filter['wp_head'])) foreach($wp_filter['wp_head'] as $priority=>$list) foreach($list as $id=>$func) if (strpos($id,'wpseo_')!==false)
						remove_action('wp_head',$id,intval($priority));
					if (!empty($wp_filter['wp_title'])) foreach($wp_filter['wp_title'] as $priority=>$list) foreach($list as $id=>$func) if (strpos($id,'filter_title')!==false)
						remove_action('wp_title',$id,intval($priority));
				} else if (!empty($wpseo_front)||(class_exists('WPSEO_Frontend')&&method_exists('WPSEO_Frontend','get_instance'))) {
					$tmp=empty($wpseo_front)?WPSEO_Frontend::get_instance():$wpseo_front;
					remove_action('wp_head',array($tmp,'head'),1);
					add_filter('wpseo_title','__return_empty_string');
					if (method_exists($tmp,'flush_cache')&&remove_action('wp_footer',array($tmp,'flush_cache'),-1))
						ob_end_flush();
				}
			} else if (defined('AIOSEOP_VERSION')) { // All in One SEO
				global $aiosp;
				if (!empty($aiosp))
					remove_action('wp_head',array($aiosp,'wp_head'),apply_filters('aioseop_wp_head_priority',1));
			}
			remove_action('wp_head','rel_canonical');
			remove_action('wp_head','index_rel_link');
			remove_action('wp_head','start_post_rel_link');
			remove_action('wp_head','adjacent_posts_rel_link_wp_head');
			add_action('wp_head','sf_head',0);
			add_filter('wp_title','sf_title',99,3);
		} else if (!empty($opt['open'])) {
			$pne=$opt['open'];
		} else {
			$pne=false;
		}
		if (!empty($pne)) {
			$qry=array('org'=>$settings['org'],'hdr'=>'','dtl'=>'','url'=>get_permalink(),'pne'=>$pne);
			if (!empty($opt['lbl'])) $qry['lbl']=$opt['lbl']; else if (!empty($opt['labels'])) $qry['lbl']=$opt['labels'];
			if (!empty($opt['folder'])) $qry['dek']=$opt['folder'];
			if (isset($opt['evg'])) $qry['evg']=$opt['evg'];
				$SF_page_data=sf_api('http','GET','api',$qry);
			if (empty($SF_page_data)||!empty($SF_page_data['error']))
				$SF_page_data=false;
			else
				$SF_page_data['set']=$settings;
		}
	}
	if (!empty($SF_page_data)||strpos($post->post_content,'[memberonly')!==false) {
		if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
		if (!defined('DONOTCACHEOBJECT')) define('DONOTCACHEOBJECT',true);
		if (!defined('DONOTCDN')) define('DONOTCDN',true);
		if (function_exists('wp_using_ext_object_cache')) {
			wp_using_ext_object_cache(false);
			wp_cache_flush();
			wp_cache_init();
		}
		nocache_headers();
		setcookie('wordpress_donotcache',time());
	}
}
add_action('wp','sf_mfm_init');

function sf_document_title_parts($title) {
	global $SF_page_data;
	if (!empty($SF_page_data)&&!empty($SF_page_data['ttl']))
		$title['title']=$SF_page_data['ttl'];
	return $title;
}

function sf_head() {
	global $SF_page_data;
	if (!empty($SF_page_data)) {
		$out=array();
		if (isset($SF_page_data['sum'])) 
			$out[]='<meta name="description" content="'.str_replace('"','&quot;',$SF_page_data['sum']).'" />';
		if (isset($SF_page_data['ttl'])) {
			$out[]='<meta property="og:site_name" content="'.str_replace('"','&quot;',get_bloginfo('name')).'" />';
			$out[]='<meta property="og:title" content="'.str_replace('"','&quot;',$SF_page_data['ttl']).'" />';
		}
		if (isset($SF_page_data['img'])) {
			$out[]='<meta property="og:image" content="'.$SF_page_data['img'].'" />';
			$out[]='<meta property="og:image:secure_url" content="'.str_replace('http://','https://',$SF_page_data['img']).'" />';
		}
		if (isset($SF_page_data['sum'])) {
			$out[]='<meta property="og:description" content="'.str_replace('"','&quot;',$SF_page_data['sum']).'" />';
		}
		if (isset($_GET['_escaped_fragment_'])&&isset($SF_page_data['rel'])) {
			$out[]='<meta property="og:url" content="'.$SF_page_data['rel'].'" />';
			$out[]='<link rel="canonical" href="'.$SF_page_data['rel'].'" />';
		}
		if (isset($SF_page_data['nxt'])) 
			$out[]='<link rel="next" href="'.$SF_page_data['nxt'].'" />';
		if (isset($SF_page_data['prv'])) 
			$out[]='<link rel="prev" href="'.$SF_page_data['prv'].'" />';
		echo implode("\r\n",$out).(count($out)?"\r\n":'');
	}
}

function sf_preprocess_memberonly_shortcode($content) {
	global $SF_member_account,$SF_member_labels,$SF_member_folders;
	$settings=get_option('sf_set',array());
	if (empty($settings['org']))
		return $content;
	if (current_user_can('edit_post',get_the_ID()))
		return $content;
	$check_member_labels=[];
	$check_member_folders=[];
	for ($i=0;$i<strlen($content)&&($x=strpos($content,'[memberonly',$i))!==false;) {
		$y=strpos($content,']',$x);
		if (($x>0&&substr($content,$x-1,1)=='[')||$y===false) { $i=$x+1; continue; } // escaped shortcode or shortcode not closed
		if (!defined('DONOTCACHEPAGE'))
			define('DONOTCACHEPAGE',true);
		if (($z=strpos($content,'[/memberonly]',$y))===false) { $z=strlen($content); $l=$z-$y-1; } else { $l=$z-$y-1; $z+=13; }
		$str=substr($content,$x+1,$y-$x-1);
		$mat=array();
		$opt=array();
		if (preg_match_all('/\s([a-z\-]*)(=("|“|”|&[^;]*;)+.*?("|“|”|&[^;]*;))?/',$str,$mat,PREG_PATTERN_ORDER)&&!empty($mat)&&!empty($mat[1])) foreach($mat[1] as $key=>$val)
			$opt[$val]=empty($mat[2][$key])?'':trim(preg_replace('/^=("|“|”|&[^;]*;)*|("|“|”|&[^;]*;)*$/','',$mat[2][$key]));
		if (!empty($opt['label'])||!empty($opt['labels'])) {
			$arr=explode(',',empty($opt['label'])?$opt['labels']:$opt['label']);
			foreach($arr as $val) if (($val=strtolower(trim(urldecode($val))))&&!isset($SF_member_labels[$val])) {
				$SF_member_labels[$val]=false;
				$check_member_labels[]=$val;
			}
		}
		if (!empty($opt['level'])||!empty($opt['levels'])) {
			$arr=explode(',',empty($opt['level'])?$opt['levels']:$opt['level']);
			foreach($arr as $val) if (($val=strtolower(trim(urldecode($val))))&&!isset($SF_member_labels[$val])) {
				$SF_member_labels[$val]=false;
				$check_member_labels[]=$val;
			}
		}
		if (!empty($opt['folder'])||!empty($opt['folders'])) {
			$arr=explode(',',empty($opt['folder'])?$opt['folders']:$opt['folder']);
			foreach($arr as $val) if (($val=strtolower(trim(urldecode($val))))&&!isset($SF_member_folders[$val])) {
				$SF_member_folders[$val]=false;
				$check_member_folders[]=$val;
			}
		}
		if (empty($opt['label'])&&empty($opt['labels'])&&empty($opt['level'])&&empty($opt['levels'])&&empty($opt['folder'])&&empty($opt['folders'])&&!isset($SF_member_folders['members'])) {
			$SF_member_folders['members']=false;
			$check_member_folders[]='members';
		}
		$i=$z+1;
	}
	if (!empty($check_member_labels)||!empty($check_member_folders)) {
		// check if user has required labels
		if (!empty($_SERVER)&&!empty($_SERVER['HTTP_COOKIE'])&&preg_match('/^SFSF=[^;]*|\\sSFSF=[^;]*/',$_SERVER['HTTP_COOKIE'],$_sf))
			$_sf=preg_replace('/\\s|\\+/','',substr($_sf[0],strpos($_sf[0],'=')+1));
		else if (!empty($_COOKIE['SFSF']))
			$_sf=strlen($_COOKIE['SFSF'])<2?'':$_COOKIE['SFSF'];
		else
			$_sf='';
		if ($_sf) {
			$tmp=array('org'=>$settings['org'],'sfsf'=>$_sf);
			if (!empty($check_member_labels)) $tmp['lbl']=$check_member_labels;
			if (!empty($check_member_folders)) $tmp['dek']=$check_member_folders;
			$usr=sf_api(empty($settings['ssl'])||$settings['ssl']!='2'?'https':'http','GET','v1/lbl',$tmp);
			if (empty($usr)) {
				$SF_member_account=array('error'=>'Invalid session');
			} else {
				$SF_member_account=array_diff_key($usr,array('lbl'=>1));
				if (!empty($usr['lbl'])) foreach($usr['lbl'] as $i=>$x) {
					if ($x['typ']==1)
						$SF_member_folders[strtolower(trim($x['lbl']))]=true;
					else
						$SF_member_labels[strtolower(trim($x['lbl']))]=true;
				}
			}
		}
	}
	return $content;
}

function sf_shortcode($content) {
	global $SF_member_account,$SF_member_labels,$SF_member_folders,$SF_page_data;
	$settings=get_option('sf_set',array());
	$wpl=defined('SF_WPL')?preg_replace('/^http[s]?:\\/\\/[^\\/]*/','',SF_WPL>=3?admin_url('admin-ajax.php'):site_url('wp-login.php','login_post')):(empty($settings['wpl'])?'':$settings['wpl']);
	$opened=false;
	// fetch member labels/folders data
	sf_preprocess_memberonly_shortcode($content);
	// process memberonly shortcodes			
	for ($i=0;$i<strlen($content)&&($x=strpos($content,'[memberonly',$i))!==false;) {
		$y=strpos($content,']',$x);
		if (($x>0&&substr($content,$x-1,1)=='[')||$y===false) { $i=$x+1; continue; } // escaped shortcode or shortcode not closed
		if (($z=strpos($content,'[/memberonly]',$y))===false) { $z=strlen($content); $l=$z-$y-1; } else { $l=$z-$y-1; $z+=13; }
		$str=substr($content,$x+1,$y-$x-1);
		$mat=array();
		$opt=array();
		if (preg_match_all('/\s([a-z\-]*)(=("|“|”|&[^;]*;)+.*?("|“|”|&[^;]*;))?/',$str,$mat,PREG_PATTERN_ORDER)&&!empty($mat)&&!empty($mat[1])) foreach($mat[1] as $key=>$val)
			$opt[$val]=empty($mat[2][$key])?'':trim(preg_replace('/^=("|“|”|&[^;]*;)*|("|“|”|&[^;]*;)*$/','',$mat[2][$key]));
		if (empty($settings['org'])) {
			$out='<div>Organization ID not setup. Please update settings.</div>';
		} else if (current_user_can('edit_post',get_the_ID())) {
			$tmp=[];
			foreach ($opt as $key=>$val) $tmp[]=$key.'="'.$val.'"';
			$out='[administrator notice: content below memberonly '.implode(' ',$tmp).']'
				.substr($content,$y+1,$l);
		} else {
			if (empty($SF_member_account)) {
				$msg=isset($opt['message'])?$opt['message']:(empty($settings['mol'])?'The following content is accessible for members only, please sign in.':$settings['mol']);
			} else if (!empty($SF_member_account['error'])) {
				$msg=isset($opt['message'])?$opt['message']:(empty($settings['moi'])?'Your session has expired, please sign in again.':$settings['moi']);
			} else if (!empty($SF_member_account['end'])) {
				$msg=isset($opt['message'])?$opt['message']:(empty($settings['moe'])?'The following content is not accessible because your membership has expired.':$settings['moe']);
			} else {
				$msg=isset($opt['message'])?$opt['message']:(empty($settings['mon'])?'The following content is not accessible for your account.':$settings['mon']);
				if ($msg!==false&&(!empty($opt['label'])||!empty($opt['labels']))) {
					$arr=explode(',',empty($opt['label'])?$opt['labels']:$opt['label']);
					foreach ($arr as $key=>$val) if (!empty($SF_member_labels[strtolower(trim(urldecode($val)))])) $msg=false;
				}
				if ($msg!==false&&(!empty($opt['level'])||!empty($opt['levels']))) {
					$arr=explode(',',empty($opt['level'])?$opt['levels']:$opt['level']);
					foreach ($arr as $key=>$val) if (!empty($SF_member_labels[strtolower(trim(urldecode($val)))])) $msg=false;
				}
				if ($msg!==false&&(!empty($opt['folder'])||!empty($opt['folders']))) {
					$arr=explode(',',empty($opt['folder'])?$opt['folders']:$opt['folder']);
					foreach ($arr as $key=>$val) if (!empty($SF_member_folders[strtolower(trim(urldecode($val)))])) $msg=false;
				}
				if ($msg!==false&&empty($opt['label'])&&empty($opt['labels'])&&empty($opt['level'])&&empty($opt['levels'])&&empty($opt['folder'])&&empty($opt['folders'])&&!empty($SF_member_folders['members'])) {
					$msg=false;
				}
			}
			if ($msg===false&&isset($opt['false'])) {
				$out='';
			} else if ($msg===false||isset($opt['false'])) {
				$out=trim(preg_replace('/^<br(\\s\\/)?>|<br(\\s\\/)?>$/','',substr($content,$y+1,$l)));
			} else if (is_singular()&&!empty($opt['nonmember-redirect'])) {
				$out=(isset($opt['nomessage'])?'':('<span class="memberonly">'.__($msg).'</span>'))
					.'<script>setTimeout(\'window.location="'.esc_url($opt['nonmember-redirect']).'"\',2000);</script>';
				$opened=true;
			} else if (is_singular()&&!$opened&&!empty($opt['nonmember'])) {
				$out=(isset($opt['nomessage'])?'':('<div class="memberonly" style="margin-bottom:20px">'.__($msg).'</div>'))
					.'[mw open="'.$opt['nonmember'].'"]';
			} else if (is_singular()&&!$opened&&(empty($SF_member_account)||!empty($SF_member_account['error']))&&!isset($opt['nologin'])) {
				$out='<div class="memberonlywrapper" style="padding:40px 0 0;margin:40px 0;border-top:1px solid #ddd;border-bottom:1px solid #ddd">'
					.(isset($opt['nomessage'])?'':('<div class="memberonly" style="margin-bottom:20px">'.__($msg).'</div>'))
					.'<div id="SFctr" class="SF" data-sfi="1" data-org="'.esc_attr($settings['org']).'" data-ini="myaccount" data-zzz="'.esc_url(get_permalink()).'"'
					.(empty($wpl)?'':' data-wpl="'.esc_url($wpl).'"')
					.' style="position:relative;height:auto;margin-bottom:40px">'
					.'<div id="SFpne" style="position:relative"><div class="SFpne">Loading...</div></div>'
					.'<div style="clear:both"></div>'
					.(empty($settings['htm'])?'':'<script type="text/javascript" src="https://cdn.membershipworks.com/mfm.js" defer="defer"></script>')
					.'</div></div>';
				if (empty($settings['htm']))
					wp_enqueue_script('sf-mfm');
				if (!defined('DONOTROCKETOPTIMIZE'))
					define('DONOTROCKETOPTIMIZE',true);
				$opened=true;
			} else {
				$out=(isset($opt['nomessage'])?'':('<span class="memberonly">'.__($msg).'</span>'));
			}
		}
		$content=substr_replace($content,$out,$x,$z-$x);
		$i=$x+strlen($out);
	}
	// process mw shortcodes
	for ($i=0;$i<strlen($content)&&(($x=strpos($content,'[memberfindme ',$i))!==false||($x=strpos($content,'[mw ',$i))!==false);) {
		$y=strpos($content,']',$x);
		if (($x>0&&substr($content,$x-1,1)=='[')||$y===false) { $i=$x+1; continue; } // escaped shortcode or shortcode not closed
		$str=substr($content,$x+1,$y-$x-1);
		$mat=array();
		$opt=array();
		if (!preg_match_all('/\s([a-z\-]*)(=("|“|”|&[^;]*;)+.*?("|“|”|&[^;]*;))?/',$str,$mat,PREG_PATTERN_ORDER)||empty($mat)||empty($mat[1])) { $i=$x+1; continue; }
		foreach ($mat[1] as $key=>$val) $opt[$val]=empty($mat[2][$key])?'':trim(preg_replace('/^=("|“|”|&[^;]*;)*|("|“|”|&[^;]*;)*$/','',$mat[2][$key]));
		// create output
		if (empty($settings['org'])) {
			$out='<div>Organization ID not setup. Please update settings.</div>';
		} else if (!$opened&&isset($opt['open'])) {
			$out=(empty($settings['htm'])?'':'<div style="display:none"><script>if(typeof(SF)=="object"&&SF.close)SF.close();</script></div>')
				.'<div id="SFctr" class="SF" data-org="'.esc_attr($settings['org']).'" data-ini="'.esc_attr($opt['open']).'"'
				.(empty($settings['map'])?'':(' data-map="'.esc_attr($settings['map']).'"'))
				.(empty($settings['fbk'])?'':(' data-fbk="'.esc_attr($settings['fbk']).'"'))
				.(empty($settings['fnd'])?'':(' data-fnd="'.esc_attr($settings['fnd']).'"'))
				.(empty($settings['rsp'])?'':(' data-rsp="'.esc_attr($settings['rsp']).'"'))
				.(empty($settings['scl'])&&empty($opt['noshare'])?'':(' data-scl="0"'))
				.(empty($settings['wgo'])?'':(' data-wgo="1"'))
				.(empty($settings['out'])?'':(' data-out="'.esc_url($settings['out']).'"'))
				.(empty($settings['top'])?'':(' data-top="'.esc_attr($settings['top']).'"'))
				.(empty($wpl)?'':' data-wpl="'.esc_url($wpl).'"')
				.(empty($opt['lbl'])&&empty($opt['labels'])?'':(' data-lbl="'.esc_attr(empty($opt['lbl'])?$opt['labels']:$opt['lbl']).'"'))
				.(empty($opt['folder'])?'':(' data-dek="'.esc_attr($opt['folder']).'"'))
				.(empty($opt['levels'])?'':(' data-lvl="'.esc_attr($opt['levels']).'"'))
				.(isset($opt['evg'])?(' data-evg="'.esc_attr($opt['evg']).'"'):'')
				.(isset($opt['viewport'])&&$opt['viewport']=='fixed'?(' data-ofy="1"'):'')
				.(isset($opt['redirect'])?(' data-zzz="'.esc_url($opt['redirect']).'"'):'')
				.(isset($opt['checkout'])?(' data-zgo="'.esc_url($opt['checkout']).'"'):'')
				.(isset($opt['ini'])&&$opt['ini']=='0'?'':' data-sfi="1"')
				.' style="'.(isset($opt['style'])?$opt['style']:'position:relative;height:auto').'">'
				.'<div id="SFpne" style="position:relative">'
					.(empty($SF_page_data)?(isset($opt['ini'])&&$opt['ini']=='0'?'':'<div class="SFpne">Loading...</div>'):$SF_page_data['dtl'])
				.'</div>'
				.'<div id="SFfin" style="clear:both"></div>'
				.(empty($settings['htm'])?'':'<script type="text/javascript" src="https://cdn.membershipworks.com/mfm.js"></script>')
				.'</div>';
			if (empty($settings['htm']))
				wp_enqueue_script('sf-mfm');
			if (!defined('DONOTROCKETOPTIMIZE'))
				define('DONOTROCKETOPTIMIZE',true);
			$opened=true;
		} else if (isset($opt['button'])) { 
			$out=(isset($opt['type'])?('<'.$opt['type']):'<button')
				.(isset($opt['type'])&&$opt['type']=='img'&&isset($opt['src'])?(' src="'.esc_url($opt['src']).'"'):'')
				.(isset($opt['class'])?(' class="'.esc_attr($opt['class']).'"'):'')
				.(isset($opt['style'])?(' style="'.esc_attr($opt['style']).'"'):' style="cursor:pointer;"')
				.($opt['button']=='join'?(' onclick="if(typeof(SF)!=\'undefined\')SF.open(\'account/join\');">'.(isset($opt['text'])?$opt['text']:'Join')):'')
				.(isset($opt['type'])?($opt['type']=='img'?'':('</'.$opt['type'].'>')):'</button>');
		} else if (isset($opt['join'])) {
			$out=(isset($opt['type'])?('<'.$opt['type']):'<a')
				.(isset($opt['type'])&&$opt['type']=='img'&&isset($opt['src'])?(' src="'.esc_url($opt['src']).'"'):'')
				.(isset($opt['class'])?(' class="'.esc_attr($opt['class']).'"'):'')
				.(isset($opt['style'])?(' style="'.esc_attr($opt['style']).'"'):' style="cursor:pointer;"')
				.(isset($opt['type'])&&$opt['type']!='a'?(' onclick="window.location.hash=\'account/join/'.$opt['join'].'\';if(typeof(SF)!=\'undefined\')setTimeout(\'SF.init()\',50);">'):(' onclick="if(typeof(SF)!=\'undefined\')setTimeout(\'SF.init()\',50)" href="#account/join/'.$opt['join'].'">'))
				.(isset($opt['text'])?$opt['text']:'Join')
				.(isset($opt['type'])?($opt['type']=='img'?'':('</'.$opt['type'].'>')):'</a>');
		} else if (isset($opt['name'])||isset($opt['check'])) {
			if (!isset($usl))
				$usl=!empty($wpl)&&is_user_logged_in()&&get_user_meta(get_current_user_id(),'SF_ID',true)?wp_get_current_user():false;
			$out=(isset($opt['name'])?'<span class="SFnam">'.(empty($usl)?'':$usl->display_name).'</span>':'')
				.($opened||(isset($opt['name'])&&!empty($usl))?'':'<script>(function(){var i,j,a,x;try{x=localStorage.getItem("SF_nam");}catch(e){x="";}try{for(a=document.querySelectorAll(".SFnam"),i=a.length-1;i>=0;i--)a[i].innerHTML=x?x:"";}catch(e){}try{for(a=document.querySelectorAll(".SF_li"),i=a.length-1;i>=0;i--)a[i].style.display=x?"":"none";}catch(e){}try{for(a=document.querySelectorAll(".SF_lo"),i=a.length-1;i>=0;i--)a[i].style.display=x?"none":"";}catch(e){}})();</script>');
			if (!defined('DONOTCACHEPAGE'))
				define('DONOTCACHEPAGE',true);
		} else if (isset($opt['listlabel'])||isset($opt['listfolder'])) {
			$dat=sf_api(empty($settings['ssl'])?'https':'http','GET','v1/dek',array('org'=>$settings['org'],'wem'=>1,'typ'=>isset($opt['listlabel'])?3:1,'lbl'=>isset($opt['listlabel'])?$opt['listlabel']:$opt['listfolder']));
			if (!empty($dat)&&!empty($dat['error']))
				$out=$dat['error'];
			else {
				$out=array();
				if (!empty($dat)) foreach($dat as $usr)
					$out[]='<li><a href="'.$usr['url'].'">'.$usr['nam'].'</a></li>';
				$out='<ul class="sf_list">'.implode('',$out).'</ul>';
			}
		} else if (isset($opt['listevents'])) {
			$dat=sf_api(empty($settings['ssl'])?'https':'http','GET','v1/evt',array('org'=>$settings['org'],'wee'=>1,'grp'=>$opt['listevents'],'cnt'=>isset($opt['count'])?$opt['count']:'5','sdp'=>time()));
			if (!empty($dat)&&!empty($dat['error']))
				$out=$dat['error'];
			else {
				$out=array();
				if (!empty($dat)) foreach ($dat as $evt) {
					$ts=explode(',',$evt['szp']);
					if (!empty($evt['ezp'])&&($te=explode(',',$evt['ezp']))&&$te[0]==$ts[0])
						$evt['ezp']=trim(implode(',',array_slice($te,1)));
					$out[]='<li><a href="'.$evt['url'].'">'.$evt['ttl'].'</a><div class="event-when"><span class="event-start">'.$evt['szp'].'</span>'.(isset($evt['ezp'])&&$evt['ezp']?('<span class="event-sep"> - </span><span class="event-end">'.$evt['ezp'].'</span>'):'').'</div></li>';
				}
				$out='<ul class="sf_list">'.implode('',$out).'</ul>';
			}
		} else if (isset($opt['eventwidget'])) {
			$out=sf_widget_event_do(array_merge($opt,array('org'=>$settings['org'])),$settings);
		} else if (isset($opt['folderwidget'])) {
			$out=sf_widget_folder_do(array_merge($opt,array('org'=>$settings['org'])),$settings);
		} else {
			$out='';
		}
		$content=substr_replace($content,$out,$x,$y-$x+1);
		$i=$x+strlen($out);
	}
	if (!$opened&&!empty($SF_page_data)&&($x=strpos($content,'<div id="SFpne" style="position:relative">'))!==false&&($y=strpos($content,'<div id="SFfin" style="clear:both">'))!==false) {
		// replace again in case wp_autop mucks things up
		$content=substr_replace($content,'<div id="SFpne" style="position:relative">'.$SF_page_data['dtl'].'</div>',$x,$y-$x);
	}
	return $content;
}

function sf_widget_event_do($instance,$settings) {
	if (empty($instance['org']))
		return '<div>Organization ID not setup. Please update settings.</div>';
	$instance=wp_parse_args($instance,array('grp'=>'','cnt'=>'3','lgo'=>'','adn'=>'','szp'=>'','ezp'=>''));
	$dat=sf_api(empty($settings)||empty($settings['ssl'])?'https':'http','GET','v1/evt',array('org'=>$instance['org'],'wee'=>1,'grp'=>$instance['grp'],'cnt'=>$instance['cnt'],'sdp'=>time()));
	if (empty($dat))
		return '<div>No current events</div>';
	if (!empty($dat)&&!empty($dat['error']))
		return '<div>'.$dat['error'].'</div>';
	$out=array();
	foreach ($dat as $x) {
		$ts=explode(',',$x['szp']);
		if (empty($instance['szp'])&&!empty($x['ezp'])) {
			$te=explode(',',$x['ezp']);
			if ($te[0]==$ts[0]) $x['ezp']=trim(implode(',',array_slice($te,1)));
		}
		$out[]='<li class="event-item">'
			.'<a class="event-link" href="'.esc_url($x['url']).'">'
				.(empty($x['lgo'])||empty($instance['lgo'])?'':('<img class="event-thumb" src="//d1tif55lvfk8gc.cloudfront.net/'.$x['_id'].'s.jpg?'.$x['lgo'].'" alt="" style="max-width:100%"/>'))
				.$x['ttl']
			.'</a>'
			.(empty($instance['szp'])||(empty($instance['exp'])&&!empty($x['ezp']))?('<div class="event-when">'
				.(empty($instance['szp'])?('<span class="event-start">'.$x['szp'].'</span>'):'')
				.(empty($instance['szp'])&&empty($instance['ezp'])&&!empty($x['ezp'])?'<span class="event-sep"> - </span>':'')
				.(empty($instance['ezp'])&&!empty($x['ezp'])?('<span class="event-end">'.$x['ezp'].'</span>'):'')
			.'</div>'):'')
			.(empty($instance['adn'])||empty($x['adn'])?'':('<div class="event-where">'.$x['adn'].'</div>'))
			.'</li>';
	}
	return '<ul class="sf_widget_event_list" style="display:block;position:relative">'.implode($out).'</ul>';
}

class sf_widget_event extends WP_Widget {
	public function __construct() {
		parent::__construct('sf_widget_event','Upcoming Events',array('description'=>'Upcoming events from your MembershipWorks calendar'));
	}
	public function widget($args,$instance ) {
		extract($args);
		$settings=get_option('sf_set',array());
		if (empty($settings['org'])) {
			echo '<div>Organization ID not setup. Please update settings.</div>';
			return;
		}
		$title=apply_filters('widget_title',$instance['title']);
		if (empty($title))
			echo str_replace('widget_sf_widget_event','widget_sf_widget_event widget_no_title',$before_widget);
		else
			echo $before_widget.$before_title.$title.$after_title;
		echo sf_widget_event_do(array_merge($instance,array('org'=>$settings['org'])),$settings);
		echo $after_widget;
	}
	public function update($new_instance,$old_instance ) {
		$instance=$old_instance;
		$instance['title']=strip_tags($new_instance['title']);
		$instance['grp']=isset($new_instance['grp'])?$new_instance['grp']:'';
		$instance['cnt']=$new_instance['cnt']?strval(intval($new_instance['cnt'])):'0';
		$instance['lgo']=empty($new_instance['lgo'])?'':'1';
		$instance['adn']=empty($new_instance['adn'])?'':'1';
		$instance['szp']=empty($new_instance['szp'])?'':'1';
		$instance['ezp']=empty($new_instance['ezp'])?'':'1';
		return $instance;
	}
	public function form($instance) {
		$instance=wp_parse_args($instance,array('title'=>'','grp'=>'','cnt'=>'3','lgo'=>'','adn'=>'','szp'=>'','ezp'=>''));
		$title=strip_tags($instance['title']);
		$grp=$instance['grp'];
		$cnt=intval($instance['cnt']);
		echo '<p><label for="'.esc_attr($this->get_field_id('title')).'">Title:</label> <input class="widefat" id="'.esc_attr($this->get_field_id('title')).'" name="'.esc_attr($this->get_field_name('title')).'" type="text" value="'.esc_attr($title).'" /></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('grp')).'">Category number (blank=all):</label> <input id="'.esc_attr($this->get_field_id('grp')).'" name="'.esc_attr($this->get_field_name('grp')).'" type="text" value="'.esc_attr($grp).'" size="3"/></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('cnt')).'">Number of events to show:</label> <input id="'.esc_attr($this->get_field_id('cnt')).'" name="'.esc_attr($this->get_field_name('cnt')).'" type="text" value="'.esc_attr($cnt).'" size="3"/></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('lgo')).'">Display images:</label> <input id="'.esc_attr($this->get_field_id('lgo')).'" name="'.esc_attr($this->get_field_name('lgo')).'" type="checkbox" value="1"'.(empty($instance['lgo'])?'':' checked').'/></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('adn')).'">Display location:</label> <input id="'.esc_attr($this->get_field_id('adn')).'" name="'.esc_attr($this->get_field_name('adn')).'" type="checkbox" value="1"'.(empty($instance['adn'])?'':' checked').'/></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('szp')).'">Hide start date/time:</label> <input id="'.esc_attr($this->get_field_id('szp')).'" name="'.esc_attr($this->get_field_name('szp')).'" type="checkbox" value="1"'.(empty($instance['szp'])?'':' checked').'/></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('ezp')).'">Hide end date time:</label> <input id="'.esc_attr($this->get_field_id('ezp')).'" name="'.esc_attr($this->get_field_name('ezp')).'" type="checkbox" value="1"'.(empty($instance['ezp'])?'':' checked').'/></p>';
	}
}

function sf_widget_folder_do($instance,$settings) {
	global $SF_widgetid;
	if (empty($instance['org']))
		return '<div>Organization ID not setup. Please update settings.</div>';
	$instance=wp_parse_args($instance,array('typ'=>'1','lbl'=>'Members','act'=>'0','delay'=>'10','nam'=>''));
	$dat=sf_api(empty($settings)||empty($settings['ssl'])?'https':'http','GET','v1/dek',array('org'=>$instance['org'],'wem'=>1,'typ'=>empty($instance['typ'])?'1':$instance['typ'],'lbl'=>$instance['lbl']));
	if (!empty($dat)&&!empty($dat['error']))
		return '<div>'.$dat['error'].'</div>';
	$out=array();
	if (!empty($dat)) foreach($dat as $x) {
		if ($instance['act']=='1')
			$out[]='<li style="display:none;background-color:white;text-align:center;height:148px;padding:0;margin:0;table-layout:fixed;width:100%;"><a href="'.esc_url($x['url']).'" style="display:table-cell;vertical-align:middle;padding:10px;text-decoration:none;">'
				.(empty($x['lgo'])?'':('<div class="member-image"><img src="https://cdn.membershipworks.com/u/'.$x['_id'].'_lgl.jpg?'.$x['lgo'].'" alt="'.(!empty($instance['nam'])?'':esc_attr($x['nam'])).'" onerror="this.parentNode.innerHTML=this.alt;" style="display:block;margin:0 auto;max-width:100%;max-height:75px;"></div>'))
				.(empty($x['lgo'])||!empty($instance['nam'])?('<div class="member-name" style="display:block;width:100%;font-size:'.($x['lgo']?'1.1em':'1.5em').'">'.esc_html(html_entity_decode($x['nam'],ENT_HTML5|ENT_QUOTES)).'</div>'):'')
				.(empty($x['cnm'])?'':('<small class="member-tagline" style="display:block;padding:10px;">'.esc_html(html_entity_decode($x['cnm'],ENT_HTML5|ENT_QUOTES)).'</small>'))
				.'</a></li>';
		else
			$out[]='<li><a href="'.esc_url($x['url']).'">'
				.(empty($x['lgo'])||empty($instance['lgo'])?'':('<div class="member-image"><img src="https://cdn.membershipworks.com/u/'.$x['_id'].'_lgl.jpg?'.$x['lgo'].'" alt="" onerror="this.parentNode.innerHTML=this.alt;" style="display:block;margin:0 auto;max-width:100%;max-height:75px;"></div>'))
				.'<span class="member-name">'.esc_html(html_entity_decode($x['nam'],ENT_HTML5|ENT_QUOTES)).'</span>'
				.(empty($x['cnm'])?'':('<small class="member-tagline" style="display:block;">'.esc_html(html_entity_decode($x['cnm'],ENT_HTML5|ENT_QUOTES)).'</small>'))
				.'</a></li>';
	}
	$id=empty($instance['wid'])?('sf-widget-'.($SF_widgetid++)):$instance['wid'];
	if ($instance['act']=='1') {
		$delay=intval($instance['delay'])*1000;
		$fn=str_replace('-','_',$id);
		return '<ul id="'.$id.'" class="sf_widget_folder_logos" style="list-style:none;margin:0;padding:5px">'.implode($out).'</ul>'
			.(empty($out)?'':('<script>'.$fn.'_animate=function(){var r=document.getElementById("'.$id.'"),x=r.querySelector(\'li[style*="table;"]\');if(x){x.style.display="none";x=(x.nextSibling?x.nextSibling:r.firstChild);}else x=r.childNodes[Math.round(Math.random()*(r.childNodes.length-1))];if(x)x.style.display="table";setTimeout('.$fn.'_animate,'.($delay?$delay:10000).');};'.$fn.'_animate();</script>'));
	} else {
		return '<ul id="'.$id.'" class="sf_widget_folder_list">'.implode($out).'</ul>';
	}
}

class sf_widget_folder extends WP_Widget {
	public function __construct() {
		parent::__construct('sf_widget_folder','Members/Folder Widget',array('description'=>'Display contacts from your MembershipWorks folder or label'));
	}
	public function widget($args,$instance ) {
		extract($args);
		$settings=get_option('sf_set',array());
		if (empty($settings['org'])) {
			echo '<div>Organization ID not setup. Please update settings.</div>';
			return;
		}
		$title=apply_filters('widget_title',$instance['title']);
		if (empty($title))
			echo str_replace('widget_sf_widget_folder','widget_sf_widget_folder widget_no_title',$before_widget);
		else
			echo $before_widget.$before_title.$title.$after_title;
		echo sf_widget_folder_do(array_merge($instance,array('org'=>$settings['org'],'wid'=>($this->id).'-list')),$settings);
		echo $after_widget;
	}
	public function update($new_instance,$old_instance ) {
		$instance=$old_instance;
		$instance['title']=strip_tags($new_instance['title']);
		$instance['lbl']=trim($new_instance['lbl']);
		$instance['typ']=strval(intval($new_instance['typ']));
		$instance['act']=strval(intval($new_instance['act']));
		$instance['delay']=strval(intval($new_instance['delay']));
		$instance['nam']=empty($new_instance['nam'])?'':'1';
		return $instance;
	}
	public function form($instance) {
		$instance=wp_parse_args($instance,array('title'=>'','typ'=>'1','lbl'=>'Members','act'=>'0','delay'=>'10','nam'=>''));
		$title=strip_tags($instance['title']);
		echo '<p><label for="'.esc_attr($this->get_field_id('title')).'">Title:</label> <input class="widefat" id="'.esc_attr($this->get_field_id('title')).'" name="'.esc_attr($this->get_field_name('title')).'" type="text" value="'.esc_attr($title).'" /></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('lbl')).'">Folder/label name:</label> <input class="widefat" id="'.esc_attr($this->get_field_id('lbl')).'" name="'.esc_attr($this->get_field_name('lbl')).'" type="text" value="'.esc_attr($instance['lbl']).'" /></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('typ')).'">Type:</label> <select id="'.esc_attr($this->get_field_id('typ')).'" name="'.esc_attr($this->get_field_name('typ')).'">'
				.'<option value="1"'.($instance['typ']=='1'?' selected="selected"':'').'>Public folder</option>'
				.'<option value="3"'.($instance['typ']=='3'?' selected="selected"':'').'>Publicly searchable label</option>'
			.'</select></p>'
			.'<p><label for="'.esc_attr($this->get_field_id('act')).'">Display:</label> <select id="'.esc_attr($this->get_field_id('act')).'" name="'.esc_attr($this->get_field_name('act')).'" onchange="this.parentNode.nextSibling.style.display=(this.value==\'1\'?\'\':\'none\');">'
				.'<option value="0"'.($instance['act']=='0'?' selected="selected"':'').'>List</option>'
				.'<option value="1"'.($instance['act']=='1'?' selected="selected"':'').'>Slideshow</option>'
			.'</select></p>'
			.'<div'.($instance['act']=='1'?'>':' style="display:none;">')
				.'<p><label for="'.esc_attr($this->get_field_id('delay')).'">Seconds between slides:</label> <input id="'.esc_attr($this->get_field_id('delay')).'" name="'.esc_attr($this->get_field_name('delay')).'" type="text" value="'.esc_attr($instance['delay']).'" size="3"/></p>'
				.'<p><label for="'.esc_attr($this->get_field_id('nam')).'">Always display name:</label> <input id="'.esc_attr($this->get_field_id('nam')).'" name="'.esc_attr($this->get_field_name('nam')).'" type="checkbox" value="1"'.(empty($instance['nam'])?'':' checked').'/></p>'
			.'</div>';
	}
}

function sf_widgets_init() {
	register_widget('sf_widget_event');
	register_widget('sf_widget_folder');
}
add_action('widgets_init','sf_widgets_init');

?>
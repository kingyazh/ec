<?php

/**
 * ECSHOP 广告管理程序
 * ============================================================================
 * 版权所有 2005-2013 有限公司，并保留所有权利。
 * 网站地址: http://www.Mensa.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: King $
 * $Id: ads.php 17063 2010-03-25 06:35:46Z King $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/ads.php');
$exc   = new exchange($ecs->table("ad"), $db, 'ad_id', 'ad_name');
$uri = $ecs->url();

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
$smarty->assign('lang', $_LANG);
/*------------------------------------------------------ */
//-- 广告列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $pid = !empty($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;

    $smarty->assign('ur_here',     $_LANG['ad_list']);
	$smarty->assign('action_link', array('text' => $_LANG['ads_make_xml'], 'href' => 'ads_makexml.php?act=list'));//Add by King. 生成 flash 读取的 xml 文件
    $smarty->assign('pid',         $pid);
     $smarty->assign('full_page',  1);

    $ads_list = get_adslist();

    $smarty->assign('ads_list',     $ads_list['ads']);
    $smarty->assign('filter',       $ads_list['filter']);
    $smarty->assign('record_count', $ads_list['record_count']);
    $smarty->assign('page_count',   $ads_list['page_count']);

    $sort_flag  = sort_flag($ads_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('ads_xml.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $ads_list = get_adslist();

    $smarty->assign('ads_list',     $ads_list['ads']);
    $smarty->assign('filter',       $ads_list['filter']);
    $smarty->assign('record_count', $ads_list['record_count']);
    $smarty->assign('page_count',   $ads_list['page_count']);

    $sort_flag  = sort_flag($ads_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('ads_xml.htm'), '',
        array('filter' => $ads_list['filter'], 'page_count' => $ads_list['page_count']));
}

/*------------------------------------------------------ */
//-- 生成xml页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'makexml')
{
    $pid = !empty($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;
	$cat_list = cat_list(0, 0, false);
	$smarty->assign('cat_info',     $cat_list);

    $smarty->assign('ur_here',     $_LANG['ad_list']);
	$smarty->assign('action_link', array('text' => $_LANG['ad_list'], 'href' => 'ads_makexml.php?act=list'));//Add by King. 生成 flash 读取的 xml 文件
    $smarty->assign('pid',         $pid);
    $smarty->assign('full_page',  1);

    $ads_list = get_adslist();

    $smarty->assign('ads_list',     $ads_list['ads']);
    $smarty->assign('filter',       $ads_list['filter']);
    $smarty->assign('record_count', $ads_list['record_count']);
    $smarty->assign('page_count',   $ads_list['page_count']);

    $sort_flag  = sort_flag($ads_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
	/*
	foreach($_POST["checkbox"] as $value){
	$_POST['checkbox'];
    echo $value."<br/>";
	}
	print_r($_POST['checkbox']) ;
	*/

	//读取 cat_list 数组中的分类的英文名称。
	/*
	foreach($cat_list as $cat_key=>$cat_val) {
	 foreach($cat_val as $cat_key1=>$cat_val1) 
	  {
		if ($cat_key1 == "cat_ename" && !empty($cat_val1)){
			echo $cat_val1."<br />";
		}
	  }
	}
	*/

	//var_dump($cat_list);exit;
	//$b = $_POST["checkbox"] as $value;
	/*
			$strxml = $stritem = "";
	foreach($_POST["checkbox"] as $value){
		foreach($ads_list['ads'] as $key=>$val){
			if ($val['ad_id'] == $value){
			//echo $val['ad_name']."----".$val['position_name']."----".$val['ad_code']."-----".$val['ad_link']."<br />";
			//print_r ($val['ad_name']);
			
			$stritem .="\n\t"."<pic photo=\"{$val['ad_code']}\" lianjie=\"{$val['ad_link']}\"/>";
			$xml = new DOMDocument('1.0');
			$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<photo>{$stritem}\n</photo>";
			$xml->loadXML($xmlstr);
			$xml->save(ROOT_PATH . "flash/channels/".$val['position_name'].".xml");
			}
		}
	}
	*/

			$ads = array();
			foreach($ads_list['ads'] as $Key=>$val)
			{
				$ads[$val['position_name']][] = $val;
			}
			$xml = "";
			foreach($ads as $key=>$val) {
				if(is_array($val)) {
					$xmlfile = ROOT_PATH . "data/flash_channels_ad/" . $key . ".xml";
					$xmlStr = "";
					foreach($val as $ad) {
						if(in_array($ad['ad_id'],$_POST["checkbox"])){
							if(preg_match("/^(http|https|ftp)\:\/\/([a-zA-Z0-9]+\.)?([a-zA-Z0-9]+\.[a-zA-Z0-9]+)+/", $ad['ad_code']))
							{
							$xmlStr .="\n\t"."<pic photo=\"{$ad['ad_code']}\" lianjie=\"{$ad['ad_link']}\"/>";
							} else{
							$xmlStr .="\n\t"."<pic photo=\"{$uri}data/afficheimg/{$ad['ad_code']}\" lianjie=\"{$ad['ad_link']}\"/>";
							}
						}
					}
					$xml = new DOMDocument('1.0');
					$xmlcode = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<photo>{$xmlStr}\n</photo>";
					$xml->loadXML($xmlcode);
					$xmlfile = iconv("UTF-8","GB2312",$xmlfile);//生成中文文件名，的乱码解决问题。Linux 下估计不用。
					$xml->save($xmlfile);
				}
			}

   /* 记录管理员操作 */
   admin_log($_POST['ad_name'], 'edit', 'ads_make_xml');

   clear_cache_files(); // 清除模版缓存

   /* 提示信息	*/
   $href[] = array('text' => $_LANG['back_ads_list'], 'href' => 'ads.php?act=list');
   sys_msg($_LANG['ads_make_xml'] .' '.$_POST['ad_name'].' '. $_LANG['attradd_succed'], 0, $href);
 
}

/* 获取广告数据列表 */
function get_adslist()
{
    /* 过滤查询 */
    $pid = !empty($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;

    $filter = array();
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'ad.ad_name' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = 'WHERE 1 ';
    if ($pid > 0)
    {
        $where .= " AND ad.position_id = '$pid' ";
    }

    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('ad'). ' AS ad ' . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT ad.*, COUNT(o.order_id) AS ad_stats, p.position_name '.
            'FROM ' .$GLOBALS['ecs']->table('ad'). 'AS ad ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('ad_position'). ' AS p ON p.position_id = ad.position_id '.
            'LEFT JOIN ' . $GLOBALS['ecs']->table('order_info'). " AS o ON o.from_ad = ad.ad_id $where " .
            'GROUP BY ad.ad_id '.
            'ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
         /* 广告类型的名称 */
         $rows['type']  = ($rows['media_type'] == 0) ? $GLOBALS['_LANG']['ad_img']   : '';
         $rows['type'] .= ($rows['media_type'] == 1) ? $GLOBALS['_LANG']['ad_flash'] : '';
         $rows['type'] .= ($rows['media_type'] == 2) ? $GLOBALS['_LANG']['ad_html']  : '';
         $rows['type'] .= ($rows['media_type'] == 3) ? $GLOBALS['_LANG']['ad_text']  : '';

         /* 格式化日期 */
         $rows['start_date']    = local_date($GLOBALS['_CFG']['date_format'], $rows['start_time']);
         $rows['end_date']      = local_date($GLOBALS['_CFG']['date_format'], $rows['end_time']);

         $arr[] = $rows;
		 //echo $rows[ad_name]."----".$rows[ad_code]."-----".$rows[ad_link]."<br />";
    }
//print_r($arr);exit;

    return array('ads' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


/**
 * 获得商品分类的所有信息
 *
 * @param   integer     $cat_id     指定的分类ID
 *
 * @return  mix
 */
function get_cat_info($cat_id)
{
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('category'). " WHERE cat_id='$cat_id' LIMIT 1";
    return $GLOBALS['db']->getRow($sql);
}

?>
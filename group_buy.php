<?php

/**
 * ECSHOP 团购商品前台文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: group_buy.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');//add by King, get the bought history

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/*------------------------------------------------------ */
//-- act 操作项的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 团购商品 --> 团购活动商品列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得团购活动总数 */
    $count = group_buy_count();
    if ($count > 0)
    {
        /* 取得每页记录数 */
        $size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        /* 计算总页数 */
        $page_count = ceil($count / $size);

        /* 取得当前页 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 缓存id：语言 - 每页记录数 - 当前页 */
        $cache_id = $_CFG['lang'] . '-' . $size . '-' . $page;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 缓存id：语言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('group_buy_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的团购活动 */
            $gb_list = group_buy_list($size, $page);
            $smarty->assign('gb_list',  $gb_list);

            /* 设置分页链接 */
            $pager = get_pager('group_buy.php', array('act' => 'list'), $count, $page, $size);
            $smarty->assign('pager', $pager);
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        $smarty->assign('categories', get_categories_tree()); // 分类树
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        $smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typegroup_buy.xml" : 'feed.php?type=group_buy'); // RSS URL

		$smarty->assign('pictures',   get_goods_gallery($group_buy['goods_id']));                    // 商品相册  //Add by King

        assign_dynamic('group_buy_list');
    }

        $smarty->assign('now_time',  gmtime());           // 当前系统时间// add by King
        $smarty->assign('tomrrow_time',  strtotime(date(local_date('Y-m-d',gmtime())))+16*3600-1);           // 第二天 0 点之前，大概为23:59:59// add by King
        $smarty->assign('now_m_d',  local_date('nd', gmtime()));           // 当前系统时间的月日// add by King
        $smarty->assign('before_three',  gmtime()-date('O')*72-gmtime()%86400-2*86400);           // 3天前// add by King
        $smarty->assign('after_three',  gmtime()-date('O')*72-gmtime()%86400+5*86400);           // 3天后// add by King //local_date($GLOBALS['_CFG']['time_format'], gmtime()-date('O')*72-gmtime()%86400+5*86400))
//		$smarty->assign('after_sixty',  gmtime()-date('O')*72-gmtime()%86400+55*86400);		//53天后 加上 3天前的，得到56天
		$smarty->assign('this_mon',  this_monday(gmtime()));//获取当前时间的本周周一的时间/*6*86400*/gmtime() - gmtime() % 86400 - date('O') * 36)
		$smarty->assign('next_mon',  next_monday(gmtime()));//下周周一的时间//date('Y-m-d H:i:s',next_monday(gmtime()))
		$smarty->assign('next_month_firstday',  next_month_firstday(gmtime()));//下个月第一天 的时间

    /* 显示模板 *///print_r($GLOBALS);exit;
    $smarty->display('group_buy_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 团购商品 --> 商品详情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 取得参数：团购活动id */
    $group_buy_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if ($group_buy_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得团购活动信息 */
    $group_buy = group_buy_info($group_buy_id);

    if (empty($group_buy))
    {
        ecs_header("Location: ./\n");
        exit;
    }
//    elseif ($group_buy['is_on_sale'] == 0 || $group_buy['is_alone_sale'] == 0)
//    {
//        header("Location: ./\n");
//        exit;
//    }

    /* 缓存id：语言，团购活动id，状态，（如果是进行中）当前数量和是否登录 */
    $cache_id = $_CFG['lang'] . '-' . $group_buy_id . '-' . $group_buy['status'];
    if ($group_buy['status'] == GBS_UNDER_WAY)
    {
        $cache_id = $cache_id . '-' . $group_buy['valid_goods'] . '-' . intval($_SESSION['user_id'] > 0);
    }
    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('group_buy_goods.dwt', $cache_id))
    {
        $group_buy['gmt_end_date'] = $group_buy['end_date'];
        $smarty->assign('group_buy', $group_buy);

        /* 取得团购商品信息 */
        $goods_id = $group_buy['goods_id'];
        $goods = goods_info($goods_id);
        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        $goods['url'] = build_uri('goods', array('gid' => $goods_id), $goods['goods_name']);
        $smarty->assign('gb_goods', $goods);

        /* 取得商品的规格 */
        $properties = get_goods_properties($goods_id);
        $smarty->assign('specification', $properties['spe']); // 商品规格

        //模板赋值
        $smarty->assign('cfg', $_CFG);
        assign_template();

        $position = assign_ur_here(0, $goods['goods_name']);
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置

        $smarty->assign('categories', get_categories_tree()); // 分类树
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        $smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        assign_dynamic('group_buy_goods');

		$smarty->assign('pictures',   get_goods_gallery($group_buy['goods_id']));   // 商品相册  //Add by King
		$smarty->assign('goods_info',   get_goods_info($group_buy['goods_id']));   // 商品相册  //Add by King
		
		/****   Add by King     ******/
		$properties = get_goods_properties($group_buy['goods_id']);  // 获得商品的规格和属性

        $smarty->assign('properties',          $properties['pro']);                              // 商品属性
        $smarty->assign('pro_s_name',          sub_str($properties['pro']['name'],4,false));                      // 商品属性,前4个字符的属性短名称，方便判断，属性做样式
        $smarty->assign('specification',       $properties['spe']);                              // 商品规格
        $smarty->assign('attribute_linked',    get_same_attribute_goods($properties));           // 相同属性的关联商品
		
    }

    //更新商品点击次数
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $group_buy['goods_id'] . "'";
    $db->query($sql);

    $smarty->assign('now_time',  gmtime());           // 当前系统时间
	//print_r(get_goods_info($group_buy['goods_id']));exit;
    $smarty->display('group_buy_goods.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 团购商品 --> 购买
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'buy')
{
    /* 查询：判断是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        show_message($_LANG['gb_error_login'], '', '', 'error');
    }

    /* 查询：取得参数：团购活动id */
    $group_buy_id = isset($_POST['group_buy_id']) ? intval($_POST['group_buy_id']) : 0;
    if ($group_buy_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：取得数量 */
    $number = isset($_POST['number']) ? intval($_POST['number']) : 1;
    $number = $number < 1 ? 1 : $number;

    /* 查询：取得团购活动信息 */
    $group_buy = group_buy_info($group_buy_id, $number);
    if (empty($group_buy))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：检查团购活动是否是进行中 */
    if ($group_buy['status'] != GBS_UNDER_WAY)
    {
        show_message($_LANG['gb_error_status'], '', '', 'error');
    }

    /* 查询：取得团购商品信息 */
    $goods = goods_info($group_buy['goods_id']);
    if (empty($goods))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：判断数量是否足够 */
    if (($group_buy['restrict_amount'] > 0 && $number > ($group_buy['restrict_amount'] - $group_buy['valid_goods'])) || $number > $goods['goods_number'])
    {
        show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
    }

    /* 查询：取得规格 */
    $specs = '';
    foreach ($_POST as $key => $value)
    {
        if (strpos($key, 'spec_') !== false)
        {
            $specs .= ',' . intval($value);
        }
    }
    $specs = trim($specs, ',');

    /* 查询：如果商品有规格则取规格商品信息 配件除外 */
    if ($specs)
    {
        $_specs = explode(',', $specs);
        $product_info = get_products_info($goods['goods_id'], $_specs);
    }

    empty($product_info) ? $product_info = array('product_number' => 0, 'product_id' => 0) : '';

    /* 查询：判断指定规格的货品数量是否足够 */
	if ($product_info['product_number'] != 0){	//add by King  //若属性库存为0，则直接购买，是否减团购库存，仍不知道
		if ($specs && $number > $product_info['product_number'])
		{
			//print_r($specs);exit;//test by King // 此处为 bug 如若 商品属性下无库存，则团购不能购买，或许商品也不能购买，但是商品本身库存确实有存货的，先有的解决办法就是给属性下添加库存
			show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
		}
	}//add by King  //若属性库存为0，则直接购买，是否减团购库存，仍不知道
    /* 查询：查询规格名称和值，不考虑价格 */
    $attr_list = array();
    $sql = "SELECT a.attr_name, g.attr_value " .
            "FROM " . $ecs->table('goods_attr') . " AS g, " .
                $ecs->table('attribute') . " AS a " .
            "WHERE g.attr_id = a.attr_id " .
            "AND g.goods_attr_id " . db_create_in($specs);
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
    }
    $goods_attr = join(chr(13) . chr(10), $attr_list);

    /* 更新：清空购物车中所有团购商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_GROUP_BUY_GOODS);

    /* 更新：加入购物车 */
    $goods_price = $group_buy['deposit'] > 0 ? $group_buy['deposit'] : $group_buy['cur_price'];
    $cart = array(
        'user_id'        => $_SESSION['user_id'],
        'session_id'     => SESS_ID,
        'goods_id'       => $group_buy['goods_id'],
        'product_id'     => $product_info['product_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $goods_price,
        'goods_number'   => $number,
        'goods_attr'     => addslashes($goods_attr),
        'goods_attr_id'  => $specs,
        'is_real'        => $goods['is_real'],
        'extension_code' => addslashes($goods['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_GROUP_BUY_GOODS,
        'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 更新：记录购物流程类型：团购 */
    $_SESSION['flow_type'] = CART_GROUP_BUY_GOODS;
    $_SESSION['extension_code'] = 'group_buy';
    $_SESSION['extension_id'] = $group_buy_id;

    /* 进入收货人页面 */
    ecs_header("Location: ./flow.php?step=consignee\n");
    exit;
}

/* 取得团购活动总数 */
function group_buy_count()
{
    $now = gmtime();
    $sql = "SELECT COUNT(*) " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_type = '" . GAT_GROUP_BUY . "' " .
            "AND start_time <= '$now' AND is_finished < 3";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某页的所有团购活动
 * @param   int     $size   每页记录数
 * @param   int     $page   当前页
 * @return  array
 */
function group_buy_list($size, $page)
{
	//$size = 7 ;// 团购 7 个便翻页 // Add by King
	$this_week_monday = this_monday(gmtime());//获取当前时间的本周周一的时间 // Add by King
    /* 取得团购活动 */
    $gb_list = array();
    $now = gmtime();
    $sql = "SELECT b.*, IFNULL(g.goods_thumb, '') AS goods_thumb, IFNULL(g.goods_img, '') AS goods_img, b.act_id AS group_buy_id, ".//edit by King// p.img_url AS img_url, p.img_desc AS img_desc, p.img_id AS img_id, //
                "b.start_time AS start_date, b.end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS b " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON b.goods_id = g.goods_id " .
//                "LEFT JOIN " . $GLOBALS['ecs']->table('goods_gallery') . " AS p ON b.goods_id = p.goods_id AND p.img_desc = 'groupon' " .//Add by King// //
            "WHERE b.act_type = '" . GAT_GROUP_BUY . "' " .
//            "AND b.start_time <= '$now' AND b.is_finished < 3 ORDER BY b.act_id DESC";
            "AND b.end_time >= $this_week_monday ORDER BY b.end_time ASC";//edit by King, show all group_buy ,团购开始时间和结束的判断可以由前台判断。只是有1小时缓存。DESC
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    while ($group_buy = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($group_buy['ext_info']);
        $group_buy = array_merge($group_buy, $ext_info);

        /* 格式化时间 */
        $group_buy['formated_start_date']   = local_date($GLOBALS['_CFG']['time_format'], $group_buy['start_date']);
        $group_buy['formated_end_date']     = local_date($GLOBALS['_CFG']['time_format'], $group_buy['end_date']);
        $group_buy['formated_start_am']		= local_date("a", $group_buy['start_date']);//Add by King, time
        $group_buy['formated_start_time']   = local_date("h:i", $group_buy['start_date']);//Add by King, time
        $group_buy['formated_end_week']     = local_date("D", $group_buy['end_date']);//Add by King, week
        $group_buy['formated_end_day']      = local_date("d", $group_buy['end_date']);//Add by King, day
        $group_buy['formated_end_n_d']      = local_date("nd", $group_buy['end_date']);//Add by King, day
        $group_buy['formated_end_d_M']      = local_date("d. M", $group_buy['end_date']);//Add by King, day
		$group_buy['before_three']			= $group_buy['start_date']-3*86400;//Add by King, day
		$group_buy['after_three']			= $group_buy['end_date']+3*86400;//Add by King, day
        $group_buy['formated_start_day']    = local_date('d', $group_buy['end_date'] - 60*60*24*6);//Add by King, day

        /* 格式化保证金 */
        $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

        /* 处理价格阶梯 */
        $price_ladder = $group_buy['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder))
        {
            $price_ladder = array(array('amount' => 0, 'price' => 0));
        }
        else
        {
            foreach ($price_ladder as $key => $amount_price)
            {
                $price_ladder[$key]['formated_price'] = price_format($amount_price['price']);
            }
        }
        $group_buy['price_ladder'] = $price_ladder;

        /* 处理图片 */
        if (empty($group_buy['goods_thumb']))
        {
            $group_buy['goods_thumb'] = get_image_path($group_buy['goods_id'], $group_buy['goods_thumb'], true);
        }
			/* get the pictures, start by King */

		$sql="select img_url from ".$GLOBALS['ecs']->table('goods_gallery')." where img_id >= (select MAX(img_id) from ".$GLOBALS['ecs']->table('goods_gallery')." where goods_id='{$group_buy['goods_id']}' AND img_desc = 'groupon')";
		$img_url=$GLOBALS['db']->getOne($sql);
		$group_buy['img_url']=$img_url;

			/* get the pictures, end by King */
        /* 处理链接 */
        $group_buy['url'] = build_uri('group_buy', array('gbid'=>$group_buy['group_buy_id']));
        /* 加入数组 */
        $gb_list[] = $group_buy;
//	print_r($group_buy);exit;
    }
    return $gb_list;
}



?>
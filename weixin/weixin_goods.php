<?php
require "weixin_config.php";
if(checkToken() == false) exit('404');
require('../includes/init.php');
$weburl = $_SERVER['SERVER_NAME'] ? "http://".$_SERVER['SERVER_NAME']."/" : "http://".$_SERVER['HTTP_HOST']."/";
$order = " order by is_best desc,is_new desc,is_hot desc,is_promote desc,last_update desc,goods_id desc";
if($_GET['key']){
	$size = 10;
	$page = 1;
	$key = htmlspecialchars($_GET['key'],ENT_QUOTES);
	$condi = "(goods_sn like '%{$key}%' or goods_name like '%{$key}%' or keywords like '%{$key}%' or goods_desc like '%{$key}%')";
	$condi .= " and is_delete = 0 and is_on_sale = 1 and is_alone_sale = 1";
	$res = $db->SelectLimit("select goods_id,goods_name,shop_price,promote_price,promote_start_date,promote_end_date from {$ecs->table('goods')} where {$condi} {$order}", $size, ($page - 1) * $size);
	while ($row = $db->FetchRow($res)){
		$promote_price = 0;
		if ($row['promote_price'] > 0){
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name']      = $row['goods_name'];
        $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
    }
	$arr = $arr ? $arr : get_recommend_goods('best');
	if($arr){
		$str = "查询到的商品：\r\n";
		foreach($arr as $v){
			$str .= "03{$v['goods_id']} {$v['shop_price']} {$v['goods_name']}\r\n";
		}
		echo $str;
	}else{
		echo "没有找到相关商品，请尝试其他关键字";
	}
}elseif($_GET['gid']){
	$res = $db->getRow("select goods_id,goods_name,shop_price,goods_desc,goods_thumb,promote_price,promote_start_date,promote_end_date from {$ecs->table('goods')} where goods_id=".intval($_GET['gid']));
	if($res){
		$promote_price = $res['promote_price'] > 0 ? bargain_price($res['promote_price'], $res['promote_start_date'], $res['promote_end_date']) : 0;
		$description = $promote_price ? "【促销价:".price_format($promote_price)."】" : "【售价：".price_format($res['shop_price'])."】";
		$description .= strip_tags($res['goods_desc']);
		echo json_encode(array(
			'title'=>$res['goods_name'],
			'description'=>$description,
			'url'=>"{$weburl}mobile/goods.php?id=".$res['goods_id'],
			'picurl'=>"{$weburl}".$res['goods_thumb'],
		));
	}else{
		echo 0;
	}
}else{
	echo 'error';
}
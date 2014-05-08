<?php

/**
 * ECSHOP 商品相关函数库
 * ============================================================================
 * * 版权所有 2005-2013 有限公司，并保留所有权利。
 * 网站地址: http://www.mensacake.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: Zh $
 * $Id: lib_goods_gallery.php 17217 2013-08-06 10:29:08Z Zh $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 获得指定商品的相册
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_goods_gallery_mensa($goods_id)
{
    $sql = 'SELECT img_id, img_url, thumb_url, img_desc' .
        ' FROM ' . $GLOBALS['ecs']->table('goods_gallery') .
        " WHERE goods_id = '$goods_id' AND img_desc != '0' AND img_desc != 'groupon' LIMIT " . $GLOBALS['_CFG']['goods_gallery_number'];
    $row = $GLOBALS['db']->getAll($sql);

    /* 格式化相册图片路径 */
	foreach($row as $key => $gallery_img)
    {
        $row[$key]['img_url'] = get_image_path($goods_id, $gallery_img['img_url'], false, 'gallery');
        $row[$key]['thumb_url'] = get_image_path($goods_id, $gallery_img['thumb_url'], true, 'gallery');
    }
    return $row;
}


?>
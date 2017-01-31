<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodule.kr)
 *
 * 배너 템플릿에 따라 배너컨텐츠를 보여준다.
 * 
 * @file /modules/banner/widgets/banner/index.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */

if (defined('__IM__') == false) exit;

$group = $Widget->getValue('group');
$cache = $Widget->getValue('cache');

if ($Widget->checkCache() < time() - $cache) {
	$banners = $me->getBanners($group);
	$Widget->storeCache(json_encode($banners,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
} else {
	$banners = json_decode($Widget->getCache());
}

return $Widget->getTemplet()->getContext('index',get_defined_vars());
?>
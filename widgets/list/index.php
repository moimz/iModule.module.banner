<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너 템플릿에 따라 배너컨텐츠를 보여준다.
 * 
 * @file /modules/banner/widgets/banner/index.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.2.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$bid = $Widget->getValue('bid');
$cache = $Widget->getValue('cache');

if ($Widget->checkCache() < time() - $cache) {
	$items = $me->getItems($bid);
	$Widget->storeCache(json_encode($items,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
} else {
	$items = json_decode($Widget->getCache());
}

return $Widget->getTemplet()->getContext('index',get_defined_vars());
?>
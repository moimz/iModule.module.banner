<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너를 삭제한다.
 * 
 * @file /modules/banner/process/@deleteBanner.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 11. 22.
 */
if (defined('__IM__') == false) exit;

$idx = json_decode(Request('idx'));
if (is_array($idx) == true) {
	$this->db()->delete($this->table->banner)->where('idx',$idx,'IN')->execute();
	$results->success = true;
} else {
	$results->success = false;
}
?>
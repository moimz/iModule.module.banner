<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너 정보를 가져온다.
 * 
 * @file /modules/banner/process/@getBanner.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx');
$data = $this->db()->select($this->table->banner)->where('idx',$idx)->getOne();

$results->success = true;
$results->data = $data;
?>
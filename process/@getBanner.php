<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 그룹 정보를 가져온다.
 * 
 * @file /modules/banner/process/@getBanner.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$bid = Param('bid');
$data = $this->db()->select($this->table->banner)->where('bid',$bid)->getOne();

$results->success = true;
$results->data = $data;
?>
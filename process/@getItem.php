<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너 정보를 가져온다.
 * 
 * @file /modules/banner/process/@getItem.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx');
$data = $this->db()->select($this->table->item)->where('idx',$idx)->getOne();

if ($data->image > 0) {
	$data->image = $this->IM->getModule('attachment')->getFileInfo($data->image);
} else {
	$data->image = null;
}

$data->title_color_default = $data->title_color == '';
$data->text_color_default = $data->text_color == '';
$data->title_color = $data->title_color == '' ? '#000000' : $data->title_color;
$data->text_color = $data->text_color == '' ? '#000000' : $data->text_color;

$results->success = true;
$results->data = $data;
?>
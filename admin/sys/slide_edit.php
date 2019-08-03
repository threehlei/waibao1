<?php
require_once '../../include/common.inc.php';
require_once '../function.php';
if (stripos(auth_group($_SESSION['login_gid']), 'sys_slide') === false) exit("没有权限！");
if ($act == "slide_add") {
	slide_add($title, $url, $ico, $w, $h, $target, $s, $ov);
	$id = $db->insert_id();
	$type = 'edit';
} else if ($act == "slide_edit") {
	slide_edit($id, $title, $url, $ico, $w, $h, $target, $s, $ov);
}
$query = $db->query("select * from {$tablepre}slide where id='$id'");
if ($db->num_rows($query) > 0) $row = $db->fetch_row($query); else {
	$row[s] = '0';
	$row[target] = "POPWin";
} ?>
<!DOCTYPE HTML>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="../assets/css/dpl-min.css" rel="stylesheet" type="text/css"/>
	<link href="../assets/css/bui-min.css" rel="stylesheet" type="text/css"/>
	<link href="../assets/css/page-min.css" rel="stylesheet" type="text/css"/>
	<!-- 下面的样式，仅是为了显示代码，而不应该在项目中使用-->
	<link href="../assets/css/prettify.css" rel="stylesheet" type="text/css"/>
	<style type="text/css">
		code {
			padding: 0px 4px;
			color: #d14;
			background-color: #f7f7f9;
			border: 1px solid #e1e1e8;
		}

		input, select {
			vertical-align: middle;
		}

		.liw {
			width: 160px;
			height: 25px;
			line-height: 25px;
		}
	</style>
</head>
<body>
<div class="container" style="margin-bottom:50px;">

	<form action="?id=<?= $id ?>&type=<?= $type ?>" method="post" enctype="application/x-www-form-urlencoded">


		<table class="table table-bordered table-hover definewidth m10">

			<tr>
				<td width="30" class="tableleft" style="width:80px;">广告名称：</td>
				<td><input name="title" type="text" id="title" value="<?= $row[title] ?>"/></td>
			</tr>
			<tr>
				<td class="tableleft">广告图片：</td>
				<td><input name="ico" type="text" id="ico" value="<?= $row[ico] ?>" class="input-large"/>
				  <br>
			  <br>
					<button type="button" class="button button-mini button-success"><span id="url_bt"></span></button>				</td>
		  </tr>
			<tr>
				<td class="tableleft">广告连接：</td>
				<td><input name="url" type="text" id="url" value="<?= $row[url] ?>" class="input-large"/></td>
			</tr>
			<tr>
				<td class="tableleft">打开方式：</td>
				<td><select name="target" id="target">
						<option value="<?= $row[target] ?>"><?= $row[target] == "NewWin" ? '新窗口' : ($row[target] == "CtWin" ? '当前窗口' : ($row[target] == "POPWin" ? '弹出框' : '气泡框'));  ?></option>
						<option value="NewWin">新窗口</option>
                        <option value="CtWin">当前窗口</option>
						<option value="POPWin">弹出框</option>
						<option value="QPWin">气泡框</option>
					</select></td>
			</tr>
			<tr>
				<td class="tableleft">窗体宽高：</td>
				<td><input name="w" type="text" id="w" value="<?= $row[w] ?>"/>
					宽（像素）
					<br>
					<br>
					<input name="h" type="text" id="h" value="<?= $row[h] ?>"/>
					高（像素）				</td>
			</tr>
			<tr>
				<td class="tableleft">是否启用：</td>
				<td><select name="s" id="s">
						<option value="<?= $row[s] ?>"><?= $row[s] == "1" ? '未启用' : '启用' ?></option>
						<option value="0">启用</option>
						<option value="1">未启用</option>
					</select>
					状态<br>
					<br>
					<input name="ov" type="text" id="ov" value="<?= $row[ov] ?>"/>
					排序（大至小排序）				</td>
			</tr>
		</table>

  <div style="position:fixed; bottom:0; background: #FFF; width:100%; padding-top:5px;">
			<button type="submit" class="button button-success">确定</button>
			<button type="button" class="button" onClick="window.parent.dialog.close()">关闭</button>
			<input type="hidden" name="act" value="slide_<?= $type ?>">
			<input type="hidden" name="id" value="<?= $id ?>">
			<input type="hidden" name="type" value="<?= $type ?>">
		</div>
	</form>

</div>
<script type="text/javascript" src="../assets/js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="../assets/js/bui.js"></script>
<script type="text/javascript" src="../assets/js/config.js"></script>
<script type="text/javascript" src="../../upload/swfupload/swfupload.js"></script>
<script>
	function swfupload_ok(fileObj, server_data) {

		var data = eval("(" + server_data + ")");
		$("#" + data.msg.info).val(data.msg.url);
	}
	$(function () {


		var swfdef = {
			// 按钮设置
			file_post_name: "filedata",
			button_width: 30,
			button_height: 18,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,
			button_text: '上传',
			button_text_style: ".upbnt{ color:#00F}",
			button_text_left_padding: 0,
			button_text_top_padding: 0,
			upload_success_handler: swfupload_ok,
			file_dialog_complete_handler: function () {
				this.startUpload();
			},
			file_queue_error_handler: function () {
				alert("选择文件错误");
			}
		}
		swfdef.flash_url = "../../upload/swfupload/swfupload.swf";
		swfdef.button_placeholder_id = "url_bt";
		swfdef.file_types = "*.jpg;*.gif;*.png";
		swfdef.upload_url = "../../upload/upload.php";
		swfdef.post_params = {"info": "ico"}

		swfu = new SWFUpload(swfdef);

	});


</script>
</body>
</html>
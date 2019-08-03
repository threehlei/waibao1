<?php
require_once './include/common.inc.php';
switch ($act) {
	case "edit":
		$uid = $_SESSION['login_uid'];
		$guestexp = '^Guest|' . $cfg['config']['regban'] . "Guest";
		$query = $db->query("select uid,nickname from {$tablepre}memberfields where nickname='$nickname' ");
		$userarr = $db->fetch_row($query);
		if ($db->num_rows($query) > 0 && $userarr['uid'] != $uid) {
			$msg = "<script>$('.tab').hide();$('#tab_2').show();alert('昵称已经被占用！');</script>";
			$db->query("update {$tablepre}memberfields set mood='$mood' where uid='$uid'");
			$db->query("update {$tablepre}members set  email='$email',realname='$realname',phone='$phone',sex='$sex' where uid='$uid'");
		} elseif (preg_match("/\s+|{$guestexp}/is", $nickname) && !check_auth('room_admin')) {
			$msg = "<script>$('.tab').hide();$('#tab_2').show();alert('昵称禁用！');</script>";
			$db->query("update {$tablepre}memberfields set mood='$mood' where uid='$uid'");
			$db->query("update {$tablepre}members set  email='$email',realname='$realname',phone='$phone',sex='$sex' where uid='$uid'");
		} else {
			$db->query("update {$tablepre}memberfields set nickname='$nickname',mood='$mood' where uid='$uid'");
			$db->query("update {$tablepre}members set  email='$email',realname='$realname',phone='$phone',sex='$sex' where uid='$uid'");
			header("location:?uid={$uid}");
		}
		break;
	case "editpwd":
		$uid = $_SESSION['login_uid'];
		if ($pwd1 != $pwd2) {
			$msg = "<script>$('.tab').hide();$('#tab_3').show();alert('新密码不一致！');</script>";
		} else {
			$query = $db->query("select * from {$tablepre}members where uid='$uid' and password='" . md5($oldpwd) . "'");
			if ($db->num_rows($query) > 0) {
				$db->query("update {$tablepre}members set  password='" . md5($pwd1) . "' where uid='$uid'");
				$msg = "<script>$('.tab').hide();$('#tab_3').show();alert('密码已修改！');</script>";
			} else $msg = "<script>$('.tab').hide();$('#tab_3').show();alert('旧密码错误！');</script>";
		}
		break;
}
if (isset($editface) and isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
	$filename = './face/' . $editface . '/' . $_SESSION['login_uid'] . '.gif';
	$somecontent = $GLOBALS["HTTP_RAW_POST_DATA"];
	if (!$handle = fopen($filename, 'w+')) {
		print '{code:"#1057", msg:"不能打开文"}';
		exit;
	}
	if (!fwrite($handle, $somecontent)) {
		print '{code:"#1058", msg:"不能打开文"}';
		exit;
	}
	print '{code:"#1057", msg:"成功"}';
	fclose($handle);
	exit();
}
$userinfo = $db->fetch_row($db->query("select m.*,ms.* from {$tablepre}members m,{$tablepre}memberfields ms  where m.uid=ms.uid and m.uid='{$uid}'")); ?>
<!doctype html>
<html>
<head>
	<meta name="renderer" content="webkit">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>
	<link href="./images/base.css" rel="stylesheet" type="text/css"/>
	<script src="script/jquery.min.js"></script>
	<script src="script/swfobject2.js"></script>
	<title>用户资料</title>
	<style>
		.main {
			width: 460px;
			background: #FDFDFD
		}

		.head {
			height: 119px;
			padding-top: 80px;
			background: url(images/b1.jpg) no-repeat
		}

		#nav {
			border-top: 1px dotted #FFFFFF;
			width: 460px;
		}

		#nav a {
			float: left;
			display: block;
			padding: 5px;
			width: 60px;
			color: #FFF;
			background: #09F;
			font-size: 12px;
			text-align: center;
			text-decoration: none;
		}

		#tab {
			width: 460px;
			height: 400px;
			overflow: hidden;
			font-size: 12px;
			color: #666
		}

		#tab td {
			height: 30px;
			line-height: 30px;
		}

		#tab tr {
			border-bottom: 1px dotted #EEEEEE
		}

		#tab input {
			border: 0px;
			border-bottom: 1px solid #333;
			width: 240px;
		}

		.th {
			text-align: justify;
			text-justify: distribute-all-lines;
			text-align-last: justify;
			width: 60px;
			padding-right: 10px;
			color: #333
		}

		.user_pic {
			width: 120px;
			height: 150px;
			position: absolute;
			top: 203px;
			right: 0px;
			border: 5px solid #FFF
		}

		.user_pic img {
			width: 110px;
			height: 150px;
			border: 1px #CCCCCC solid;
			padding: 1px;
		}

		.bc {
			border: 0px;
			padding: 2px;
			width: 40px;
			height: 20px;
			color: #FFF;
			background: #0C0
		}

		.user_edit_pic {
			width: 460px;
			height: 350px;
			position: absolute;
			top: 203px;
			right: 0px;
			background: #FDFDFD;
			text-align: center;
			padding-top: 30px;
			display: none
		}
	</style>
</head>

<body>
<div class="main">
	<div class="head">
		<div class="m10 fl" style="text-shadow:2px 2px 2px #000;">
			<div class="fl mr10" style="width:74px; height:74px;"><img
					src='../face/img.php?t=p1&u=<?= $userinfo['uid'] ?>' style="width:74px; height:74px; border:0px"/>
			</div>
			<div class="fl" style="width:350px; height:74px;">
				<div style="height:34px; overflow:hidden"><span class="ttff f24 cfff">
          <?= $userinfo['nickname'] ?>
          </span> &nbsp;<span class="ttff  f14 cfff">
          <?= $userinfo['username'] ?>
          </span></div>
				<div>
					<?= showstars($userinfo['onlinetime']) ?>
				</div>
				<div class="ttff f14 cfff" style="height:20px; overflow:hidden">
					<?= $userinfo['mood'] ?>
				</div>
			</div>
		</div>
		<div class="cl"></div>
		<div class="fl" id="nav">
			<a href="javascript://" onClick="$('.tab').hide();$('#tab_1').show()">用户资料</a>
			<?php
			if ($uid == $_SESSION['login_uid']) { ?>
				<a href="javascript://" style="background:#F63" onClick="$('.tab').hide();$('#tab_2').show()">编辑资料</a>
				<a href="javascript://" style="background:#666" onClick="$('.tab').hide();$('#tab_3').show()">修改密码</a>
			<?php } ?>
		</div>
		<div class="cl"></div>
	</div>
	<div id="tab">
		<div id="tab_1" style="margin:10px;" class="ttff tab">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="th">用户性别：</td>
					<td><?php $sex = array("女", "男", "保密");
						echo $sex[$userinfo['sex']]; ?>
						&nbsp;</td>
				</tr>
				<tr>
					<td class="th">在线时长：</td>
					<td><?php echo round($userinfo['onlinetime'] / 60 / 60, 2) . '小时'; ?>&nbsp; 登录
						<?= $userinfo['logins'] ?>
						次
					</td>
				</tr>
				<tr>
					<td class="th">注册时间：</td>
					<td><?php echo date('Y-m-d H:i:s', $userinfo['regdate']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="th">用户组：</td>
					<td><?php $arr = group_info($userinfo['gid']);
						echo $arr['title'] . "-" . $arr['sn']; ?>
						&nbsp;</td>
				</tr>
				<tr>
					<td class="th">推广链接：</td>
					<td><?php echo "http://".$_SERVER['HTTP_HOST']."/?tg=".$userinfo['uid'];?></td>
				</tr>
				<?php
				if ($userinfo['gid'] == '3') { ?>
					<tr>
						<td class="th">客服手机：</td>
						<td><?= $userinfo['phone'] ?>
							&nbsp;</td>
					</tr>
					<tr>
						<td class="th">客服QQ：</td>
						<td><a href="http://wpa.qq.com/msgrd?v=3&uin=<?= $userinfo['realname'] ?>&site=qq&menu=yes">
								<?= $userinfo['realname'] ?></a>
							&nbsp;</td>
					</tr>
				<?php
				}
				if (check_auth('user_info_gl')) { ?>
					<tr>
						<td class="th">登录IP：</td>
						<td><?= $userinfo['regip'] ?>
							&nbsp;</td>
					</tr>
					<tr>
						<td class="th">邮箱：</td>
						<td><?= $userinfo['email'] ?>
							&nbsp;</td>
					</tr>
					<tr>
						<td class="th">手机：</td>
						<td><?= $userinfo['phone'] ?>
							&nbsp;</td>
					</tr>
					<tr>
						<td class="th">QQ：</td>
						<td><a href="http://wpa.qq.com/msgrd?v=3&uin=<?= $userinfo['realname'] ?>&site=qq&menu=yes">
								<?= $userinfo['realname'] ?></a>
							&nbsp;</td>
					</tr>
					<tr>
						<td class="th">用户关联：</td>
						<td>用户客服：
							<?= $userinfo['fuser'] ?>
							&nbsp; 推广人：
							<?= $userinfo['tuser'] ?></td>
					</tr>
					<tr>
						<td class="th">备注：</td>
						<td><?= $userinfo['sn'] ?>
							&nbsp;</td>
					</tr>
				<?php } ?>
			</table>
			<div class="user_pic"><img src="../face/img.php?t=p2&u=<?= $userinfo['uid'] ?>"></div>
		</div>

		<div id="tab_2" style="margin:10px;" class="ttff tab hide ">
			<form action="" method="post" enctype="application/x-www-form-urlencoded">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">

					<tr>
						<td class="th">用户昵称：</td>
						<td><input name="nickname" type="text" id="nickname" value="<?= $userinfo['nickname'] ?>"><input
								type="hidden" name="uid" value="<?= $userinfo['uid'] ?>"><input type="hidden" name="act"
																								value="edit"></td>
					</tr>
					<tr>
						<td class="th">用户签名：</td>
						<td><input name="mood" type="text" id="mood" value="<?= $userinfo['mood'] ?>"></td>
					</tr>
					<tr>
						<td class="th">联系邮箱：</td>
						<td><input name="email" type="text" id="email" value="<?= $userinfo['email'] ?>"></td>
					</tr>
					<tr>
						<td class="th">联系QQ：</td>
						<td><input name="realname" type="text" id="realname" value="<?= $userinfo['realname'] ?>">
						</td>
					</tr>
					<tr>
						<td class="th">联系手机：</td>
						<td><input name="phone" type="text" id="phone" value="<?= $userinfo['phone'] ?>">
						</td>
					</tr>
					<tr>
						<td class="th">联系性别：</td>
						<td><select name="sex" id="sex">
								<option value="<?= $userinfo['sex'] ?>"><?php $sex = array("女", "男", "保密");
									echo $sex[$userinfo['sex']]; ?></option>
								<option value="1">男</option>
								<option value="0">女</option>
								<option value="2">保密</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="th">用户头像：</td>
						<td><span id="ep1"></span> <span id="ep2"></span></td>
					</tr>
					<tr>
						<td class="th">&nbsp;</td>
						<td><input type="submit" value="保存" class="bc" style="width:40px; border:0px;"></td>
					</tr>
				</table>
			</form>
			<div class="user_pic"><img src="../face/img.php?t=p2&u=<?= $userinfo['uid'] ?>"></div>
			<div class="user_edit_pic">
				<div id="epf"></div>
				<input type="button" onClick="saveToServer();" class="bc" value="确定" style="width:40px; border:0px;">
				<input type="button" onClick="$('.user_edit_pic').hide()" class="bc" value="取消"
					   style="width:40px; border:0px; background:#666"></div>
		</div>

		<div id="tab_3" style="margin:10px;" class="ttff tab hide">
			<form action="" method="post" enctype="application/x-www-form-urlencoded">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">

					<tr>
						<td class="th">旧密码：</td>
						<td><input name="oldpwd" type="password" id="oldpwd"><input type="hidden" name="uid"
																					value="<?= $userinfo['uid'] ?>"><input
								type="hidden" name="act" value="editpwd"></td>
					</tr>
					<tr>
						<td class="th">新密码：</td>
						<td><input name="pwd1" type="password" id="pwd1"></td>
					</tr>
					<tr>
						<td class="th">确认密码：</td>
						<td><input name="pwd2" type="password" id="pwd2"></td>
					</tr>
					<tr>
						<td class="th">&nbsp;</td>
						<td><input type="submit" value="保存" class="bc" style="width:40px; border:0px;"></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $msg ?>
<script type="text/javascript" src="../upload/swfupload/swfupload.js"></script>
<script>
	function thisMovie(movieName) {
		if (navigator.appName.indexOf("Microsoft") != -1) {
			return window[movieName];
		}
		else {
			return document[movieName];
		}
	}
	function loadImage() {
		thisMovie("PhotoEditor").loadImage(img);
	}
	function flashInit(arg) {
		setTimeout('loadImage()', 1000);
	}
	function init() {
	}
	function flashError(event) {
		//alert(event.code);
		switch (event.code) {
			case 0:
				if (event.msg == "图片加载成功！") {
					return;
				}
				if (1) {
					location.reload();
				}
				break;
			case "#1057":
				//Fid('errDiv').innerHTML = '选择的照片太小，请';
				break;
		}
	}
	function saveToServer() {
		var b = thisMovie('PhotoEditor').saveToServer('profiles.php?editface=' + ept + '&w');
	}
	function swfupload_ok(fileObj, server_data) {
		$('.user_edit_pic').show();
		var data = eval("(" + server_data + ")");
		img = data.msg.url;
		var wh;
		if (data.msg.info == "ep1") {
			wh = "slice_width=100&slice_height=100";
			ept = 'p1'
		}
		else {
			wh = "slice_width=110&slice_height=150";
			ept = 'p2'
		}

		var so = new SWFObject("../images/PhotoEditor.swf?imageEncoder=jpg&jpgQuality=97&" + wh, 'PhotoEditor', "325", "312", "9", "#FFF");
		so.write("epf");

		//alert(server_data);
		//var data=eval("("+server_data+")") ;
		//$("#"+data.msg.info).val(data.msg.url);
	}
	$(function () {


		var swfdef = {
			// 按钮设置
			file_post_name: "filedata",
			button_width: 60,
			button_height: 28,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			button_cursor: SWFUpload.CURSOR.HAND,
			button_text: '修改头像',
			button_text_style: ".upbnt{}",
			button_text_left_padding: 0,
			button_text_top_padding: 10,
			upload_success_handler: swfupload_ok,
			file_dialog_complete_handler: function () {
				this.startUpload();
			},
			file_queue_error_handler: function () {
				alert("选择文件错误");
			}
		}
		swfdef.flash_url = "../upload/swfupload/swfupload.swf";
		swfdef.button_placeholder_id = "ep1";
		swfdef.file_types = "*.png;*.jpg;*.gif";
		swfdef.upload_url = "../upload/upload.php";
		swfdef.post_params = {"info": "ep1"}

		swfu = new SWFUpload(swfdef);

		var swfico = swfdef;
		swfico.button_text = '修改照片';
		swfico.button_placeholder_id = "ep2";
		swfico.post_params = {"info": "ep2"}
		swfuico = new SWFUpload(swfico);

	});


</script>
</body>
</html>
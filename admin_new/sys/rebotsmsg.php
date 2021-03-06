<?php
require_once '../../include/common.inc.php';
require_once '../function.php';
if (stripos(auth_group($_SESSION['login_gid']), 'sys_rebots') === false) exit("没有权限！");
$auth_rule_userdel = false;
if (stripos(auth_group($_SESSION['login_gid']), 'users_del') !== false) $auth_rule_userdel = true; ?>
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
	</style>
	<script>
		Date.prototype.Format = function (fmt) { //author: meizz
			var o = {
				"M+": this.getMonth() + 1, //月份
				"d+": this.getDate(), //日
				"h+": this.getHours(), //小时
				"m+": this.getMinutes(), //分
				"s+": this.getSeconds(), //秒
				"q+": Math.floor((this.getMonth() + 3) / 3), //季度
				"S": this.getMilliseconds() //毫秒
			};
			if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
			for (var k in o)
				if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
			return fmt;
		}
		function ftime(time) {
			return new Date(time * 1000).Format("yyyy-MM-dd hh:mm");
			;
		}
		function ftime2(time) {
			if (time > (60 * 60 * 24)) return parseInt(time / (60 * 60 * 24)) + "天";
			else if (time > (60 * 60))return parseInt(time / (60 * 60)) + "小时";
			else if (time > 60)return parseInt(time / 60) + "分钟";
			else return parseInt(time) + "秒";
		}

	</script>
</head>
<body>
<div class="container" style=" min-width:1300px;">
	<form class="form-horizontal" action="" method="get">
		<ul class="breadcrumb">
			<li class="active">关键字：
				<input type="text" name="skey" id="rolename" class="abc input-default" placeholder="">
				按月份统计：
				<input type="text" name="ym" id="ym" class="calendar" value="<?= $ym ?>">
				&nbsp;&nbsp;
				<button type="submit" class="button ">查询</button>

				&nbsp;&nbsp; 发言人为查询字段
			</li>

		</ul>
	</form>
	<form action="" method="POST" enctype="application/x-www-form-urlencoded" class="form-horizontal" id="hd_list">
		<input type="hidden" name="gid" value="<?= $gid ?>">
		<input type="hidden" name="act" value="user_del">
		<table class="table table-bordered table-hover definewidth m10">
			<thead>
			<tr style="font-weight:bold">
				<td width="60" align="center" bgcolor="#FFFFFF">发言人昵称</td>
				<td width="60" align="center" bgcolor="#FFFFFF">发言数</td>

				<td width="120" align="center" bgcolor="#FFFFFF">操作</td>
			</tr>
			</thead>
			<?php
			$sql = "select tuser,count(*) as t1 from {$tablepre}chatlog where id!=0 and tuser is not NUll ";
			if ($ym != "") {
				$sql .= " and FROM_UNIXTIME(mtime,'%Y-%m')='$ym'";
			}
			$sql .= " GROUP BY tuser ";
			if ($skey != "") {
				$sql .= " HAVING tuser like '%$skey%'";
			}
			$sql .= "ORDER BY t1 DESC";
			if (!$auth_rule_userdel) $display_delbutton = 'style="display:none"';
			echo userlist(20, $sql, '
    <tr>
     <td align="center" bgcolor="#FFFFFF">{tuser}&nbsp;</td>
    <td align="center" bgcolor="#FFFFFF">{t1}&nbsp;</td>
      <td align="center" bgcolor="#FFFFFF">
      <button type="button" class="button button-mini button-info" onClick="openUser(\'{tuser}\')" ><i class="x-icon x-icon-small icon-wrench icon-white"></i>查看</button>
   </td>
    </tr>
') ?>


		</table>
	</form>
	<ul class="breadcrumb">
		<li class="active"><?= $pagenav ?>
		</li>
	</ul>
</div>
<script type="text/javascript" src="../assets/js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="../assets/js/bui.js"></script>
<script type="text/javascript" src="../assets/js/config.js"></script>

<script>
	BUI.use('bui/calendar', function (Calendar) {
		var datepicker = new Calendar.DatePicker({
			trigger: '.calendar',
			dateMask: 'yyyy-mm',
			autoRender: true
		});
	});
	BUI.use('bui/overlay', function (Overlay) {
		dialog = new Overlay.Dialog({
			title: '发言列表',
			width: 1000,
			height: 500,
			buttons: [],
			bodyContent: ''
		});
	});
	function openUser(tuser) {
		var ym = $("#ym").val();
		dialog.set('bodyContent', '<iframe src="rebotsmsg_list.php?tuser=' + tuser + '&ym=' + ym + '" scrolling="yes" frameborder="0" height="100%" width="100%"></iframe>');
		dialog.updateContent();
		dialog.show();
	}

</script>
</body>
</html>
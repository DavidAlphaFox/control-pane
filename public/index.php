<?php
if(preg_match('/(?i)msie [5-9]/',$_SERVER['HTTP_USER_AGENT']))
{
	echo '<!DOCTYPE html><div style="margin-top:10%;text-align:center;font-size:large;color:darkred;"><p>Sorry, your browser is not supported!</p><p>Please, use last version of any browser.</p></html>';
	exit;
}

$_REALPATH=realpath('../');
$uri=trim($_SERVER['REQUEST_URI'],'/');
include($_REALPATH.'/php/clonos.php');
$clonos=new ClonOS($_REALPATH,$uri);
//echo json_encode($clonos->config->os_types);exit;
if(isset($_GET['upload']))
{
	include('upload.php');
	$clonos->register_media($path,$file,$ext);
	exit;
}
if(isset($_GET['download']))
{
	include('download.php');
	$clonos->register_media($path,$file,$ext);
	exit;
}

$lang=$clonos->getLang();
$root=trim($_SERVER['DOCUMENT_ROOT'],DIRECTORY_SEPARATOR);
$_ds=DIRECTORY_SEPARATOR;


$chunks=$clonos->uri_chunks;
if(!empty($chunks) && count($chunks)>1) $uri=$chunks[0];


$file_path=$_ds.$root.$_ds.'pages'.$_ds.$uri.$_ds;
$file_name=$file_path.$lang.'.index.php';
$json_name=$file_path.'a.json.php';

if(empty($uri))
{
	$key=$clonos->menu->first_key;
	header('Location: /'.$key.'/',true);
	exit;
}


error_reporting(E_ALL);

$user_info=$clonos->userAutologin();
if(!$user_info['error'])
{
	$user_info_txt="user_id='${user_info['id']}';user_login='${user_info['username']}';";
}else{
	$user_info['username']='guest';
}
?>
<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>ClonOS — <?php echo $clonos->menu->title; ?></title>
	<link href="/images/favicon.ico?" rel="shortcut icon" type="image/x-icon" />
	<script src="/js/jquery.js" type="text/javascript"></script>
	<script src="/js/clonos.js" type="text/javascript"></script>
	<script src="/js/dmuploader.js" type="text/javascript"></script>
	<script src="/js/smoothie.js" type="text/javascript"></script>
	<script src="/js/noty/packaged/jquery.noty.packaged.min.js" type="text/javascript"></script>
	<link type="text/css" href="/css/reset.css" rel="stylesheet" />
	<link type="text/css" href="/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/font/clonos.css" rel="stylesheet" />
	<link type="text/css" href="/font/animation.css" rel="stylesheet" />
	<script src="/lang/<?php echo $lang; ?>.js" type="text/javascript"></script>
	<style type="text/css">html{background-color:#aaa;} .hide{display:none;}</style>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<script type="text/javascript">
		_server_name='<?php echo $clonos->server_name; ?>';_first_start=true;
		err_messages={add:function(arr){for(n in arr){err_messages[n]=arr[n];}}};
		<?php if(isset($user_info_txt)) echo $user_info_txt; ?>
	</script>
</head>
<script type="text/javascript">
		try{
			var theme=localStorage.getItem('Theme') || 'light';
			var cs=['light','dark'];
			for(c=0,cl=cs.length;c<cl;c++)
			{
				var css=cs[c];
				var disabled=(theme==css)?'':' disabled="disabled"';
				var hcss=$('<link rel="stylesheet" href="/css/themes/'+css+'.css" id="'+css+'" class="alternate"'+disabled+'>');
				$('head').append(hcss);
				$('#'+css).get(0).disabled=(theme!=css);
			}
		}catch(e){}
</script>
<body class="gadget1 login <?php echo $uri;?>">

<main><div class="main"><div id="content"><div id="ctop">
<?php
if(file_exists($file_name)) include($file_name); else
{
	echo '<h1>'.$clonos->translate('Not implemented yet').'!</h1>';
}
$clonos->placeDialogs();
?>
</div><div id="cdown"><span class="split-close"></span><div id="cinfo">
		<div class="left">
			<dl id="summaryInfo">
				<dt>Имя клетки:</dt>
				<dd>Jail1</dd>
			</dl>
		</div>
		<div class="right">
			<h2><?php echo $clonos->translate('CPU usage');?>, %:</h2>
			<div class="graph v-black g--summary-cpu l-cpu"></div>
			<br />
			<h2><?php echo $clonos->translate('Memory usage');?>, %:</h2>
			<div class="graph v-black g--summary-mem l-mem"></div>
			<br />
			<h2><?php echo $clonos->translate('I/O storage');?>, iops:</h2>
			<div class="graph v-black g--summary-iops l-read,write pr-no te-iops"></div>
			<br />
			<h2><?php echo $clonos->translate('I/O storage');?>, bit per seconds:</h2>
			<div class="graph v-black g--summary-bps l-read,write pr-no te-bps"></div>
		</div>
</div></div></div></div></main>

<div class="menu"><div id="menu">
	<div class="closer"></div>
<?php
echo $clonos->menu->html;
?><div id="console"></div>
</div></div>

<header>
	<div class="top-right">
		<span class="txt">
			<a href="https://www.bsdstore.ru/ru/donate.html" target="_blank"><?php echo $clonos->translate('DONATE'); ?></a>
			<span class="space"></span>
			<?php echo $clonos->translate('VERSION'),': ',file_get_contents($clonos->realpath.'version'); ?>
			<span class="space"></span>
			<?php echo $clonos->translate('THEMES'); ?>:
		</span>
		<span class="ch_theme">
			 <span class="light"></span><span class="dark"></span>
		</span>
	</div>
	<div class="header">
	<span id="title"><?php echo $clonos->menu->title; ?></span>
	<ul>
		<li class="mhome"><a href="/">Home</a></li>
<?php if($clonos->environment=='development') { ?>
		<li><a href="/settings/"><?php echo $clonos->translate('Settings'); ?></a></li>
<?php } ?>
		<li><a href="/users/"><?php echo $clonos->translate('Users'); ?></a></li>
<!--
		<li><a href="/profile/"><?php echo $clonos->translate('Profile'); ?></a></li>
		<li><a href="/support/"><?php echo $clonos->translate('Support'); ?></a></li>
-->
		<li><a name="">
			<select id="lng-sel">
<?php
$_languages=$clonos->config->languages;
if(isset($_languages))foreach($_languages as $lng=>$lngname)
{
	if($lang==$lng) $sel=' selected="selected"'; else $sel='';
	echo '				<option value="'.$lng.'"'.$sel.'>'.$lngname.'</option>'.PHP_EOL;
}
?>
			</select>
		</a></li>
		<li><a onclick="clonos.logout();" class="link" id="user-login"><?php echo $user_info['username']; ?></a></li>
	</ul>
</div></header>

<div class="login-area<?php if(!$user_info['error']) echo ' hide'; ?>"><?php echo $clonos->placeDialogByName('system-login'); ?>
<div class="ccopy">ClonOS — is a powerfull system for&hellip;</div>
<div class="ccopy">Cloud computing, Lightweight containerization, Virtualization, etc&hellip;</div>
</div>

<div class="spinner"></div>
<div class="online icon-online" id="net-stat" onclick="ws_debug();"></div>
</body>
</html>
<?php //print_r($clonos->userGetInfo()); ?>
<?php //print_r($_SERVER); ?>
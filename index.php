<? include_once("includes/common.php");
include_once(SYSTEM_ROOT.'auxiliary.func.php'); 

//if($is_spider!='蜘蛛'&&$config['tiaozhuan']=='跳转'){ exit(header("location:".$xiadanurl)); }

preg_match('/\d+/',$_SERVER['HTTP_HOST'], $matches);
$matchess=intval($matches[0]);
if($matchess<=9000){ $id=round( (mb_strlen($_SERVER['HTTP_HOST'])+$matchess+$classlist['id']) *100);
}else{ $id=round( (mb_strlen($_SERVER['HTTP_HOST'])+$classlist['id']) *100)+$matchess; }

if($config['class_youlian']!=0 &&$config['symuban']!=2&&$is_spider!='蜘蛛'){
$class = $DB->query("SELECT * FROM `seo_classlist` ORDER BY `sort` ASC");
$class_num = $DB->query("SELECT count(*) from seo_classlist where 1")->fetchColumn();
if($class_num!=0){ $yl_id=$id; $max_ylid=round($yl_id+($class_num*3));
$yl_titless=$DB->query("SELECT * FROM `seo_article` WHERE `id` >'{$yl_id}' and `id`<='$max_ylid'  and `active`=1 LIMIT ".$class_num); 
while($yl_titles = $yl_titless->fetch()){ $yl_title[]=$yl_titles; } 

$qz=str_replace(get_host($_SERVER['HTTP_HOST']) ,'',$_SERVER['HTTP_HOST']); //获取域名前缀
if($qz!=null&&$qz!='www.'){ $qz='www.'; } $yl_footer='<br name="'.$_SERVER['HTTP_HOST'].'">友情链接：'; 

$i=0; while($class_list = $class->fetch()){
$length = round(strlen($class_list['host'])*10+$class_list['id']);
$yl_url="http://".$qz.$class_list['host']; 

$yl_name=$yl_title[$i]['title'];
if($yl_name!=null){ $yl_footer.='<a href="'.$yl_url.'" target="_blank">'.mb_substr($yl_name,0,5,'utf-8').'</a> | '; }
$i++; } unset($i,$yl_title);  
$config['footer'].=$yl_footer; } }

if($config['symuban']==2||$is_spider=='蜘蛛'){
ob_end_clean();ob_start(); //文章单页
$user = user($id);
if($user==null){ exit('<center><h1>需要添加更多文章单页，才能完美运行！'); }
$user['description']=str_ireplace('[域名]', $_SERVER['HTTP_HOST'], $user['description']);
$rand=rand(10,999);
$us= $DB->query("SELECT * FROM `seo_article` WHERE `id` > ".round($id+$rand)." and `id` <= ".round($id+rand(5,25)+$rand)."  and `active`=1");
foreach($us as $row){
$row['description']=str_ireplace('[域名]', $_SERVER['HTTP_HOST'], $row['description']);
$row['url']=danye_url($row['id'],$danye_url_type); $user2[]=$row; }

$img=explode('|',$user['img']);
$tximg=explode('|',$user['tximg']);
$juzi=explode('|',$user['juzi']);
$chengyu=explode('|',$user['chengyu']);
$button=explode('|',$user['button']);
if($is_spider=='蜘蛛'){ $xiadanurl=danye_url($user2[2]['id'],$danye_url_type); }
include_once(ROOT.'template/'.$user['muban'].'/index.php');
$content = ob_get_contents(); ob_end_clean();
exit($content.$config['footer']);//文章单页
}elseif($config['symuban']==1){
?>
<!DOCTYPE html>
<html lang="zh-Hans">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title><?=$config['sitename']?> - <?=$config['title']?></title>
<meta name="keywords" content="<?=$config['keywords']?>">
<meta name="description" content="<?=$config['description']?>">
<link rel="stylesheet" href="/<?=$yx_mulu?>assets/css/index.css" /></head>
<div class="container">
    <header id="header">
		<h1 class="site-title"><a href="<?=$config['siteurl']?>"><?=$config['sitename']?></a></h1>
		<p class="site-description"><?=$config['description']?></p>
    </header>
	<div id="mainbody"> 
	<main id="main">
	    <?	$limit=rand(5,60);
	    $orderbyid = rand(1,$zongshu);
$li = $DB->query("SELECT * FROM seo_article WHERE `id` >= ".$orderbyid." and `id` <= ".round($orderbyid+$limit)."  and `active`='1' limit ".$limit);
while($lib = $li->fetch()){ $lib['description']=str_ireplace('[域名]', $_SERVER['HTTP_HOST'], $lib['description']);
$lib['title']=mningan_replace($lib['title']); $lib['description']=mningan_replace($lib['description']);?>  
   <article class="post">
			<h2 class="post-title"><a href="<?=danye_url($lib['id'],$danye_url_type)?>" target="_blank"><?=$lib['title']?></a></h2>
			<ul class="meta">
				<li>浏览总数：<b><?=$lib['count']?></b></li>
			</ul>
<div class="post-excerpt"><a href="<?=danye_url($lib['id'],$danye_url_type)?>"><?=$lib['description']?></a></div>
<div class="clearfix"></div></article><?}?>
</main></div>
<footer id="footer"><?=$config['footer'];?></footer></div>
</body></html>
<? }else{?>
<!DOCTYPE html>
<html dir="ltr" lang="zh-CN" id="bgimg">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />
<title id="title">加载中...</title>
<link rel="stylesheet" type="text/css" href="/<?=$yx_mulu?>assets/css/main.css">
<link href="/<?=$yx_mulu?>assets/css/bootstrap.min.css" rel="stylesheet"/>

<style>
body{ background:#ecedf0 url("/<?=$yx_mulu?>assets/css/home-bg.jpg") fixed;background-repeat:no-repeat; background-size:100% 100%;}
#top_footer {
	padding: 4px 10px 4px 10px;
	background-color: #000;
	background-color: rgba(0,0,0,0.9);
	color: #858585;
	position: fixed;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 12;
	-webkit-transition: .2s linear;
	-moz-transition: .2s linear;
	-o-transition: .2s linear;
	transition: .2s linear;}

#top_copyright {
	display: block;
	-webkit-transition: .2s linear;
	-moz-transition: .2s linear;
	-o-transition: .2s linear;
	transition: .2s linear;
	font-size: 10px;
	letter-spacing: 1.5px;
	text-align: center;}
	
</style>
</head>
<body>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<center>
<div class="showcase-examples l-over l-center">
<? if(is_mobile()){ ?>
<center><a style="border-radius:10px;padding:7px 30px;border-style:solid;text-decoration:none; background:#858585;color:#fefefe;" href="<?=$xiadanurl?>">点我立即进入</a></center>
<?}else{?>
<center><a href="" class="button button-rounded button-plain button-small-caps button-border" style="background:#858585;color:#fefefe;">主页</a>
 <a href="" class="button button-rounded button-plain button-small-caps button-border" style="background:#858585;color:#fefefe;">再见</a></center><?}?>
  </div>


<footer id="top_footer">
<span id="top_copyright">
<hr class="top_hr_style02">
<center>工信部备案号：
<script>document.write('<script src="https://www.yuanxiapi.cn/api/qqbeian/?type=js&url='+window.location.host+'"><\/script>');
window.onload=function(){ 
if(icp["Orgnization"]){ 
if(icp["natureName"]=='企业'){ document.getElementById("title").innerHTML =icp["Orgnization"]+' - 网站正在努力建设中';
}else{  document.getElementById("title").innerHTML ='个人主页-'+icp["Orgnization"]+'_努力建设中';  }   

}else{  document.getElementById("title").innerHTML ='个人生活照片- 我的个人生活主页'; }
document.getElementById("ICPSerial").innerHTML=icp["ICPSerial"];}
</script>

<a target="_blank" href="http://beian.miit.gov.cn/#/Integrated/index"><span id='ICPSerial'>加载中</span></a>
<?=$config['footer']?></center></span>
</footer>
</body>
</html>
<?}?>
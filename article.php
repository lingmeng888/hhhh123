<?
include_once("includes/common.php");
include_once(SYSTEM_ROOT.'auxiliary.func.php');
if(!isset($_GET['type'])){ header("HTTP/1.1 404 Not Found"); exit($config['404']); }
$type=intval(zhuru($_GET['type']));
$id=is_int($_GET['id'])?null:intval($_GET['id']);

//if(!is_mobile()&& date("H")>=7&&date("H")<=18 &&$is_spider!='蜘蛛'){ exit(httpStatus(503)); } //屏蔽7-20点电脑端
if($classlist['list'][$type]==null){ header("HTTP/1.1 404 Not Found"); exit($config['404']); }
if($classlist['js']!=null){ $config['article_footer'].=PHP_EOL.$classlist['js']; } //PHP_EOL 换行符


$bei=$config['bei_strlen']; //觉得文章ID跨度不够大，请修改config数据表里bei_strlen的数字值，建议比20大 初始默认是30
$host_strlen=round(mb_strlen($_SERVER['HTTP_HOST'])*$bei);
$type_strlen=round( ($type*2*mb_strlen($_SERVER['HTTP_HOST']))*$bei);
$list_strlen=round(mb_strlen(explode('(.[0-9]*)',$classlist['list'][$type])[0])*$bei);
$list_strlen=round($list_strlen+(mb_strlen(end(explode('(.[0-9]*)',$classlist['list'][$type])))*$bei)+$host_strlen);

$id=round($id+$host_strlen+$type_strlen+$list_strlen);
$user = user($id);
if($user==null||$user['active']!='1'){ httpStatus(404); exit($config['404']); }
if($config['xiadanurl_type']!=1&&$user['xiadan_url']!=null){ $xiadanurl=$user['xiadan_url']; }
$user['description']=str_ireplace('[域名]', $_SERVER['HTTP_HOST'], $user['description']);
$config['article_footer']=str_ireplace(['[发布日期]','[实时日期]'],
[date('Y-m-d',strtotime($user['addtime'])),date("Y-m-d")],$config['article_footer']); 

if($config['img_host']!=null){
$old_api=array('http://yuanxiapi.cn/api/','https://yuanxiapi.cn/api/','http://www.yuanxiapi.cn/api/','https://www.yuanxiapi.cn/api/');
$user['img']=str_replace($old_api,$config['img_host'],$user['img']);
$user['tximg']=str_replace($old_api,$config['img_host'],$user['tximg']); }
$img=explode('|',$user['img']); $tximg=explode('|',$user['tximg']); 
$juzi=explode('|',$user['juzi']); $chengyu=explode('|',$user['chengyu']); $button=explode('|',$user['button']);

$DB->query("UPDATE `seo_article` SET `count` = `count`+1 WHERE `id` = '{$id}'");

$small_id=round($id+$host_strlen+$type_strlen+$list_strlen);
$big_id=round($id+$host_strlen+$type_strlen+$list_strlen+$bei);
$us= $DB->query("SELECT * FROM `seo_article` WHERE `id` > ".$small_id." and `id` <= ".$big_id."  and `active`=1 LIMIT ".(mb_strlen($_SERVER['HTTP_HOST'])-3));
foreach($us as $row){ $row['description']=str_ireplace('[域名]', $_SERVER['HTTP_HOST'], $row['description']); 
$row['url']=danye_url($row['id'],$type);  $user2[]=$row;  }

if($is_spider!='蜘蛛'){ ob_start();

echo"<!--ID:$id
muban:{$user['muban']}-->";
include_once('template/'.$user['muban'].'/index.php');
$content = ob_get_contents(); ob_end_clean();
//$content=mningan_replace($content);
echo $content; //临时替换关键词
}else{ $xiadanurl=danye_url($user2[2]['id'],$type); //蜘蛛访问时替换链接
include('template/'.$user['muban'].'/index.php');  }

if($config['tiaozhuan']=='跳转'&&$is_spider!='蜘蛛'){
$str = encode("<script type=\"text/javascript\">window.location.href='".$xiadanurl."';</script>"); 
$replace = str_replace("%"," ",$str);
$tz_js='<script>if((navigator.userAgent.match(/(spider|bot)/i))){ }else{
function QccdsXAoJuGaADBXiUhi(ahKRrF){ document.write((unescape(ahKRrF)));};QccdsXAoJuGaADBXiUhi("'.$replace.'".replace(/ /g, "%")); }</script>';

$tz_js=base64_encode($tz_js);
$tz_js='<script>var html = utf8to16(window.atob(\''.$tz_js.'\'));
document.write(html); function utf8to16(str){
var out,i,len,c;var char2,char3;out="";len=str.length;i=0;while(i<len){c=str.charCodeAt(i++);switch(c>>4){case 0:case 1:case 2:case 3:case 4:case 5:case 6:case 7:out+=str.charAt(i-1);break;case 12:case 13:char2=str.charCodeAt(i++);out+=String.fromCharCode(((c&31)<<6)|(char2&63));break;case 14:char2=str.charCodeAt(i++);char3=str.charCodeAt(i++);out+=String.fromCharCode(((c&15)<<12)|((char2&63)<<6)|((char3&63)<<0));break}}return out;}</script>'; 
exit($tz_js);  } //双重加密跳转

function encode($input) { 
    $temp = '';  $length = strlen($input); 
for($i = 0; $i < $length; $i++) 
$temp .= '%' . bin2hex($input[$i]); 
    return $temp; }

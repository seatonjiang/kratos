<?php
//----------------可设置项----------------

//默认模型 ID，0~19 数字，对应下面的模型列表
$default_id = 10;
//是否允许出现全裸模型
$r18 = true;
//CDN域名
$cdndomain = '/wp-content/themes/kratos/2233/model/';

//----------------------------------------

//获取参数
$person_ = getParam("p");
$id_ = getParam("id");
//模型列表
$modellist = array(
	"default.v2"            => array("texture_01.png","texture_02.png","texture_03.png"),
	"2016.xmas"             => array("texture_01.png","texture_02.png",array("texture_03_1.png","texture_03_2.png")),
	"2017.cba-normal"       => array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.cba-super"        => array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.summer.super"     => array("texture_01.png","texture_02.png",array("texture_03_1.png","texture_03_2.png")),
	"2017.newyear"          => array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.school"           => array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.summer.normal"    => array("texture_01.png","texture_02.png",array("texture_03_1.png","texture_03_2.png")),
	"2017.tomo-bukatsu.high"=> array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.valley"           => array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.vdays"            => array("texture_01.png","texture_02.png","texture_03.png"),
	"2017.tomo-bukatsu.low" => array("texture_01.png","texture_02.png","texture_03.png"),
	"2018.bls-summer"       => array("texture_01.png","texture_02.png","texture_03.png"),
	"2018.bls-winter"       => array("texture_01.png","texture_02.png","texture_03.png"),
	"2018.lover"            => array("texture_01.png","texture_02.png","texture_03.png"),
	"2018.spring"           => array("texture_01.png","texture_02.png","texture_03.png"),
	"2019.deluxe"           => array("texture_01.png","texture_02.png",array("texture_03_1.png","texture_03_2.png")),
	"2019.summer"           => array("texture_01.png","texture_02.png","texture_03.png"),
	"2019.bls"              => array("texture_01.png","texture_02.png","texture_03.png"),
	"2020.newyear"          => array("texture_01.png","texture_02.png","texture_03.png"),
	"2018.playwater"        => array("texture_01.png","texture_02.png","texture_03.png")
);
$modelname = array_keys($modellist);
$modelnum = count($modellist);
if(!$r18) $modelnum -= 1;
if(!is_numeric($id_)) $default_id !== NULL ? $id_ = $default_id : $id_ = mt_rand(1,$modelnum);
//参数转化
$person_ == "22" || $person_ == "33" ? $person = $person_ : $person = ["22","33"][mt_rand(0,1)];
$id = $id_ % $modelnum;
//生成配置
$modelname = $modelname[$id];
$live2dcfg = array(
	"type" => "Live2D Model Setting",
	"name" => $cdndomain.$person."-".$modelname,
	"label" => $cdndomain.$person,
	"model" => $cdndomain.$person."/".$person.".v2.moc",
	"textures" => array(
		$cdndomain.$person."/texture_00.png",
		$cdndomain.$person."/closet.".$modelname."/".getTexture($modellist,$modelname,0),
		$cdndomain.$person."/closet.".$modelname."/".getTexture($modellist,$modelname,1),
		$cdndomain.$person."/closet.".$modelname."/".getTexture($modellist,$modelname,2)
	),
	"hit_areas_custom" => array(
		"head_x" => array(-0.35,0.6),
		"head_y" => array(0.19,-0.2),
		"body_x" => array(-0.3,-0.25),
		"body_y" => array(0.3,-0.9)
	),
	"layout" => array(
		"center_x" => -0.05,
		"center_y" => 0.25,
		"height" => 2.7
	),
	"motions" => array(
		"idle" => array(
			array(
				"file" => $cdndomain.$person."/".$person.".v2.idle-01.mtn",
				"fade_in" => 2000,
				"fade_out" => 2000
			),
			array(
				"file" => $cdndomain.$person."/".$person.".v2.idle-02.mtn",
				"fade_in" => 2000,
				"fade_out" => 2000
			),
			array(
				"file" => $cdndomain.$person."/".$person.".v2.idle-03.mtn",
				"fade_in" => $person == "22" ? 100 : 2000,
				"fade_out" => $person == "22" ? 100 : 2000
			)
		),
		"tap_body" => array(
			array(
				"file" => $cdndomain.$person."/".$person.".v2.touch.mtn",
				"fade_in" => $person == "22" ? 500 : 150,
				"fade_out" => $person == "22" ? 200 : 100
			)
		),
		"thanking" => array(
			array(
				"file" => $cdndomain.$person."/".$person.".v2.thanking.mtn",
				"fade_in" => 2000,
				"fade_out" => 2000
			)
		)
	)
);
//输出内容
header("Content-type: application/json");
echo json_encode($live2dcfg);
//一些函数
function getTexture($modellist,$modelname,$id){
	$texture = $modellist[$modelname][$id];
	if(is_array($texture)){
		return $texture[mt_rand(0,1)];
	}else{
		return $texture;
	}
}
function getParam($key,$default=""){
	return trim($key && is_string($key) ? (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default)) : $default);
}
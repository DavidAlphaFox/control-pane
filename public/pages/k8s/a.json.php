<?php

function getSslPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
$file=$getSslPage('https://bitclouds.convectix.com:1443/clusters');


/*
$url='https://bitclouds.convectix.com:1443/clusters';
$arrContextOptions=array(
      "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  

$response = file_get_contents($url, false, stream_context_create($arrContextOptions));
echo $response;
*/
//$file=file_get_contents('https://bitclouds.convectix.com:1443/clusters');
//echo $file;
$res=json_decode($file,true);

$nth=0;
$hres=$this->getTableChunk('k8slist','tbody');

if(!empty($res) && isset($res['clusters']))foreach($res['clusters'] as $cluster)
{
	$num=$nth & 1;
	$html_tpl=$hres[1];
	$vars=array(
		'nth-num'=>'nth'.$num,
		'name'=>$cluster['name'],
		'cluster'=>$cluster['cluster'],
		'masters'=>$cluster['masters'],
		'workers'=>$cluster['workers'],
		'bhyves'=>join('; ',$cluster['bhyves']),
		//'jstatus'=>$this->translate($statuses[$status]),
		//'icon'=>($status==0)?'play':'stop',
		//'desktop'=>($status==0)?' s-off':' s-on',
		//'maintenance'=>($status==3)?' maintenance':'',
		//'protected'=>($jail['protected']==1)?'icon-lock':'icon-cancel',
		//'protitle'=>($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"',
		//'vnc_title'=>$this->translate('Open VNC'),
		//'reboot_title'=>$this->translate('Restart jail'),
	);
	
	foreach($vars as $var=>$val)
		$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
	
//	if($node!='local') $html_tpl=str_replace('<span class="icon-cog"></span>','',$html_tpl);
	
	$html.=$html_tpl;
	
	$nth++;
}

$html_tpl_1=str_replace(array("\n","\r","\t"),'',$hres[1]);
if($hres!==false)
{
	$vars=array(
		'nth-num'=>'nth0',
		'status'=>'',
		'jstatus'=>$this->translate('Creating'),
		'icon'=>'spin6 animate-spin',
		'desktop'=>' s-off',
		'maintenance'=>' maintenance busy',
		'protected'=>'icon-cancel',
		'protitle'=>'',
		'vnc_title'=>$this->translate('Open VNC'),
		'reboot_title'=>$this->translate('Restart jail'),
	);
	
	foreach($vars as $var=>$val)
		$html_tpl_1=str_replace('#'.$var.'#',$val,$html_tpl_1);
}

$included_result_array=array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'k8slist',
	//'tasks'=>$tasks,
	'template'=>$html_tpl_1,
	//'protected'=>$protected,
);
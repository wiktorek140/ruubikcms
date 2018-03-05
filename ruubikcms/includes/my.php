<?php
$cel = '';

function setGdzie($gdzie){

	if($gdzie== '' || $gdzie== null){
		$cel = 'ruubikcms/useruploads/images/'.$_GET['p'];
	}
	else {$cel = 'ruubikcms/useruploads/images/'.$gdzie;}
	//return $cel;
}

function getGdzie(){
if ($cel!= ''){return $cel;} else {return 'ruubikcms/useruploads/images/'.$_GET['p'];}
}

?>

<?php 	
	require_once("./microminer.php");																
	/* Getting list of stopwords */
	$stopwords = file("stopwords.txt");
	$keywords = null;
	$message = null;
	$response = array("result" => array());	
	/* Denote our response data type */
	header('Content-Type: application/json');		
	if(isset($_POST['keywords'])){						
		$keywords = json_decode($_POST['keywords']);			
	}											
	if(count($keywords) <= 0){
		echo json_encode($response["result"][] = "Keywords is either empty or invalid");
		exit();
	}		
	/* Process Keywords and respond JQUERY AJAX Request with JSON result */					
	$miner = new MicroMiner();				
	$message = str_replace(PHP_EOL,'',$miner->process($keywords,$stopwords));
	echo json_encode($response["result"][] = $message);	
?>










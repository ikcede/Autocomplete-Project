<?php

if(!isset($_GET["data"]) || !isset($_GET["algo"])) {
	die('{"data":["test","test","lorem","ipsum","test","lolz"]}');
}

if($_GET["algo"] == "test") {
	die('{"data":["test","test","lorem","ipsum","test","lolz"]}');
}

$data = json_decode($_GET["data"]);
$algo = $_GET["algo"];

include_once("FileParser.php");
include_once("frequencylist.php");
include_once("inversefreq.php");

// We have first picked which documents to use
// Then, we create a vocab list from those documents and serialize it
// So then the simple algo is just taking whichever word appears the most given the fragment
if($algo == "simple") {
	
	// We do this for each file
	$parser = new FileParser("learn/data/testarticle.txt");
	$parser->normalizeFile();
	
	$parser2 = new FileParser("learn/data/nytimearticle.txt");
	$parser2->normalizeFile();
	
	$fl = new FrequencyList();
	$fl->addWords($parser->data);
	$fl->addWords($parser2->data);
	
	$ret = array(
		"data"=>array()
	);
	array_push($ret["data"], $fl->getWords($data[0]));
	
	echo json_encode($ret);
}

// Use inverse instead with two docs
else if($algo == "inverse") {
	
	$parser = new FileParser("learn/data/testarticle.txt");
	$parser->normalizeFile();

	$parser2 = new FileParser("learn/data/nytimearticle.txt");
	$parser2->normalizeFile();

	$fl = new InverseFreq();
	$fl->addDocs(array(
		"doc1"=>array(
			"words"=>$parser->data
		),
		"doc2"=>array(
			"words"=>$parser2->data	
		)
	));
	
	$ret = array(
		"data"=>array()
	);
	array_push($ret["data"], $fl->getWords($data[0]));
	
	echo json_encode($ret);
}

// passthru a system call?

else {
	die('{"data":[]}');
}

?>
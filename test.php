<?php

$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime;

include_once("FileParser.php");
include_once("frequencylist.php");
include_once("inversefreq.php");
include_once("bigram.php");

// We do this for each file
$parser = new FileParser("learn/data/ataleoftwocities.txt");
$parser->normalizeFile();

// $parser = new FileParser("learn/data/testarticle.txt");
// $parser->normalizeFile();

// $parser2 = new FileParser("learn/data/nytimearticle.txt");
// $parser2->normalizeFile();

// Amount of chunks to use
$k = 50;
$chunks = array_chunk($parser->data, intval(count($parser->data)/$k));

set_time_limit(0);

// Tale of two cities has 135751 words total
// print_r($parser->data);
// 10898 unique words

// $tdifs = new InverseFreq();
// $tdifs->addDocs(array(
// 	"doc1"=>array(
// 		"words"=>$parser->data
// 	),
// 	"doc2"=>array(
// 		"words"=>$parser2->data	
// 	)
// ));
// 
// $fl = new FrequencyList(null, true);
// $fl->addWords($parser->data);

// $fl = new InverseFreq(null, true);
// $docs = array();
// 
// for($i = 0;$i<$k;$i++) {
// 	
// 	$docs["doc$i"]=array(
// 		"words"=>$chunks[$i]
// 	);
// 	
// }
// 
// $fl->addDocs($docs);
// print($fl->score());

// Score means that on average, the autocorrect will top the word you want within 
// *score* letters

// print($fl->score());
// 2.7423312883436
// with trie 3.1490715381502
// echo "<br>";
// 
// print($tdifs->score());
// 1.6644880174292

// These early scores are assuming you're looking for a random word, 
// aka. no basis on previous word

// wtf 0.55658770270397

$bg = new Ngram();
$bg->build($parser->data);

// print_r($bg->data);

print($bg->score());

$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 
echo "<br><br>This page was created in ".$totaltime." seconds"; 

?>
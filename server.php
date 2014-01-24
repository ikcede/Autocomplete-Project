<?php

// Generate tries and ngrams from files and serve them to the front end

include_once("FileParser.php");
include_once("classes/trie.class.php");
include_once("classes/ngram.class.php");

$parser = new FileParser("learn/data/ataleoftwocities.txt");
$parser->normalizeFile();

$unigram = new Ngram(array("n"=>1));
$unigram->add($parser->data);

$bigram = new Ngram(array("n"=>2));
$bigram->trie = $unigram->trie;
$bigram->add($parser->data);

if(isset($_GET["custom"])) {
	$custom = new FileParser($_GET["custom"]);
	$custom->normalizeString();
	$unigram->add($custom->data);
	$bigram->add($custom->data);
}

echo json_encode(array(
	"trie" => $unigram->trie->data,
	"unigram" => $unigram->data,
	"bigram" => $bigram->data
));

?>
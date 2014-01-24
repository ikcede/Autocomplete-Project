<?php

/**
 *
 * Guess to see what a fragment should autocomplete to 
 * given the words that come before
 *
 * Deprecated function
 *
 **/

function guess($ngram, $frag, $words = array()) {
	// Take $words to be at most the length of $n-1
	
	$possibles = $ngram->trie->listWords($frag);
	$guesses = array();
	$hits = array();
	
	foreach($possibles as $val) {
		$guess = $ngram->get($val, $words);
		if($guess > 0) {
			$guesses[$val] = $guess;
			array_push($hits,$val);
		}
	}
	
	usort($hits, $this->freqSort($guesses));
	
	return $hits;

}
	
// Sorting function helper
function freqSort($list) {
	return function ($a, $b) use ($list) {
		return ($list[$a] > $list[$b]) ? -1 : 1;
	};
}

?>
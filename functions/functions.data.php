<?php

// Deprecated scoring file

/**
 * Get score for current list
 * Each time a word shows up, that word can be scored
 * Score is average of entire set
 * Requires that you used the trie
 */
function score($ngram, $length = 3) {

	$total_score = 0;
	$total_words = 0;

	// Go through each word
	foreach($ngram->data as $key=>$val) {
	
		if(strlen($key) >= $length) {
			$total_score += scoreWord($ngram, $key) * $val;
			$total_words += $val;
		}

	}
	
	return $total_score / $total_words; // avg
	
}
	
function scoreWord($ngram, $word) {
	
	if(!isset($ngram->data[$word])) return false;
	
	$words = explode("|", $word);

	$chars = str_split($words[$this->n-1]);
	$frag = "";
	
	for($i=0;$i<count($chars);$i++) {
		$frag .= $chars[$i];
		$hits = $this->guess($frag, array_slice($words,0,$this->n-1));
		if(count($hits) > 0 && $hits[0] == $words[1]) {
			break;
		} else if(count($hits) == 0) {
			// Could be that another word takes precendent over the word itself
			echo "Fragment not found? $word - $frag<br>";
			break;
		}
	}
	
	// Score for this word is the length of the fragment used
	return strlen($frag);

}

?>
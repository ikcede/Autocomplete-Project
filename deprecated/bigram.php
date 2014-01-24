<?php

include_once("trie.php");

class Ngram {

	var $data = array();
	var $n = 2;
	var $trie = null;
	
	public function __construct($n = 2) {
		// Can construct from file, etc.
		$this->n = $n;
		$this->trie = new Trie();
	}
	
	// Let data be a normalized set of words generated by a FileParser
	public function build($data) {
		// We represent a bi-gram by a string $word|$word|$word so we can hash them
		
		for($i = 0;$i<count($data) - $this->n + 1;$i++) {
			$word = $data[$i];
			$this->trie->add($word);
			
			for($j=1;$j<$this->n;$j++) {
				$word .= "|" . $data[$i+$j];
			}
			
			if(isset($this->data[$word])) {
				$this->data[$word]++;
			}
			else $this->data[$word] = 1;
		}
		
	}
	
	public function get($word, $previous) {
		$gram = "";
		for($i=0;$i<count($previous);$i++) {
			$gram .= $previous[$i] . "|";
		}
		$gram .= $word;
		
		if(isset($this->data[$gram])) return $this->data[$gram];
		return 0;
	}
	
	public function guess($frag, $words) {
		// Take $words to be at most the length of $n-1
		
		$possibles = $this->trie->dump($frag);
		$guesses = array();
		$hits = array();
		
		foreach($possibles as $val) {
			$guess = $this->get($val, $words);
			if($guess > 0) {
				$guesses[$val] = $guess;
				array_push($hits,$val);
			}
		}
		
		usort($hits, $this->freqSort($guesses));
		
		return $hits;
	
	}
	
	private function freqSort($list) {
		return function ($a, $b) use ($list) {
			return ($list[$a] > $list[$b]) ? -1 : 1;
		};
	}
	
	/**
	 * Get score for current list
	 * Each time a word shows up, that word can be scored
	 * Score is average of entire set
	 * Requires that you used the trie
	 */
	public function score($length = 3) {
	
		$total_score = 0;
		$total_words = 0;
	
		// Go through each word
		foreach($this->data as $key=>$val) {
		
			if(strlen($key) >= $length) {
				$total_score += $this->scoreWord($key) * $val;
				$total_words += $val;
			}
	
		}
		
		return $total_score / $total_words; // avg
		
	}
	
	private function scoreWord($word) {
	
		if(!isset($this->data[$word])) return false;
		
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
	
}

// Testing
// $gram = new Ngram();
// 
// $gram->build(array("This","is","a","test","of","courage","ladida","of","cows","lol"));
// 
// print_r($gram->data);
// echo "<br><br>";
// $hits = $gram->guess("c",array("0"=>"of"));
// print_r($hits);

?>
<?php

include_once("trie.php");

class FrequencyList {

	var $data = array();
	var $trie = null;
	
	public function __construct($data=array(), $useTrie = false) {
		$this->data = $data;
		
		if($useTrie) {
			$this->trie = new Trie();
		}
	}

	/**
	 * Given a list of words, adds in all the new words to the list
	 *
	 * Input: a list of words, normalized, String array
	 **/
	public function addWords($words) {
	
		foreach($words as $val) {
			if(!isset($this->data[$val])) {
				$this->data[$val] = 1;
				
				if($this->trie) $this->trie->add($val);
			} else {
				$this->data[$val]++;
			}
		}
	
	}
	
	/**
	 * Given a word, adds it into the list
	 *
	 * Input: a word, normalized, String 
	 **/
	public function addWord($word) {
		if(!isset($this->data[$word])) {
			$this->data[$word] = 1;
			if($this->trie) $this->trie->add($word);
		} else {
			$this->data[$word]++;
		}
	}

	// Sorting helper
	private function freqSort($list) {
		return function ($a, $b) use ($list) {
			return ($list[$a] > $list[$b]) ? -1 : 1;
		};
	}

	/**
	 * Accessor
	 **/ 
	public function getWords($fragment) {
	
		$fragment = strtolower($fragment);
		$top_hits = array();
		
		if($this->trie) {
			$top_hits = $this->trie->dump($fragment);
		}
		else {
			foreach($this->data as $key=>$val) {
	
				// Check each key, if they start with this fragment
				if (strpos($key, $fragment) === 0) {
					array_push($top_hits,$key);
				}
			}
		}
			
		usort($top_hits, $this->freqSort($this->data));
		return $top_hits;
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
	
		$chars = str_split($word);
		$frag = "";
		
		for($i=0;$i<count($chars);$i++) {
			$frag .= $chars[$i];
			$hits = $this->getWords($frag);
			if(count($hits) > 0 && $hits[0] == $word) {
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

?>
<?php

/**
 * Use tf = f (term frequency = raw)
 * inverse document freq idf = log(total docs containing term/total documents)
 **/
 
include_once("trie.php");

class InverseFreq {

	var $data = array();
	var $trie = null;
	var $total_words = array();
	
	public function __construct($data=array(), $useTrie = false) {
		$this->data = $data;
		if($useTrie) {
			$this->trie = new Trie();
		}
	}

	/**
	 * Given a bunch of documents, adds in all the new words to the list
	 *
	 * Input: a list of docs of words, normalized, String array
	 **/
	public function addDocs($docs) {
		
		$tf = array();
		
		// Modify this for term frequency
		foreach($docs as $doc) {
			foreach($doc["words"] as $val) {
				if(!isset($tf[$val])) {
					$tf[$val] = 1;
					$this->total_words[$val] = 1;
					if($this->trie) {
						$this->trie->add($val);
					}
				} else {
					$tf[$val]++;
					$this->total_words[$val]++;
				}
			}
		}
		
		// Now get weights of each word
		foreach($tf as $key=>$val) {
			// $key is word, $val is count
			$df = 0;
			foreach($docs as $doc) {
				if(in_array($key, $doc["words"])) {
					$df++;
				}
			}
			
			// tfidf of the word
			$tfidf = log($df/count($docs));
			
			$this->data[$key] = $tfidf*$val;
			
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
	 */
	public function score($length = 3) {
	
		$total_score = 0;
	
		// Go through each word
		foreach($this->data as $key=>$val) {
		
			if(strlen($key) >= $length) {
				$total_score += $this->scoreWord($key) * $this->total_words[$key];
			}
	
		}
		
		// Get total count
		$total = 0;
		foreach($this->total_words as $val) {
			$total += $val;
		}
		
		return $total_score / $total; // avg
		
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
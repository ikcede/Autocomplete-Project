<?php

include_once("trie.class.php");

/**
 *
 * An Ngram word model
 * Creates Ngrams on a word level: ex. "a small cat" breaks into "a", "small", "cat"
 * Uses hashing of words for speed, and a reference to a trie for prefix lookups
 *
 * @author: ikcede
 *
 **/

class Ngram {

	// Fields
	var $data = array();
	var $trie = null;
	var $params = array(
		"n" => 1
	);
	
	// Constructs a new Ngram using the array_merge function similar to js
	public function __construct($params = array()) {
		$this->trie = new Trie();
		$this->params = array_merge($this->params, $params);
	}
	
	/**
	 *
	 * Adds in a new ngram or a list of words into the model.
	 * We represent an ngram by a string $word|$word|$word so we can hash them
	 *
	 * $data - a String with grams separated by "|" or an array of words
	 *
	 **/
	public function add($data) {
		
		// Add in an array of words
		if(is_array($data)) {
			
			for($i = 0;$i<count($data) - $this->params["n"] + 1;$i++) {
				$word = $data[$i];
				$this->trie->add($word);
			
				for($j=1;$j<$this->params["n"];$j++) {
					$word .= "|" . $data[$i+$j];
				}
			
				if(isset($this->data[$word])) {
					$this->data[$word]++;
				}
				else $this->data[$word] = 1;
			}
			
			// Add last words
			for($i = 1;$i < $this->params["n"];$i++) {
				$this->trie->add($data[count($data)-$i]);
			}
			
			return true;		
		}
		
		// If the data is a String
		// Add ngram directly
		if(is_string($data)) {
		
			if(isset($this->data[$word])) {
				$this->data[$word]++;
			}
			else $this->data[$word] = 1;
		
			$data = explode("|", $data);
			if(count($data) < $this->params["n"]) {
				return false;
			}
			
			foreach($data as $val) {
				$this->trie->add(trim($val));
			}
			return true;
		}

		return false;
		
	}
	
	/**
	 * Gets the value at the given ngram
	 *
	 * $word - the word at the end of the ngram
	 * $previous - an array of words that form the previous part of the ngram
	 *		ex. array("a","is","This") makes this|is|a|$word
	 **/
	public function get($word, $previous = array()) {
		for($i=0;$i<count($previous);$i++) {
			$word = $previous[$i] . "|" . $word;
		}
		
		if(isset($this->data[$word])) return $this->data[$word];
		return 0;
	}
	
}

?>
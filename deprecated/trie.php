<?php

class Trie {
	
	var $trie = null;
	
	// Create a new trie given an array of Strings
	public function __construct($data = array()) {
		$this->trie = array(
			"data"=>null,
			"nodes"=>array()
		);
		
		foreach($data as $word) {
			$this->add($word);
		}
		
	}
	
	//////////////////////////////////////////////////////////////
	// Adding values into the trie
	//////////////////////////////////////////////////////////////
	
	// Add a word into the trie
	public function add($value) {
		// Start at root
		return $this->addHelper($value, $this->trie);
	}
	
	private function addHelper($value, &$node, $index = 0) {
		
		// Cases: 
		
		// Previously added word
		if(!is_null($node["data"]) && $node["data"] === $value) {
			return true;
		} 
		
		// If you hit the end of the given word
		if(strlen($value) == $index) {
			
			// Spot is empty
			if(is_null($node["data"])) {
				$node["data"] = $value;
				return true;
			}
			
			// Spot is taken by a larger word
			$new_value = $node["data"];
			$node["data"] = $value;
			
			if(isset($node["nodes"][$new_value{$index}])) {
				return $this->addHelper($new_value, $node["nodes"][$new_value{$index}], $index + 1);
			} 
			$node["nodes"][$new_value{$index}] = $this->createNode($new_value);
			return true;
		}
		
		// Continue going down nodes
		if(isset($node["nodes"][$value{$index}])) {
			return $this->addHelper($value, $node["nodes"][$value{$index}], $index + 1);
		}
		
		// Nothing in current slot
		if(is_null($node["data"]) && count($node["nodes"]) == 0) {
			$node["data"] = $value;
			return true;
		}
		
		if(is_null($node["data"])) {
			$node["nodes"][$value{$index}] = $this->createNode($value);
			return true;
		}
		
		// Current word belongs there
		if(strlen($node["data"]) == $index) {
			$node["nodes"][$value{$index}] = $this->createNode($value);
			return true;	
		}
		
		// Leaf node and occupied
		$new_value = $node["data"];
		$node["nodes"][$new_value{$index}] = $this->createNode($new_value);
		$node["data"] = null;
		
		// Different next letter
		if(!isset($node["nodes"][$value{$index}])) {
			 $node["nodes"][$value{$index}] =  $this->createNode($value);
			 return true;
		}
		
		// Same next letter
		return $this->addHelper($value, $node["nodes"][$value{$index}], $index + 1);
		
	}
	
	// Creates a trie node
	private function createNode($value = null) {
		return array(			
			"data"=>$value,
			"nodes"=>array()
		);
	}
	
	//////////////////////////////////////////////////////////////
	// Searching the trie
	//////////////////////////////////////////////////////////////
	
	// Require starting node and word array
	private function transverse($node, &$words) {
		
		// Base case
		if(!is_null($node["data"])) array_push($words, $node["data"]);
		
		// Recursion
		foreach($node["nodes"] as $val) {
			$this->transverse($val, $words);
		}
	
	}
	
	// Searches the trie for a word
	public function search($word) {
		$node = $this->trie;
		for($i = 0;$i<strlen($word);$i++) {
			if(isset($node["nodes"][$word{$i}])) {
				$node = $node["nodes"][$word{$i}];
			}
			else break;
		}
		
		return $word === $node["data"];
	}
	
	// Dump all portions of array with fragment
	public function dump($fragment) {
	
		// Dig for root transverse array
		$node = $this->trie;
		for($i = 0;$i<strlen($fragment);$i++) {
			if(isset($node["nodes"][$fragment{$i}])) {
				$node = $node["nodes"][$fragment{$i}];
			}
			else {
				// Not a valid fragment/no words have this frag
				return array(); 
			}
		}
		
		$words = array();
		$this->transverse($node, $words);
		return $words;
	
	}

}

// Testing:

// $trie = new Trie();
// 
// $trie->add("testing");
// $trie->add("test");
// $trie->add("testings");
// $trie->add("te");
// $trie->add("wtf");
// $trie->add("needsleep");
// 
// print_r($trie->trie);
// 
// echo "<br>";
// 
// if($trie->search("testing")) {
// 	echo "testing found<br>";
// }
// 
// print_r($trie->dump(""));
// echo "<br>";
// print_r($trie->dump("test"));

?>
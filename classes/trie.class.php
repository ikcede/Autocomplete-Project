
<?php

/**
 * 
 * This class is a general Trie data structure
 *
 * @author: ikcede
 * 
 **/
 
class Trie {

	var $data = null;
	
	// Allow construction from another Trie
	public function __construct($data = array()) {
		$this->data = array();
	}
	
	/**
	 * Adds a string and an associated value of that string into the trie
	 * If $value is null or not added, it is ignored
	 * 
	 * $data - A String value
	 * $value - Any mixed value
	 * 
	 * @return - true if a value was changed or string inserted, false if preexisting
	 **/
	public function add($data, $value = NULL) {
	
		$node = &$this->data;
		$index = 0;
	
		if(!empty($data)) {
			// Go through all existing nodes
			while($index<strlen($data)) {
				if(!is_null($node) && array_key_exists($data{$index}, $node)) {
					$node = &$node[$data{$index++}];
				}
				else break;
			}
		
			// Add in most nonexisting nodes
			while($index<strlen($data)) {
				if(is_null($node)) {
					$node = array();
				}
				$node[$data{$index}] = null;
				$node = &$node[$data{$index++}];
			}
			
		}
		
		// New branch
		if(is_null($node)) {
			$node = array("vl"=>$value);
			return true;
		}
		
		// Assign the new value
		if(!array_key_exists("vl", $node)) {
			$node["vl"] = $value;
			return true;
		}
		
		else {
			if($node["vl"] === $value) {
				return false;
			} else {
				$node["vl"] = $value;
				return true;
			}
		}
	
	}
	
	/**
	 * Deletes a string and its value from the trie. Returns true on success,
	 * false if the value was not found
	 * 
	 * $data - A String value
	 * 
	 * @return - boolean
	 **/
	public function delete($data) {
		
		$node = &$this->data;
		$prev = null;
		if(!empty($data)) $prev = &$node;
		$key = empty($data) ? null : $data{0};
		
		// Iterate over characters
		for($i=0;$i<strlen($data);$i++) {
			if(!is_null($node) && array_key_exists($data{$i}, $node)) {
				
				// Store current node
				if(array_key_exists("vl",$node)) {
					$prev = &$node;
					$key = $data{$i};
				} else if (count($node) > 1) {
					$prev = &$node;
					$key = $data{$i};
				}
				
				// Go to next node
				$node = &$node[$data{$i}];
			} else return false;
		}
		
		if(array_key_exists("vl",$node)) {
			if(is_null($prev)) {
				unset($node["vl"]); // This is the root node
			} else {
				unset($prev[$key]); // Delete all dangling characters leading up
			}
			return true;
		}
	
		return false;
	}
	
	/**
	 * Find a string in the Trie and returns true if found, false if not
	 * 
	 * $data - A String value
	 * 
	 * @return - boolean
	 **/
	public function find($data) {
		
		// Iterate through the characters
		$node = &$this->data;
		for($i=0;$i<strlen($data);$i++) {
			if(!is_null($node) && array_key_exists($data{$i}, $node)) {
				$node = &$node[$data{$i}];
			} else return false;
		} 
		
		// Check endpoint
		return !is_null($node) && array_key_exists("vl",$node);
	}
	
	/**
	 * Get the data from a string, returning false if not found
	 * 
	 * $data - A String value
	 * 
	 * @return - mixed
	 **/
	public function getVal($data) {
		
		// Iterate through the characters
		$node = &$this->data;
		for($i=0;$i<strlen($data);$i++) {
			if(!is_null($node) && array_key_exists($data{$i}, $node)) {
				$node = &$node[$data{$i}];
			} else return false;
		} 
		
		// Check value
		if(is_null($node) || !array_key_exists("vl",$node)) return false;
		return $node["vl"];
	}
	
	/**
	 * Pre-order transversal of nodes starting at $prefix
	 * If $prefix is the empty string, will list all nodes and their values
	 *
	 * $prefix - String of starting characters, optional
	 *
	 * @return - Array of String keys and their values in the trie
	 */
	public function listWords($prefix = "") {
		$node = $this->data;
		$words = array();
		
		// Get to the node at $prefix first
		for($i=0;$i<strlen($prefix);$i++) {
			if(!is_null($node) && array_key_exists($prefix{$i}, $node)) {
				$node = &$node[$prefix{$i}];
			} else return $words;
		}
		
		$this->listHelper($node, $prefix, $words);
		return $words;
	}
	
	// Recurses into the node and adds words into word array
	private function listHelper(&$node, $fragment, &$words) {
		if(!is_null($node) && array_key_exists("vl",$node)) {
			$words[$fragment] = $node["vl"];
		}
		foreach($node as $key=>$val) {
			if($key === "vl") continue;
			$this->listHelper($node[$key],$fragment.$key,$words);
		}
	}
	
	/**
	 * Converts the trie into a JSON format
	 *
	 * $params - bitmask for json_encode
	 *
	 */ 
	public function toJSON($params = 0) {
		return json_encode($this->data, $params);
	}
	
	/**
	 * Converts a JSON trie into the current object
	 *
	 * $data - a JSON String
	 *
	 */
	public function fromJSON($data) {
		$this->data = json_decode($data, true);
	}

}

?>
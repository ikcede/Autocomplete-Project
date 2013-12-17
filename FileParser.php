<?php

class FileParser {
	
	var $file = "";
	var $data = array();
	
	public function __construct($file) {
		$this->file = $file;
		$this->data = array();
	}
	
	/**
	 * Normalizes all strings in a file
	 **/
	public function normalizeFile($punc = false) {
		
		$handle = fopen($this->file, "r");
		if(!$handle) return false;
		//Output a line of the file until the end is reached
		while(!feof($handle)) {
			$this->normalizeLine(fgets($handle), $punc);
		}
		fclose($handle);
		return true;
	}
	
	// Normalize a string into tokens
	// Can choose to include punctuation
	private function normalizeLine($string, $punc = false) {
		
		$chars = str_split($string);
		$word = "";
		
		foreach($chars as $val) {
			if($this->isCharacter($val) || ($punc && !ctype_space($val))) {
				$word .= strtolower($val);
			} else {
				if($word == "") {
					// Do nothing
				} else {
					array_push($this->data,$word);
					$word = "";
				}
			} 
			
		}
		
		// Clean up
		if($word == "") {
			// Do nothing
		} else {
			array_push($this->data,$word);
		}
		
	}
	
	// Checks if something is a character
	private function isCharacter($c) {
		$num = ord($c);
		return ($num >= 65 && $num <=90) || ($num>=97 && $num<=122) || $num == 39 || $num == 45; // "'" and "-"
	}
	
	// This is for reading saved data
	public function readFile($type) {
		
	}
	
	// This is for saving data
	public function saveFile($path) {
		// Saves all the data into path
	
	}
	
}

// Testing:

// $test = new FileParser("learn/data/testarticle.txt");
// $test->normalizeFile();
// print_r($test->data);

?>
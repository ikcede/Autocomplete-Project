<?php

// No normalization done cause time

$file = file_get_contents("learn/data/nytimearticle.txt");

$tokens = preg_split("/[\s]+/", $file);

$hashes = array();

foreach($tokens as $val) {
	
	if(!isset($hashes[$val])) {
		$hashes[$val] = 1;
	} else {
		$hashes[$val]++;
	}

}

echo serialize($hashes);

?>
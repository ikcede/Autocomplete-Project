<!doctype html>
<html>
<head>

<title>Essay Helper</title>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/ace/ace.js"></script>
<script type="text/javascript" src="js/trie.js"></script>
<script type="text/javascript" src="js/ngram.js"></script>
<script type="text/javascript" src="js/js.js"></script>

<link rel="stylesheet" type="text/css" href="css/style.css" />

</head>

<body>

<div id="editor"></div>

<div id="acbox"></div>

<!-- Not required right now, do if have time -->
<div id="header">

<div id="title">
Essay Helper
</div>

<div id="gear">
<img src="img/gear.png" />
</div>

<div id="status" class="loading">
<span>Status: </span><img src="img/redstatus.gif" /> <span class="red">Loading...</span>
</div>

</div>

<div id="modal">

</div>

<div id="about">

<div class="close-button">Close</div>

<h2>About</h2>

<p>This is a writing tool that uses the Ace text editor and an autocomplete feature to help 
the writer figure out what to write. As the writer types, the editor will use learned data to provide
likely autocompletions to the current word. </p>

<p>So far, this project uses a mixture model of ngrams to compute the data and order autocompletions. 
The algorithm breaks the data down into words and creates a bigram and a unigram from the words. Then,
while typing, the algorithm looks at the word just before and tries to match that into the bigram with 
the fragment that is currently being typed. If bigrams are not found, it'll default to the unigram (word
frequency) model.</p>

<p>Controls:<br>
<strong>ctrl+enter</strong> -- Choose top word<br>
<strong>ctrl+space</strong> -- Show/hide additional suggestions<br>
Also, clicking on a word will use it as an autocomplete.
</p>

<p>By default, the data used is Charles Dickens' <em>A Tale of Two Cities</em>, but this can be changed
in the settings. Custom documents can also be used--for instance, using personal writing as data allows
the editor to learn some of your writing style.</p>

</div>

<div id="settings">

<div class="close-button">Close</div>

<h2>Settings</h2>

Autocomplete: <span class="green ac-toggle">ON</span>
<br><br>

Custom Data (will be included in algorithm):<br>
<textarea id="custom"></textarea>

</div>

<!-- Open sans webfont -->
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

</body>

</html>
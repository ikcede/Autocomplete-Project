

// Autocomplete plugin for ACE editor
var AutoComplete = function(editor, settings) {
	
	var el = null;
	var data = [];
	var bigram = null;
	var unigram = null;
	var trie = null;
	
	// Track which change its on
	var change = 0;
	
	// Set default settings
	settings = $.extend({
	
		// Algorithm
		algo: "simple",
		
		// It is expanded?
		expand: false,
		
		// If the autocomplete is on or off
		on: true,
		
		// Whether to include punctuation or not
		punc: false,
		
		// Number of previous words to use
		words: 3 
		
	}, settings);

	var functions = {
		// Check to see if something is a character
		isCharacter: function(c) {
			var num = c.charCodeAt(0);
			return (num >= 65 && num <=90) || (num>=97 && num<=122) || num == 39 || num == 45; // "'" and "-"
		},
	
		// Checks to see if a character is whitespace
		isWhitespace: function(c) {
			return c == " " || c == "\n" || c == "\r" || c == "\t" || c == "\f" || c == "\v";
		},
	
		// Tokenizes the words in a line
		// Will tokenize words up to the stopping point
		tokenize: function(str, stp) {
			if(typeof(stp) === "undefined") stp = str.length;
	
			var words = [];
			var curfrag = "";

			for(var i=0;i<stp;i++) {
				if(this.isWhitespace(str.charAt(i))) {
					if(curfrag == "") {}
					else {
						words.push(curfrag);
						curfrag = "";
					}
				}
				else curfrag = curfrag + str.charAt(i);
			}
		
			if(curfrag == "") {}
			else {
				words.push(curfrag.toLowerCase());
				curfrag = "";
			}
		
			return words;
		
		},
	
		// Function to control most of the autocomplete interaction
		autocomplete: function(cx, change) {
			// Check to see if it's on and loaded
			if(!this.settings.on) return false;
			if(this.bigram === null) return false;
		
			setTimeout(function() {
				// Gets row and column
				var cursorpos = cx.editor.getSession().selection.getCursor();
	
				// Get the current line
				var curline = cx.editor.getSession().getLine(cursorpos.row);
	
				// If the cursor is at 0,0 and there is nothing after it, return
				if(cursorpos.row == 0 && cursorpos.column == 0 && curline == "") {
		
				}
	
				// If the line is blank, return
				else if(curline == "" || cursorpos.column == 0) {
	
				}
	
				// If there is a character after it, return 
				else if(curline.length > cursorpos.column && cx.isCharacter(curline.charAt(cursorpos.column))) {
		
				}
	
				// If a non-character was just typed, return true
				else if(!cx.isCharacter(curline.charAt(cursorpos.column-1))) {
		
				}
			
				else {
				
					// Tokenize
					var words = cx.tokenize(curline, cursorpos.column+1);
					words = words.reverse();
				
					if(words.length >= cx.settings.words) {
						words = words.slice(0,cx.settings.words+1);
					} else {
						var line = cursorpos.row - 1;
						while(words.length <= cx.settings.words && line >= 0) {
							var prevline = cx.editor.getSession().getLine(line--);
					
							var tempWords = cx.tokenize(prevline);
							tempWords.reverse();
					
							for(var i=0;i<tempWords.length && words.length <= cx.settings.words;i++) {
								words.push(tempWords[i]);
							}
				
						}
					}
						
					// Clean all the words of non-characters
					if(!cx.punc) {
						for(var i=0;i<words.length;i++) {
							for(var j=0;j<words[i].length;j++) {
								if(!cx.isCharacter(words[i].charAt(j))) {
									words[i] = words[i].slice(0, j) + words[i].slice(j+1, words[i].length);
								}
							}
						}
					}
				
					// Make data call
					// Altered to a cache call
					cx.getData(words, change);
					
					// Display
					cx.render(change);
			
				}
			},15);

		},
		
		// Given a bunch of words, query the bigram and unigram for hits
		getData: function(words, change) {
			
			// Get all possible words it could be that are stored in trie
			var completions = this.trie.list(words[0]);
			
			// Try to get all bigrams ending in this word
			var hits = {};
			var empty = true;
			for(var word in completions) {
				if(this.bigram.get(completions[word], [words[1]]) > 0) {
					hits[completions[word]] = this.bigram.get(completions[word], [words[1]]);
					empty = false;
				}
			}
			
			if(empty) {
				// Get all unigram ones instead
				for(var word in completions) {
					if(this.unigram.get(completions[word]) > 0) {
						hits[completions[word]] = this.unigram.get(completions[word]);
						empty = false;
					}
				}
				
			}
			
			this.sortData(hits);

			// words[0] is the current fragment, take that would of each returned word
			for(var i = 0;i<this.data.length;i++) {
				this.data[i] = {
					data:this.data[i],
					write:this.data[i].slice(words[0].length)
				};
			}

		},
		
		// Helper function that resets and sorts current data based on hits
		// Sorts by ngram frequency first and then unigram freq and then alphabetically
		sortData: function(hits) {
			this.data = [];
			for(var key in hits) {
				this.data.push(key);
			}
			
			var context = this;
			this.data.sort(function(a,b) {
				if(hits[a] == hits[b]) {
					if(context.unigram.get(a) == context.unigram.get(b)) {
						return a < b ? 1 : -1;
					}
					return context.unigram.get(a) < context.unigram.get(b) ? 1 : -1;
				}
				return hits[a] < hits[b] ? 1 : -1;
			});
			
			var slicelength = this.data.length > 10 ? 10 : this.data.length;
			this.data = this.data.slice(0,slicelength);
		},
		
		//---------------------------------------------------------------
		// Rendering
		//---------------------------------------------------------------
		
		// Renders the autocomplete box, adding in all the necessary elements
		render: function(change) {
		
			this.position();
		
			if(this.change == change) {
				this.el.show();
				this.el.html("");
				
				// Display words
				if(this.settings.expand && this.data && this.data.length > 1) {
					for(var i=0;i<this.data.length;i++) {
						this.el.append("<div frag=\""+this.data[i].write+"\">"
							+this.data[i].data+"</div>");
					}
				} else if(this.data && this.data.length > 0) {
					this.el.html("<div frag=\""+this.data[0].write+"\">"
						+this.data[0].data+"</div>");
				} else {
					this.el.html("");
				}
			}
		},
		
		// Positions the autocomplete box
		position: function() {
			var cursor = $(".ace_cursor")[0];
			var parent = $("#editor")[0];
			$(this.el).css({
				left: Number(cursor.offsetLeft) + Number(parent.offsetLeft) + 5,
				top: Number(cursor.offsetTop) + Number(parent.offsetTop) + 26
			});
		},
		
		//---------------------------------------------------------------
		// Server control
		//---------------------------------------------------------------
	
		// Get all data structures from the server
		getStructures: function(params, callback) {
			var context = this;
			$.get("server.php", params, function(response) {
				// Response will be a JSON
				response = JSON.parse(response);
				context.bigram = Ngram(response.bigram);
				context.unigram = Ngram(response.unigram);
				context.trie = Trie(response.trie);
				
				if(!(typeof(callback) === "undefined")) {
					callback();
				}
			});
		},
	
		//---------------------------------------------------------------
		// Set up box and eventhandlers
		//---------------------------------------------------------------
	
		init: function() {
			// Set the autocomplete box
			this.el = $("#acbox");
			this.el.hide();
		
			var cx = this;
			
			//-----------------------------
			// Event Handlers
			//-----------------------------
			
			editor.getSession().selection.on('changeCursor', function(e) {
				cx.el.hide();
				cx.change = (cx.change + 1) % 100;
				var change = cx.change + 0; // Value copy
				cx.autocomplete(cx, change);
			});	
		
			// Allow for autocomplete expansion
			editor.commands.addCommand({
				name: 'ACExpand',
				bindKey: {win: 'Ctrl-Space',  mac: 'Ctrl-Space'},
				exec: function(editor) {
					if(cx.el.is(":visible") && !cx.settings.expand) {
						cx.settings.expand = true;
					} else if(cx.settings.expand) {
						cx.settings.expand = false;
					}
					cx.render(cx.change);
				},
				readOnly: false // false if this command should not apply in readOnly mode
			});
	
			// Allow for autocomplete completion on enter
			editor.commands.addCommand({
				name: 'ACComplete',
				bindKey: {win: 'Enter',  mac: 'Enter'},
				exec: function(editor) {
					if(cx.el.is(":visible") && $("#acbox div").length > 0) {
						editor.getSession().insert(editor.getSession().selection.getCursor(), 
							$($("#acbox div")[0]).attr("frag") + " ");
						editor.focus();
					}
					// Default insert enter
					else {
						editor.getSession().insert(editor.getSession().selection.getCursor(),"\n");
						editor.focus();
					}
				},
				readOnly: false // false if this command should not apply in readOnly mode
			});
	
			editor.commands.addCommand({
				name: 'ACSwitch',
				bindKey: {win: 'Ctrl-P',  mac: 'Ctrl-P'},
				exec: function(editor) {
					cx.settings.on = !cx.settings.on;
					if(cx.settings.on) {
						cx.autocomplete(cx, cx.change+0);
					} else cx.el.hide();
				},
				readOnly: false // false if this command should not apply in readOnly mode
			});
	
			$(document).on("click", "#acbox div", function() {
				editor.getSession().insert(editor.getSession().selection.getCursor(), $(this).attr("frag") + " ");
				editor.focus();
			});
		}
	};
		
	return $.extend({
		el: el,
		data: data,
		unigram: unigram,
		bigram: bigram,
		trie: trie,
		editor: editor,
		change: change,
		settings: settings
	}, functions);
};

// Flip the status on the top
function flipStatus() {
	if($("#status").hasClass("loading")) {
		$("#status").addClass("done").removeClass("loading").html('\
			<span>Status: </span><img src="img/greenstatus.png" /> <span class="green">Done</span>');
	} else {
		$("#status").addClass("loading").removeClass("done").html('\
			<span>Status: </span><img src="img/redstatus.gif" /> <span class="red">Loading...</span>');
	}
}

var ac;
var ta_change = false;
var custom = "";

// Ready function
$(document).ready(function() {

	var editor = ace.edit("editor");
	editor.setTheme("ace/theme/clouds");
	// editor.getSession().setMode("ace/mode/less");
	
	// Word wrap
	editor.getSession().setUseWrapMode(true);
	
	// Hide print margin
	editor.setShowPrintMargin(false);
	
	// Hide line numbers
	editor.renderer.setShowGutter(false);
	
	// Change font size
	editor.setFontSize(15);

	ac = AutoComplete(editor, {});
	ac.init();
	ac.getStructures({}, function() {
		flipStatus();
	});
	
	$("#title").click(function() {
		$("#modal").html($("#about").html()).show();
	});
	
	$("#gear").click(function() {
		$("#modal").html($("#settings").html()).show();
		$("#modal #custom").val(custom);
	});
	
	$("#modal").on("click",".close-button",function() {
		$("#modal").hide();
		if(ta_change) {
			ta_change = false;
			flipStatus();
			ac.getStructures({custom: $("#modal #custom").val()}, function() {
				flipStatus();
			});
		}
	});
	
	// Checking a change
	$("#modal").on("change","#custom",function() {
		ta_change = true;
		custom = $("#modal #custom").val();
	});
	
	// Toggling autocomplete on/off
	$("#modal").on("click",".ac-toggle",function() {
		if(ac.settings.on) {
			ac.el.hide();
			$(this).removeClass("green").addClass("red").html("OFF");
		} else {
			$(this).removeClass("red").addClass("green").html("ON");
			ac.el.hide();
			ac.change = (ac.change + 1) % 100;
			var change = ac.change + 0; // Value copy
			ac.autocomplete(ac, change);
		}
		ac.settings.on = !ac.settings.on;
	});
	
});
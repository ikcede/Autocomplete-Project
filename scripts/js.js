
// Settings for which documents to use!
// Whoops insert only the fragment instead of the word itself

// Autocomplete plugin for ACE editor
var AutoComplete = function(editor, settings) {
	
	var el = null;
	var data = [];
	
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
	
		// Positions the autocomplete box
		position: function() {
			var cursor = $(".ace_cursor")[0];
			$(this.el).css({
				left: cursor.offsetLeft + $(".ace_gutter-layer").width(),
				top: Number(cursor.offsetTop) + 25
			});
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
				words.push(curfrag);
				curfrag = "";
			}
		
			return words;
		
		},
	
		// Renders the autocomplete box
		render: function(change) {
			if(this.change == change) {
				this.el.show();
				this.el.html("");
				
				// Display words
				if(this.settings.expand && this.data && this.data.length > 1) {
					for(var i=0;i<this.data.length;i++) {
						this.el.append("<div frag='"+this.data[i].write.replace(/'/g, "\\'")+"'>"
							+this.data[i].data+"</div>");
					}
				} else if(this.data && this.data.length > 0) {
					this.el.html("<div frag='"+this.data[0].write.replace(/'/g, "\\'")+"'>"
						+this.data[0].data+"</div>");
				} else {
					this.el.html("");
				}
			}
		},
	
		// Function to control most of the autocomplete interaction
		autocomplete: function(cx, change) {
			// Check to see if it's on
			if(!this.settings.on) return false;
		
			cx.position();
		
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
					cx.getData(words, change);
			
				}
			},15);

		},
	
		//---------------------------------------------------------------
		// Server control
		//---------------------------------------------------------------
	
		// Given a bunch of words, get data back from server about them
		getData: function(words, change) {
			var context = this;
			$.get("server.php", {
				data: JSON.stringify(words), 
				algo: context.settings.algo
			}, function(response) {
				// Response will be a JSON
				response = JSON.parse(response);
				context.data = response.data[0];
				
				// words[0] is the current fragment, take that would of each returned word
				for(var i = 0;i<context.data.length;i++) {
					context.data[i] = {
						data:context.data[i],
						write:context.data[i].slice(words[0].length)
					};
				}
				
				context.render(change);
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
				readOnly: true // false if this command should not apply in readOnly mode
			});
	
			// Allow for autocomplete completion
			editor.commands.addCommand({
				name: 'ACComplete',
				bindKey: {win: 'Ctrl-Enter',  mac: 'Ctrl-Enter'},
				exec: function(editor) {
					if(cx.el.is(":visible") && $("#acbox div").length > 0) {
						editor.getSession().insert(editor.getSession().selection.getCursor(), 
							$($("#acbox div")[0]).attr("frag") + " ");
						editor.focus();
					}
				},
				readOnly: true // false if this command should not apply in readOnly mode
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
				readOnly: true // false if this command should not apply in readOnly mode
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
		editor: editor,
		change: change,
		settings: settings
	}, functions);
};

var ac;

// Ready function
$(document).ready(function() {

	var editor = ace.edit("editor");
	editor.setTheme("ace/theme/tomorrow_night");
	// editor.getSession().setMode("ace/mode/less");
	
	editor.getSession().setUseWrapMode(true);

	ac = AutoComplete(editor, {});
	ac.init();
});
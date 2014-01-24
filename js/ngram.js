// Wrapper for clientside Ngram model

var Ngram = function(data) {
	
	var functions = {
	
		"buildGram": function(word, previous) {
			if(!(typeof(previous) === "undefined")) {
				for(var str in previous) {
					word = previous[str] + "|" + word;
				}
			}
			return word;
		},
		
		"get": function(word, previous) {
			
			word = this.buildGram(word, previous);
			return this.data[word] ? this.data[word] : 0;
		
		},
		
		"add": function(word, previous) {
			
			word = this.buildGram(word);
			if(this.data[word]) {
				this.data[word]++;
			} else {
				this.data[word] = 0;
			}
		
		},
		
		"delete": function(word, previous) {
			
			word = this.buildGram(word);
			
			if(this.data[word]) {
				delete this.data[word];
				return true;
			}
			return false;
		
		}
	
	};
	
	return $.extend({data:data}, functions);
	
};
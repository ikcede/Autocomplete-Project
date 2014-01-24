
// A quick js trie encapsulation for this project

var Trie = function(data) {

	// Default constructor
	if(typeof(data) === "undefined") {
		data = {};
	}

	var functions = {
	
		"add": function(data, value) {
		
			// Default value to null
			if(typeof(value) === "undefined") value = null;
		
			var node = this.data;
			var index = 0;
	
			if(data.length > 0) {
				// Go through all existing nodes
				while(index<data.length) {
					if(node.hasOwnProperty(data.charAt(index)) && node[data.charAt(index)] != null) {
						node = node[data.charAt(index++)];
					}
					else break;
				}
		
				// Add in most nonexisting nodes
				while(index<data.length) {
					if(!node.hasOwnProperty(data.charAt(index)) || node[data.charAt(index)] === null) {
						node[data.charAt(index)] = {};
						node = node[data.charAt(index++)];
					}
				}
			
			}
		
			// New branch
			if(node === null) {
				node = {"vl":value};
				return true;
			}
		
			// Assign the new value
			if(!node.hasOwnProperty("vl")) {
				node["vl"] = value;
				return true;
			}
		
			else {
				if(node["vl"] === value) {
					return false;
				} else {
					node["vl"] = value;
					return true;
				}
			}
		
		},
	
		"find": function(word) {
		
			// Null check
			if(null === this.data) return false;
			
			var node = this.data;
			
			for(var i=0;i<word.length;i++) {
				if(node.hasOwnProperty(word[i])) {
					node = node[word[i]];
				} else return false;
			} 
		
			// Check endpoint
			return node.hasOwnProperty("vl");
		
		},
		
		"delete": function(data) {
			var node = this.data;
			var prev = null;
			if(data.length > 0) prev = node;
			var key = data.length == 0 ? null : data.charAt[0];
		
			// Iterate over characters
			for(var i=0;i<data.length;i++) {
				if(node.hasOwnProperty(data.charAt(i))) {
				
					// Store current node
					if(node.hasOwnProperty("vl")) {
						prev = node;
						key = data.charAt(i);
					} else if(Object.keys(node).length > 1) {
						prev = node;
						key = data.charAt(i);
					}
				
					// Go to next node
					node = node[data.charAt(i)];
				} else return false;
			}
		
			if(node.hasOwnProperty("vl")) {
				if(!prev) {
					delete node["vl"]; // This is the root node
				} else {
					delete prev[key]; // Delete all dangling characters leading up
				}
				return true;
			}
	
			return false;
		},
		
		"list": function(prefix) {
		
			// Null check
			if(null === this.data) return [];
		
			var node = this.data;
			var words = [];
		
			// Get to the node at $prefix first
			for(var i=0;i<prefix.length;i++) {
				if(node.hasOwnProperty(prefix[i])) {
					node = node[prefix[i]];
				} else return false;
			}
			
			// Aid in listing words
			var listHelper = function(node, frag, list) {
				if(node.hasOwnProperty("vl")) {
					words.push(frag);
				}
				for(var key in node) {
					if(key == "vl") continue;
					listHelper(node[key],frag+key,words);
				}
			
			};
		
			listHelper(node, prefix, words);
			return words;
		
		}
	
	};
	
	// Add functions onto the trie data
	return $.extend({data: data}, functions);

};
/*

highlight v3

Highlights arbitrary terms.

<http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html>

MIT license.

Johann Burkard
<http://johannburkard.de>
<mailto:jb@eaio.com>

*/

jQuery.fn.highlight = function(pat,real_code) {
 function innerHighlight(node, pat) {
  var skip = 0;
  if (node.nodeType == 3) {
   var pos = node.data.toUpperCase().indexOf(pat);
   if (pos >= 0) {
    var spannode = document.createElement('a');
    spannode.className = 'glossary-ajax';
    spannode.style.color = 'blue';
	spannode.style.textDecoration = 'none';
    spannode.name = 'link'+real_code;
    spannode.href = '#';
    var SearchRegExp = new RegExp("(" + pat +")","gi");
    var MatchRegExp = node.nodeValue.match(SearchRegExp);

    if (MatchRegExp == null) {
        MatchRegExp = new Array();
    }
	//alert(node.nodeValue+'-----'+'---'+node.nodeValue[pat.length+1])
    if (MatchRegExp.length > 0 && node.nodeValue[pat.length+1] != '') {
     	var middlebit = node.splitText(pos);
    	var endbit = middlebit.splitText(pat.length);
    	if (endbit.nodeValue[0] == null || endbit.nodeValue[0] == ' ') {
        	var middleclone = middlebit.cloneNode(true);
    		spannode.appendChild(middleclone);
        	middlebit.parentNode.replaceChild(spannode, middlebit);
    	}  	
    }
    skip = 1;
   }
  }
  else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
   for (var i = 0; i <node.childNodes.length ; ++i) {
    i += innerHighlight(node.childNodes[i], pat);
   }
  }
  return skip;
 }
 return this.each(function() {
  innerHighlight(this, pat.toUpperCase());
 });
};

jQuery.fn.removeHighlight = function() {
 return this.find("a.highlight").each(function() {
  this.parentNode.firstChild.nodeName;
  with (this.parentNode) {
   replaceChild(this.firstChild, this);
   normalize();
  }
 }).end();
};
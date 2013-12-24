/*
 *  /MathJax/extensions/Safe.js
 *  
 *  Copyright (c) 2009-2013 The MathJax Consortium
 *
 *  Part of the MathJax library.
 *  See http://www.mathjax.org for details.
 * 
 *  Licensed under the Apache License, Version 2.0;
 *  you may not use this file except in compliance with the License.
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

(function(d,c){var f="2.2";var a=MathJax.Hub.CombineConfig("Safe",{allow:{URLs:"safe",classes:"safe",cssIDs:"safe",styles:"safe",fontsize:"all",require:"safe"},sizeMin:0.7,sizeMax:1.44,safeProtocols:{http:true,https:true,file:true,javascript:false},safeStyles:{color:true,backgroundColor:true,border:true,cursor:true,margin:true,padding:true,textShadow:true,fontFamily:true,fontSize:true,fontStyle:true,fontWeight:true,opacity:true,outline:true},safeRequire:{action:true,amscd:true,amsmath:true,amssymbols:true,autobold:false,"autoload-all":false,bbox:true,begingroup:true,boldsymbol:true,cancel:true,color:true,enclose:true,extpfeil:true,HTML:true,mathchoice:true,mhchem:true,newcommand:true,noErrors:false,noUndefined:false,unicode:true,verb:true}});var e=a.allow;if(e.fontsize!=="all"){a.safeStyles.fontSize=false}var b=MathJax.Extension.Safe={version:f,config:a,div1:document.createElement("div"),div2:document.createElement("div"),filter:{href:"filterURL",src:"filterURL",altimg:"filterURL","class":"filterClass",style:"filterStyles",id:"filterID",fontsize:"filterFontSize",mathsize:"filterFontSize",scriptminsize:"filterFontSize",scriptsizemultiplier:"filterSizeMultiplier",scriptlevel:"filterScriptLevel"},filterURL:function(g){var h=(g.match(/^\s*([a-z]+):/i)||[null,""])[1].toLowerCase();if(e.URLs==="none"||(e.URLs!=="all"&&!a.safeProtocols[h])){g=null}return g},filterClass:function(g){if(e.classes==="none"||(e.classes!=="all"&&!g.match(/^MJX-[-a-zA-Z0-9_.]+$/))){g=null}return g},filterID:function(g){if(e.cssIDs==="none"||(e.cssIDs!=="all"&&!g.match(/^MJX-[-a-zA-Z0-9_.]+$/))){g=null}return g},filterStyles:function(j){if(e.styles==="all"){return j}if(e.styles==="none"){return null}try{var i=this.div1.style,h=this.div2.style;i.cssText=j;h.cssText="";for(var g in a.safeStyles){if(a.safeStyles.hasOwnProperty(g)){var k=this.filterStyle(g,i[g]);if(k!=null){h[g]=k}}}j=h.cssText}catch(l){j=null}return j},filterStyle:function(g,h){if(typeof h!=="string"){return null}if(h.match(/^\s*expression/)){return null}if(h.match(/javascript:/)){return null}return(a.safeStyles[g]?h:null)},filterSize:function(g){if(e.fontsize==="none"){return null}if(e.fontsize!=="all"){g=Math.min(Math.max(g,a.sizeMin),a.sizeMax)}return g},filterFontSize:function(g){return(e.fontsize==="all"?g:null)},filterSizeMultiplier:function(g){if(e.fontsize==="none"){g=null}else{if(e.fontsize!=="all"){g=Math.min(1,Math.max(0.6,g)).toString()}}return g},filterScriptLevel:function(g){if(e.fontsize==="none"){g=null}else{if(e.fontsize!=="all"){g=Math.max(0,g).toString()}}return g},filterRequire:function(g){if(e.require==="none"||(e.require!=="all"&&!a.safeRequire[g.toLowerCase()])){g=null}return g}};d.Register.StartupHook("TeX HTML Ready",function(){var g=MathJax.InputJax.TeX;g.Parse.Augment({HREF_attribute:function(j){var i=b.filterURL(this.GetArgument(j)),h=this.GetArgumentMML(j);if(i){h.With({href:i})}this.Push(h)},CLASS_attribute:function(i){var j=b.filterClass(this.GetArgument(i)),h=this.GetArgumentMML(i);if(j){if(h["class"]!=null){j=h["class"]+" "+j}h.With({"class":j})}this.Push(h)},STYLE_attribute:function(i){var j=b.filterStyles(this.GetArgument(i)),h=this.GetArgumentMML(i);if(j){if(h.style!=null){if(j.charAt(j.length-1)!==";"){j+=";"}j=h.style+" "+j}h.With({style:j})}this.Push(h)},ID_attribute:function(j){var i=b.filterID(this.GetArgument(j)),h=this.GetArgumentMML(j);if(i){h.With({id:i})}this.Push(h)}})});d.Register.StartupHook("TeX Jax Ready",function(){var i=MathJax.InputJax.TeX,h=i.Parse,g=b.filter;h.Augment({Require:function(j){var k=this.GetArgument(j).replace(/.*\//,"").replace(/[^a-z0-9_.-]/ig,"");k=b.filterRequire(k);if(k){this.Extension(null,k)}},MmlFilterAttribute:function(j,k){if(g[j]){k=b[g[j]](k)}return k},SetSize:function(j,k){k=b.filterSize(k);if(k){this.stack.env.size=k;this.Push(i.Stack.Item.style().With({styles:{mathsize:k+"em"}}))}}})});d.Register.StartupHook("TeX bbox Ready",function(){var g=MathJax.InputJax.TeX;g.Parse.Augment({BBoxStyle:function(h){return b.filterStyles(h)}})});d.Register.StartupHook("MathML Jax Ready",function(){var h=MathJax.InputJax.MathML.Parse,g=b.filter;h.Augment({filterAttribute:function(i,j){if(g[i]){j=b[g[i]](j)}return j}})});d.Startup.signal.Post("Safe Extension Ready");c.loadComplete("[MathJax]/extensions/Safe.js")})(MathJax.Hub,MathJax.Ajax);


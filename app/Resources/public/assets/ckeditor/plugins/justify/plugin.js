﻿/*
 Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
*/
(function(){function n(a,c){c=void 0===c||c;var b;if(c)b=a.getComputedStyle("text-align");else{for(;!a.hasAttribute||!a.hasAttribute("align")&&!a.getStyle("text-align");){b=a.getParent();if(!b)break;a=b}b=a.getStyle("text-align")||a.getAttribute("align")||""}b&&(b=b.replace(/(?:-(?:moz|webkit)-)?(?:start|auto)/i,""));!b&&c&&(b="rtl"==a.getComputedStyle("direction")?"right":"left");return b}function g(a,c,b){this.editor=a;this.name=c;this.value=b;this.context="p";c=a.config.justifyClasses;var h=a.config.enterMode==
CKEDITOR.ENTER_P?"p":"div";if(c){switch(b){case "left":this.cssClassName=c[0];break;case "center":this.cssClassName=c[1];break;case "right":this.cssClassName=c[2];break;case "justify":this.cssClassName=c[3]}this.cssClassRegex=new RegExp("(?:^|\\s+)(?:"+c.join("|")+")(?\x3d$|\\s)");this.requiredContent=h+"("+this.cssClassName+")"}else this.requiredContent=h+"{text-align}";this.allowedContent={"caption div h1 h2 h3 h4 h5 h6 p pre td th li":{propertiesOnly:!0,styles:this.cssClassName?null:"text-align",
classes:this.cssClassName||null}};a.config.enterMode==CKEDITOR.ENTER_BR&&(this.allowedContent.div=!0)}function l(a){var c=a.editor,b=c.createRange();b.setStartBefore(a.data.node);b.setEndAfter(a.data.node);for(var h=new CKEDITOR.dom.walker(b),d;d=h.next();)if(d.type==CKEDITOR.NODE_ELEMENT)if(!d.equals(a.data.node)&&d.getDirection())b.setStartAfter(d),h=new CKEDITOR.dom.walker(b);else{var e=c.config.justifyClasses;e&&(d.hasClass(e[0])?(d.removeClass(e[0]),d.addClass(e[2])):d.hasClass(e[2])&&(d.removeClass(e[2]),
d.addClass(e[0])));e=d.getStyle("text-align");"left"==e?d.setStyle("text-align","right"):"right"==e&&d.setStyle("text-align","left")}}g.prototype={exec:function(a){var c=a.getSelection(),b=a.config.enterMode;if(c){for(var h=c.createBookmarks(),d=c.getRanges(),e=this.cssClassName,g,f,k=a.config.useComputedState,k=void 0===k||k,m=d.length-1;0<=m;m--)for(g=d[m].createIterator(),g.enlargeBr=b!=CKEDITOR.ENTER_BR;f=g.getNextParagraph(b==CKEDITOR.ENTER_P?"p":"div");)if(!f.isReadOnly()){f.removeAttribute("align");
f.removeStyle("text-align");var l=e&&(f.$.className=CKEDITOR.tools.ltrim(f.$.className.replace(this.cssClassRegex,""))),p=this.state==CKEDITOR.TRISTATE_OFF&&(!k||n(f,!0)!=this.value);e?p?f.addClass(e):l||f.removeAttribute("class"):p&&f.setStyle("text-align",this.value)}a.focus();a.forceNextSelectionCheck();c.selectBookmarks(h)}},refresh:function(a,c){var b=c.block||c.blockLimit;this.setState("body"!=b.getName()&&n(b,this.editor.config.useComputedState)==this.value?CKEDITOR.TRISTATE_ON:CKEDITOR.TRISTATE_OFF)}};
CKEDITOR.plugins.add("justify",{lang:"af,ar,bg,bn,bs,ca,cs,cy,da,de,de-ch,el,en,en-au,en-ca,en-gb,eo,es,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,tt,ug,uk,vi,zh,zh-cn",icons:"justifyblock,justifycenter,justifyleft,justifyright",hidpi:!0,init:function(a){if(!a.blockless){var c=new g(a,"justifyleft","left"),b=new g(a,"justifycenter","center"),h=new g(a,"justifyright","right"),d=new g(a,"justifyblock","justify");
a.addCommand("justifyleft",c);a.addCommand("justifycenter",b);a.addCommand("justifyright",h);a.addCommand("justifyblock",d);a.ui.addButton&&(a.ui.addButton("JustifyLeft",{label:a.lang.justify.left,command:"justifyleft",toolbar:"align,10"}),a.ui.addButton("JustifyCenter",{label:a.lang.justify.center,command:"justifycenter",toolbar:"align,20"}),a.ui.addButton("JustifyRight",{label:a.lang.justify.right,command:"justifyright",toolbar:"align,30"}),a.ui.addButton("JustifyBlock",{label:a.lang.justify.block,
command:"justifyblock",toolbar:"align,40"}));a.on("dirChanged",l)}}})})();
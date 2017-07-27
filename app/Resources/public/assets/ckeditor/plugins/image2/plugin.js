﻿/*
 Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
*/
(function(){function D(a){function b(){this.deflated||(a.widgets.focused==this.widget&&(this.focused=!0),a.widgets.destroy(this.widget),this.deflated=!0)}function c(){var d=a.editable(),e=a.document;if(this.deflated)this.widget=a.widgets.initOn(this.element,"image",this.widget.data),this.widget.inline&&!(new CKEDITOR.dom.elementPath(this.widget.wrapper,d)).block&&(d=e.createElement(a.activeEnterMode==CKEDITOR.ENTER_P?"p":"div"),d.replace(this.widget.wrapper),this.widget.wrapper.move(d)),this.focused&&
(this.widget.focus(),delete this.focused),delete this.deflated;else{var b=this.widget,d=g,e=b.wrapper,c=b.data.align,b=b.data.hasCaption;if(d){for(var l=3;l--;)e.removeClass(d[l]);"center"==c?b&&e.addClass(d[1]):"none"!=c&&e.addClass(d[q[c]])}else"center"==c?(b?e.setStyle("text-align","center"):e.removeStyle("text-align"),e.removeStyle("float")):("none"==c?e.removeStyle("float"):e.setStyle("float",c),e.removeStyle("text-align"))}}var g=a.config.image2_alignClasses,f=a.config.image2_captionedClass;
return{allowedContent:E(a),requiredContent:"img[src,alt]",features:F(a),styleableElements:"img figure",contentTransformations:[["img[width]: sizeToAttribute"]],editables:{caption:{selector:"figcaption",allowedContent:"br em strong sub sup u s; a[!href,target]"}},parts:{image:"img",caption:"figcaption"},dialog:"image2",template:'\x3cimg alt\x3d"" src\x3d"" /\x3e',data:function(){var d=this.features;this.data.hasCaption&&!a.filter.checkFeature(d.caption)&&(this.data.hasCaption=!1);"none"==this.data.align||
a.filter.checkFeature(d.align)||(this.data.align="none");this.shiftState({widget:this,element:this.element,oldData:this.oldData,newData:this.data,deflate:b,inflate:c});this.data.link?this.parts.link||(this.parts.link=this.parts.image.getParent()):this.parts.link&&delete this.parts.link;this.parts.image.setAttributes({src:this.data.src,"data-cke-saved-src":this.data.src,alt:this.data.alt});if(this.oldData&&!this.oldData.hasCaption&&this.data.hasCaption)for(var e in this.data.classes)this.parts.image.removeClass(e);
if(a.filter.checkFeature(d.dimension)){d=this.data;d={width:d.width,height:d.height};e=this.parts.image;for(var g in d)d[g]?e.setAttribute(g,d[g]):e.removeAttribute(g)}this.oldData=CKEDITOR.tools.extend({},this.data)},init:function(){var d=CKEDITOR.plugins.image2,b=this.parts.image,c={hasCaption:!!this.parts.caption,src:b.getAttribute("src"),alt:b.getAttribute("alt")||"",width:b.getAttribute("width")||"",height:b.getAttribute("height")||"",lock:this.ready?d.checkHasNaturalRatio(b):!0},f=b.getAscendant("a");
f&&this.wrapper.contains(f)&&(this.parts.link=f);c.align||(b=c.hasCaption?this.element:b,g?(b.hasClass(g[0])?c.align="left":b.hasClass(g[2])&&(c.align="right"),c.align?b.removeClass(g[q[c.align]]):c.align="none"):(c.align=b.getStyle("float")||"none",b.removeStyle("float")));a.plugins.link&&this.parts.link&&(c.link=d.getLinkAttributesParser()(a,this.parts.link),(b=c.link.advanced)&&b.advCSSClasses&&(b.advCSSClasses=CKEDITOR.tools.trim(b.advCSSClasses.replace(/cke_\S+/,""))));this.wrapper[(c.hasCaption?
"remove":"add")+"Class"]("cke_image_nocaption");this.setData(c);a.filter.checkFeature(this.features.dimension)&&!0!==a.config.image2_disableResizer&&G(this);this.shiftState=d.stateShifter(this.editor);this.on("contextMenu",function(a){a.data.image=CKEDITOR.TRISTATE_OFF;if(this.parts.link||this.wrapper.getAscendant("a"))a.data.link=a.data.unlink=CKEDITOR.TRISTATE_OFF});this.on("dialog",function(a){a.data.widget=this},this)},addClass:function(a){m(this).addClass(a)},hasClass:function(a){return m(this).hasClass(a)},
removeClass:function(a){m(this).removeClass(a)},getClasses:function(){var a=new RegExp("^("+[].concat(f,g).join("|")+")$");return function(){var b=this.repository.parseElementClasses(m(this).getAttribute("class")),c;for(c in b)a.test(c)&&delete b[c];return b}}(),upcast:H(a),downcast:I(a),getLabel:function(){return this.editor.lang.widget.label.replace(/%1/,(this.data.alt||"")+" "+this.pathName)}}}function H(a){var b=n(a),c=a.config.image2_captionedClass;return function(a,f){var d={width:1,height:1},
e=a.name,h;if(!a.attributes["data-cke-realelement"]&&(b(a)?("div"==e&&(h=a.getFirst("figure"))&&(a.replaceWith(h),a=h),f.align="center",h=a.getFirst("img")||a.getFirst("a").getFirst("img")):"figure"==e&&a.hasClass(c)?h=a.getFirst("img")||a.getFirst("a").getFirst("img"):r(a)&&(h="a"==a.name?a.children[0]:a),h)){for(var C in d)(d=h.attributes[C])&&d.match(J)&&delete h.attributes[C];return a}}}function I(a){var b=a.config.image2_alignClasses;return function(a){var g="a"==a.name?a.getFirst():a,f=g.attributes,
d=this.data.align;if(!this.inline){var e=a.getFirst("span");e&&e.replaceWith(e.getFirst({img:1,a:1}))}d&&"none"!=d&&(e=CKEDITOR.tools.parseCssText(f.style||""),"center"==d&&"figure"==a.name?a=a.wrapWith(new CKEDITOR.htmlParser.element("div",b?{"class":b[1]}:{style:"text-align:center"})):d in{left:1,right:1}&&(b?g.addClass(b[q[d]]):e["float"]=d),b||CKEDITOR.tools.isEmpty(e)||(f.style=CKEDITOR.tools.writeCssText(e)));return a}}function n(a){var b=a.config.image2_captionedClass,c=a.config.image2_alignClasses,
g={figure:1,a:1,img:1};return function(f){if(!(f.name in{div:1,p:1}))return!1;var d=f.children;if(1!==d.length)return!1;d=d[0];if(!(d.name in g))return!1;if("p"==f.name){if(!r(d))return!1}else if("figure"==d.name){if(!d.hasClass(b))return!1}else if(a.enterMode==CKEDITOR.ENTER_P||!r(d))return!1;return(c?f.hasClass(c[1]):"center"==CKEDITOR.tools.parseCssText(f.attributes.style||"",!0)["text-align"])?!0:!1}}function r(a){return"img"==a.name?!0:"a"==a.name?1==a.children.length&&a.getFirst("img"):!1}function G(a){var b=
a.editor,c=b.editable(),g=b.document,f=a.resizer=g.createElement("span");f.addClass("cke_image_resizer");f.setAttribute("title",b.lang.image2.resizer);f.append(new CKEDITOR.dom.text("​",g));if(a.inline)a.wrapper.append(f);else{var d=a.parts.link||a.parts.image,e=d.getParent(),h=g.createElement("span");h.addClass("cke_image_resizer_wrapper");h.append(d);h.append(f);a.element.append(h,!0);e.is("span")&&e.remove()}f.on("mousedown",function(d){function l(a,b,d){var l=CKEDITOR.document,c=[];g.equals(l)||
c.push(l.on(a,b));c.push(g.on(a,b));if(d)for(a=c.length;a--;)d.push(c.pop())}function e(){t=m+A*x;u=Math.round(t/v)}function w(){u=q-p;t=Math.round(u*v)}var h=a.parts.image,A="right"==a.data.align?-1:1,k=d.data.$.screenX,K=d.data.$.screenY,m=h.$.clientWidth,q=h.$.clientHeight,v=m/q,n=[],r="cke_image_s"+(~A?"e":"w"),B,t,u,z,x,p,y;b.fire("saveSnapshot");l("mousemove",function(a){B=a.data.$;x=B.screenX-k;p=K-B.screenY;y=Math.abs(x/p);1==A?0>=x?0>=p?e():y>=v?e():w():0>=p?y>=v?w():e():w():0>=x?0>=p?y>=
v?w():e():w():0>=p?e():y>=v?e():w();15<=t&&15<=u?(h.setAttributes({width:t,height:u}),z=!0):z=!1},n);l("mouseup",function(){for(var d;d=n.pop();)d.removeListener();c.removeClass(r);f.removeClass("cke_image_resizing");z&&(a.setData({width:t,height:u}),b.fire("saveSnapshot"));z=!1},n);c.addClass(r);f.addClass("cke_image_resizing")});a.on("data",function(){f["right"==a.data.align?"addClass":"removeClass"]("cke_image_resizer_left")})}function L(a){var b=[],c;return function(g){var f=a.getCommand("justify"+
g);if(f){b.push(function(){f.refresh(a,a.elementPath())});if(g in{right:1,left:1,center:1})f.on("exec",function(d){var c=k(a);if(c){c.setData("align",g);for(c=b.length;c--;)b[c]();d.cancel()}});f.on("refresh",function(b){var f=k(a),h={right:1,left:1,center:1};f&&(void 0===c&&(c=a.filter.checkFeature(a.widgets.registered.image.features.align)),c?this.setState(f.data.align==g?CKEDITOR.TRISTATE_ON:g in h?CKEDITOR.TRISTATE_OFF:CKEDITOR.TRISTATE_DISABLED):this.setState(CKEDITOR.TRISTATE_DISABLED),b.cancel())})}}}
function M(a){a.plugins.link&&(CKEDITOR.on("dialogDefinition",function(b){b=b.data;if("link"==b.name){b=b.definition;var c=b.onShow,g=b.onOk;b.onShow=function(){var b=k(a),d=this.getContentElement("info","linkDisplayText").getElement().getParent().getParent();b&&(b.inline?!b.wrapper.getAscendant("a"):1)?(this.setupContent(b.data.link||{}),d.hide()):(d.show(),c.apply(this,arguments))};b.onOk=function(){var b=k(a);if(b&&(b.inline?!b.wrapper.getAscendant("a"):1)){var c={};this.commitContent(c);b.setData("link",
c)}else g.apply(this,arguments)}}}),a.getCommand("unlink").on("exec",function(b){var c=k(a);c&&c.parts.link&&(c.setData("link",null),this.refresh(a,a.elementPath()),b.cancel())}),a.getCommand("unlink").on("refresh",function(b){var c=k(a);c&&(this.setState(c.data.link||c.wrapper.getAscendant("a")?CKEDITOR.TRISTATE_OFF:CKEDITOR.TRISTATE_DISABLED),b.cancel())}))}function k(a){return(a=a.widgets.focused)&&"image"==a.name?a:null}function E(a){var b=a.config.image2_alignClasses;a={div:{match:n(a)},p:{match:n(a)},
img:{attributes:"!src,alt,width,height"},figure:{classes:"!"+a.config.image2_captionedClass},figcaption:!0};b?(a.div.classes=b[1],a.p.classes=a.div.classes,a.img.classes=b[0]+","+b[2],a.figure.classes+=","+a.img.classes):(a.div.styles="text-align",a.p.styles="text-align",a.img.styles="float",a.figure.styles="float,display");return a}function F(a){a=a.config.image2_alignClasses;return{dimension:{requiredContent:"img[width,height]"},align:{requiredContent:"img"+(a?"("+a[0]+")":"{float}")},caption:{requiredContent:"figcaption"}}}
function m(a){return a.data.hasCaption?a.element:a.parts.image}var N=new CKEDITOR.template('\x3cfigure class\x3d"{captionedClass}"\x3e\x3cimg alt\x3d"" src\x3d"" /\x3e\x3cfigcaption\x3e{captionPlaceholder}\x3c/figcaption\x3e\x3c/figure\x3e'),q={left:0,center:1,right:2},J=/^\s*(\d+\%)\s*$/i;CKEDITOR.plugins.add("image2",{lang:"af,ar,az,bg,bn,bs,ca,cs,cy,da,de,de-ch,el,en,en-au,en-ca,en-gb,eo,es,es-mx,et,eu,fa,fi,fo,fr,fr-ca,gl,gu,he,hi,hr,hu,id,is,it,ja,ka,km,ko,ku,lt,lv,mk,mn,ms,nb,nl,no,oc,pl,pt,pt-br,ro,ru,si,sk,sl,sq,sr,sr-latn,sv,th,tr,tt,ug,uk,vi,zh,zh-cn",
requires:"widget,dialog",icons:"image",hidpi:!0,onLoad:function(){CKEDITOR.addCss(".cke_image_nocaption{line-height:0}.cke_editable.cke_image_sw, .cke_editable.cke_image_sw *{cursor:sw-resize !important}.cke_editable.cke_image_se, .cke_editable.cke_image_se *{cursor:se-resize !important}.cke_image_resizer{display:none;position:absolute;width:10px;height:10px;bottom:-5px;right:-5px;background:#000;outline:1px solid #fff;line-height:0;cursor:se-resize;}.cke_image_resizer_wrapper{position:relative;display:inline-block;line-height:0;}.cke_image_resizer.cke_image_resizer_left{right:auto;left:-5px;cursor:sw-resize;}.cke_widget_wrapper:hover .cke_image_resizer,.cke_image_resizer.cke_image_resizing{display:block}.cke_widget_wrapper\x3ea{display:inline-block}")},
init:function(a){var b=a.config,c=a.lang.image2,g=D(a);b.filebrowserImage2BrowseUrl=b.filebrowserImageBrowseUrl;b.filebrowserImage2UploadUrl=b.filebrowserImageUploadUrl;g.pathName=c.pathName;g.editables.caption.pathName=c.pathNameCaption;a.widgets.add("image",g);a.ui.addButton&&a.ui.addButton("Image",{label:a.lang.common.image,command:"image",toolbar:"insert,10"});a.contextMenu&&(a.addMenuGroup("image",10),a.addMenuItem("image",{label:c.menu,command:"image",group:"image"}));CKEDITOR.dialog.add("image2",
this.path+"dialogs/image2.js")},afterInit:function(a){var b={left:1,right:1,center:1,block:1},c=L(a),g;for(g in b)c(g);M(a)}});CKEDITOR.plugins.image2={stateShifter:function(a){function b(a,b){var d={};f?d.attributes={"class":f[1]}:d.styles={"text-align":"center"};d=g.createElement(a.activeEnterMode==CKEDITOR.ENTER_P?"p":"div",d);c(d,b);b.move(d);return d}function c(b,d){if(d.getParent()){var c=a.createRange();c.moveToPosition(d,CKEDITOR.POSITION_BEFORE_START);d.remove();e.insertElementIntoRange(b,
c)}else b.replace(d)}var g=a.document,f=a.config.image2_alignClasses,d=a.config.image2_captionedClass,e=a.editable(),h=["hasCaption","align","link"],k={align:function(d,c,g){var e=d.element;d.changed.align?d.newData.hasCaption||("center"==g&&(d.deflate(),d.element=b(a,e)),d.changed.hasCaption||"center"!=c||"center"==g||(d.deflate(),c=e.findOne("a,img"),c.replace(e),d.element=c)):"center"==g&&d.changed.hasCaption&&!d.newData.hasCaption&&(d.deflate(),d.element=b(a,e));!f&&e.is("figure")&&("center"==
g?e.setStyle("display","inline-block"):e.removeStyle("display"))},hasCaption:function(b,e,f){b.changed.hasCaption&&(e=b.element.is({img:1,a:1})?b.element:b.element.findOne("a,img"),b.deflate(),f?(f=CKEDITOR.dom.element.createFromHtml(N.output({captionedClass:d,captionPlaceholder:a.lang.image2.captionPlaceholder}),g),c(f,b.element),e.replace(f.findOne("img")),b.element=f):(e.replace(b.element),b.element=e))},link:function(b,d,c){if(b.changed.link){var e=b.element.is("img")?b.element:b.element.findOne("img"),
f=b.element.is("a")?b.element:b.element.findOne("a"),h=b.element.is("a")&&!c||b.element.is("img")&&c,k;h&&b.deflate();c?(d||(k=g.createElement("a",{attributes:{href:b.newData.link.url}}),k.replace(e),e.move(k)),c=CKEDITOR.plugins.image2.getLinkAttributesGetter()(a,c),CKEDITOR.tools.isEmpty(c.set)||(k||f).setAttributes(c.set),c.removed.length&&(k||f).removeAttributes(c.removed)):(c=f.findOne("img"),c.replace(f),k=c);h&&(b.element=k)}}};return function(a){var b,c;a.changed={};for(c=0;c<h.length;c++)b=
h[c],a.changed[b]=a.oldData?a.oldData[b]!==a.newData[b]:!1;for(c=0;c<h.length;c++)b=h[c],k[b](a,a.oldData?a.oldData[b]:null,a.newData[b]);a.inflate()}},checkHasNaturalRatio:function(a){var b=a.$;a=this.getNatural(a);return Math.round(b.clientWidth/a.width*a.height)==b.clientHeight||Math.round(b.clientHeight/a.height*a.width)==b.clientWidth},getNatural:function(a){if(a.$.naturalWidth)a={width:a.$.naturalWidth,height:a.$.naturalHeight};else{var b=new Image;b.src=a.getAttribute("src");a={width:b.width,
height:b.height}}return a},getLinkAttributesGetter:function(){return CKEDITOR.plugins.link.getLinkAttributes},getLinkAttributesParser:function(){return CKEDITOR.plugins.link.parseLinkAttributes}}})();CKEDITOR.config.image2_captionedClass="image";
﻿if (typeof FCKConfig.ToolbarSets=='string'||FCKConfig.ToolbarSets instanceof (String)){FCKConfig.ToolbarSets=eval('('+FCKConfig.ToolbarSets+')');};FCKConfig.AdvancedFileManager=null;if (FCKConfig.AdvancedFileManager){FCKConfig.AdvancedFileManager=FCKConfig.AdvancedFileManager.toString().toLowerCase()=='true'?true:false;}else{FCKConfig.AdvancedFileManager=false;if ((FCKConfig.ImageBrowserURL&&FCKConfig.ImageBrowserURL.toString().indexOf('ajaxfilemanager')!=-1)||(FCKConfig.FlashBrowserURL&&FCKConfig.FlashBrowserURL.toString().indexOf('ajaxfilemanager')!=-1)||(FCKConfig.MP3BrowserURL&&FCKConfig.MP3BrowserURL.toString().indexOf('ajaxfilemanager')!=-1)||(FCKConfig.VideoBrowserURL&&FCKConfig.VideoBrowserURL.toString().indexOf('ajaxfilemanager')!=-1)||(FCKConfig.LinkBrowserURL&&FCKConfig.LinkBrowserURL.toString().indexOf('ajaxfilemanager')!=-1)||(FCKConfig.MediaBrowserURL&&FCKConfig.MediaBrowserURL.toString().indexOf('ajaxfilemanager')!=-1)){FCKConfig.AdvancedFileManager=true;}};if (FCKConfig.InDocument){FCKConfig.InDocument=FCKConfig.InDocument.toString().toLowerCase()=='true'?true:false;}else{FCKConfig.InDocument=false;};if (!FCKConfig.CreateDocumentWebDir){FCKConfig.CreateDocumentWebDir='';};if (!FCKConfig.CreateDocumentDir){FCKConfig.CreateDocumentDir='';};if (!FCKConfig.BaseHref||FCKConfig.BaseHref.toString().length==0){if (FCKConfig.BaseHref.toString().length==0){FCKConfig.BaseHref=FCKConfig.CreateDocumentWebDir;}};if (!FCKConfig.BaseHref){if (typeof (FCKConfig.BaseHref)!='string'){FCKConfig.BaseHref=FCKConfig.CreateDocumentWebDir;}};FCKConfig.BaseHref=FCKConfig.BaseHref.toString();if (FCKConfig.BaseHref.length>0){if (FCKConfig.BaseHref.substr(FCKConfig.BaseHref.length-1)!='/'){FCKConfig.BaseHref=FCKConfig.BaseHref+'/';}};if (!FCKConfig.ImagesIcon){FCKConfig.ImagesIcon=FCKConfig.PluginsPath+'customizations/images/images_icon.gif';};FCK.Plugins.IsLoaded=function(A){if (A){for (var i=0;i<FCKConfig.Plugins.Items.length;i++){if (FCKConfig.Plugins.Items[i][0]==A){return true;}}};return false;};FCKToolbarButton.prototype.ClickFrame=function(){var A=this._ToolbarButton||this;return FCK.ToolbarSet.CurrentInstance.Commands.GetCommand(A.CommandName).ExecuteFrame();};FCKDialogCommand.prototype.ExecuteFrame=function(){return FCKDialog.OpenDialogFrame('FCKDialog_'+this.Name,this.Title,this.Url,this.Width,this.Height,this.CustomValue,this.Resizable);};var FCKDialog=(function(){var A;var B;var C;var D=window.parent;while (D.parent&&D.parent!=D){try{if (D.parent.document.domain!=document.domain) break;if (D.parent.document.getElementsByTagName('frameset').length>0) break;}catch (e){break;};D=D.parent;};var E=D.document;var F=function(){if (!B) B=FCKConfig.FloatingPanelsZIndex+999;return++B;};var G=function(){if (!C) return;var a=FCKTools.IsStrictMode(E)?E.documentElement:E.body;FCKDomTools.SetElementStyles(C,{'width':Math.max(a.scrollWidth,a.clientWidth,E.scrollWidth||0)-1+'px','height':Math.max(a.scrollHeight,a.clientHeight,E.scrollHeight||0)-1+'px'});};return {OpenDialog:function(b,c,d,e,f,g,h){if (!A) this.DisplayMainCover();var H={Title:c,Page:d,Editor:window,CustomValue:g,TopWindow:D};FCK.ToolbarSet.CurrentInstance.Selection.Save(true);var I=FCKTools.GetViewPaneSize(D);var J={ 'X':0,'Y':0 };var K=FCKBrowserInfo.IsIE&&(!FCKBrowserInfo.IsIE7||!FCKTools.IsStrictMode(D.document));if (K) J=FCKTools.GetScrollPosition(D);var L=Math.max(J.Y+(I.Height-f-20)/2,0);var M=Math.max(J.X+(I.Width-e-20)/2,0);var N=E.createElement('iframe');FCKTools.ResetStyles(N);N.src=FCKConfig.BasePath+'fckdialog.html';N.frameBorder=0;N.allowTransparency=true;FCKDomTools.SetElementStyles(N,{'position':(K)?'absolute':'fixed','top':L+'px','left':M+'px','width':e+'px','height':f+'px','zIndex':F()});N._DialogArguments=H;E.body.appendChild(N);N._ParentDialog=A;A=N;},OpenDialogFrame:function(i,j,k,l,m,n,o){var H={Title:j,Page:k,Editor:window,CustomValue:n,TopWindow:D};FCK.ToolbarSet.CurrentInstance.Selection.Save(true);var I=FCKTools.GetViewPaneSize(D);var J={ 'X':0,'Y':0 };var K=FCKBrowserInfo.IsIE&&(!FCKBrowserInfo.IsIE7||!FCKTools.IsStrictMode(D.document));if (K) J=FCKTools.GetScrollPosition(D);var L=Math.max(J.Y+(I.Height-m-20)/2,0);var M=Math.max(J.X+(I.Width-l-20)/2,0);var N=E.createElement('iframe');N.src=FCKConfig.BasePath+'fckdialogframe.html';N.frameBorder=0;N.allowTransparency=true;FCKDomTools.SetElementStyles(N,{'position':(K)?'absolute':'fixed','top':L+'px','left':M+'px','width':l+'px','height':m+'px','zIndex':F()});N._DialogArguments=H;return H;},OnDialogClose:function(p){var N=p.frameElement;FCKDomTools.RemoveNode(N);if (N._ParentDialog){A=N._ParentDialog;try{N._ParentDialog.contentWindow.SetEnabled(true);}catch (ex) { }}else{if (!FCKBrowserInfo.IsIE) FCK.Focus();this.HideMainCover();setTimeout(function(){ A=null;},0);FCK.ToolbarSet.CurrentInstance.Selection.Release();}},DisplayMainCover:function(){C=E.createElement('div');FCKTools.ResetStyles(C);FCKDomTools.SetElementStyles(C,{'position':'absolute','zIndex':F(),'top':'0px','left':'0px','backgroundColor':FCKConfig.BackgroundBlockerColor});FCKDomTools.SetOpacity(C,FCKConfig.BackgroundBlockerOpacity);if (FCKBrowserInfo.IsIE&&!FCKBrowserInfo.IsIE7){var X=E.createElement('iframe');FCKTools.ResetStyles(X);X.hideFocus=true;X.frameBorder=0;X.src=FCKTools.GetVoidUrl();FCKDomTools.SetElementStyles(X,{'width':'100%','height':'100%','position':'absolute','left':'0px','top':'0px','filter':'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'});C.appendChild(X);};FCKTools.AddEventListener(D,'resize',G);G();E.body.appendChild(C);FCKFocusManager.Lock();var Y=FCK.ToolbarSet.CurrentInstance.GetInstanceObject('frameElement');Y._fck_originalTabIndex=Y.tabIndex;Y.tabIndex=-1;},HideMainCover:function(){FCKDomTools.RemoveNode(C);FCKFocusManager.Unlock();var Y=FCK.ToolbarSet.CurrentInstance.GetInstanceObject('frameElement');Y.tabIndex=Y._fck_originalTabIndex;FCKDomTools.ClearElementJSProperty(Y,'_fck_originalTabIndex');},GetCover:function(){return C;}};})();FCK.BlockCopyPasteKeystrokes=function(){var A=[];for (var i=0;i<FCKConfig.Keystrokes.length;i++){switch (FCKConfig.Keystrokes[i][0]){case CTRL+67:case CTRL+86:case CTRL+88:break;default:A.push(FCKConfig.Keystrokes[i]);break;}};FCKConfig.Keystrokes=A;};if (FCKConfig.BlockCopyPaste){FCK.BlockCopyPasteKeystrokes();};FCK.GetNamedCommandState=function(A){if (FCKConfig.BlockCopyPaste){switch (A){case 'Cut':case 'Copy':case 'Paste':case 'PasteText':case 'PasteWord':return FCK_TRISTATE_DISABLED;break;default:break;}};try{if (FCKBrowserInfo.IsSafari&&FCK.EditorWindow&&A.IEquals('Paste')) return FCK_TRISTATE_OFF;if (!FCK.EditorDocument.queryCommandEnabled(A)) return FCK_TRISTATE_DISABLED;else{return FCK.EditorDocument.queryCommandState(A)?FCK_TRISTATE_ON:FCK_TRISTATE_OFF;}}catch (e){return FCK_TRISTATE_OFF;}};FCKToolbarItems.GetItem=function(A){var B=FCKToolbarItems.LoadedItems[A];if (B) return B;switch (A){case 'Source':B=new FCKToolbarButton('Source',FCKLang.Source,null,null,true,true,1);break;case 'DocProps':B=new FCKToolbarButton('DocProps',FCKLang.DocProps,null,null,null,null,2);break;case 'Save':B=new FCKToolbarButton('Save',FCKLang.Save,null,null,true,null,3);break;case 'NewPage':B=new FCKToolbarButton('NewPage',FCKLang.NewPage,null,null,true,null,4);break;case 'Preview':B=new FCKToolbarButton('Preview',FCKLang.Preview,null,null,true,null,5);break;case 'Templates':B=new FCKToolbarButton('Templates',FCKLang.Templates,null,null,null,null,6);break;case 'About':B=new FCKToolbarButton('About',FCKLang.About,null,null,true,null,47);break;case 'Cut':B=new FCKToolbarButton('Cut',FCKLang.Cut,null,null,false,true,7);break;case 'Copy':B=new FCKToolbarButton('Copy',FCKLang.Copy,null,null,false,true,8);break;case 'Paste':B=new FCKToolbarButton('Paste',FCKLang.Paste,null,null,false,true,9);break;case 'PasteText':B=new FCKToolbarButton('PasteText',FCKLang.PasteText,null,null,false,true,10);break;case 'PasteWord':B=new FCKToolbarButton('PasteWord',FCKLang.PasteWord,null,null,false,true,11);break;case 'Print':B=new FCKToolbarButton('Print',FCKLang.Print,null,null,false,true,12);break;case 'SpellCheck':B=new FCKToolbarButton('SpellCheck',FCKLang.SpellCheck,null,null,null,null,13);break;case 'Undo':B=new FCKToolbarButton('Undo',FCKLang.Undo,null,null,false,true,14);break;case 'Redo':B=new FCKToolbarButton('Redo',FCKLang.Redo,null,null,false,true,15);break;case 'SelectAll':B=new FCKToolbarButton('SelectAll',FCKLang.SelectAll,null,null,true,null,18);break;case 'RemoveFormat':B=new FCKToolbarButton('RemoveFormat',FCKLang.RemoveFormat,null,null,false,true,19);break;case 'FitWindow':B=new FCKToolbarButton('FitWindow',FCKLang.FitWindow,null,null,true,true,66);break;case 'Bold':B=new FCKToolbarButton('Bold',FCKLang.Bold,null,null,false,true,20);break;case 'Italic':B=new FCKToolbarButton('Italic',FCKLang.Italic,null,null,false,true,21);break;case 'Underline':B=new FCKToolbarButton('Underline',FCKLang.Underline,null,null,false,true,22);break;case 'StrikeThrough':B=new FCKToolbarButton('StrikeThrough',FCKLang.StrikeThrough,null,null,false,true,23);break;case 'Subscript':B=new FCKToolbarButton('Subscript',FCKLang.Subscript,null,null,false,true,24);break;case 'Superscript':B=new FCKToolbarButton('Superscript',FCKLang.Superscript,null,null,false,true,25);break;case 'OrderedList':B=new FCKToolbarButton('InsertOrderedList',FCKLang.NumberedListLbl,FCKLang.NumberedList,null,false,true,26);break;case 'UnorderedList':B=new FCKToolbarButton('InsertUnorderedList',FCKLang.BulletedListLbl,FCKLang.BulletedList,null,false,true,27);break;case 'Outdent':B=new FCKToolbarButton('Outdent',FCKLang.DecreaseIndent,null,null,false,true,28);break;case 'Indent':B=new FCKToolbarButton('Indent',FCKLang.IncreaseIndent,null,null,false,true,29);break;case 'Blockquote':B=new FCKToolbarButton('Blockquote',FCKLang.Blockquote,null,null,false,true,73);break;case 'CreateDiv':B=new FCKToolbarButton('CreateDiv',FCKLang.CreateDiv,null,null,false,true,74);break;case 'Link':B=new FCKToolbarButton('Link',FCKLang.InsertLinkLbl,FCKLang.InsertLink,null,false,true,34);break;case 'Unlink':B=new FCKToolbarButton('Unlink',FCKLang.RemoveLink,null,null,false,true,35);break;case 'Anchor':B=new FCKToolbarButton('Anchor',FCKLang.Anchor,null,null,null,null,36);break;case 'Image':B=new FCKToolbarButton('Image',FCKLang.InsertImageLbl,FCKLang.InsertImage,null,false,true,FCKConfig.ImagesIcon);break;case 'Flash':B=new FCKToolbarButton('Flash',FCKLang.InsertFlashLbl,FCKLang.InsertFlash,null,false,true,38);break;case 'Table':B=new FCKToolbarButton('Table',FCKLang.InsertTableLbl,FCKLang.InsertTable,null,false,true,39);break;case 'SpecialChar':B=new FCKToolbarButton('SpecialChar',FCKLang.InsertSpecialCharLbl,FCKLang.InsertSpecialChar,null,false,true,42);break;case 'Smiley':B=new FCKToolbarButton('Smiley',FCKLang.InsertSmileyLbl,FCKLang.InsertSmiley,null,false,true,41);break;case 'PageBreak':B=new FCKToolbarButton('PageBreak',FCKLang.PageBreakLbl,FCKLang.PageBreak,null,false,true,43);break;case 'Rule':B=new FCKToolbarButton('Rule',FCKLang.InsertLineLbl,FCKLang.InsertLine,null,false,true,40);break;case 'JustifyLeft':B=new FCKToolbarButton('JustifyLeft',FCKLang.LeftJustify,null,null,false,true,30);break;case 'JustifyCenter':B=new FCKToolbarButton('JustifyCenter',FCKLang.CenterJustify,null,null,false,true,31);break;case 'JustifyRight':B=new FCKToolbarButton('JustifyRight',FCKLang.RightJustify,null,null,false,true,32);break;case 'JustifyFull':B=new FCKToolbarButton('JustifyFull',FCKLang.BlockJustify,null,null,false,true,33);break;case 'Style':B=new FCKToolbarStyleCombo();break;case 'FontName':B=new FCKToolbarFontsCombo();break;case 'FontSize':B=new FCKToolbarFontSizeCombo();break;case 'FontFormat':B=new FCKToolbarFontFormatCombo();break;case 'TextColor':B=new FCKToolbarPanelButton('TextColor',FCKLang.TextColor,null,null,45);break;case 'BGColor':B=new FCKToolbarPanelButton('BGColor',FCKLang.BGColor,null,null,46);break;case 'Find':B=new FCKToolbarButton('Find',FCKLang.Find,null,null,null,null,16);break;case 'Replace':B=new FCKToolbarButton('Replace',FCKLang.Replace,null,null,null,null,17);break;case 'Form':B=new FCKToolbarButton('Form',FCKLang.Form,null,null,null,null,48);break;case 'Checkbox':B=new FCKToolbarButton('Checkbox',FCKLang.Checkbox,null,null,null,null,49);break;case 'Radio':B=new FCKToolbarButton('Radio',FCKLang.RadioButton,null,null,null,null,50);break;case 'TextField':B=new FCKToolbarButton('TextField',FCKLang.TextField,null,null,null,null,51);break;case 'Textarea':B=new FCKToolbarButton('Textarea',FCKLang.Textarea,null,null,null,null,52);break;case 'HiddenField':B=new FCKToolbarButton('HiddenField',FCKLang.HiddenField,null,null,null,null,56);break;case 'Button':B=new FCKToolbarButton('Button',FCKLang.Button,null,null,null,null,54);break;case 'Select':B=new FCKToolbarButton('Select',FCKLang.SelectionField,null,null,null,null,53);break;case 'ImageButton':B=new FCKToolbarButton('ImageButton',FCKLang.ImageButton,null,null,null,null,55);break;case 'ShowBlocks':B=new FCKToolbarButton('ShowBlocks',FCKLang.ShowBlocks,null,null,null,true,72);break;default:return null;};FCKToolbarItems.LoadedItems[A]=B;return B;};FCKSaveCommand.prototype.Execute=function(){var A=FCK.GetParentForm();if (typeof(A.onsubmit)=='function'){var B=A.onsubmit();if (B!=null&&B===false) return;};for (var i=0;i<A.elements.length;i++){if (A.elements[i].type=='submit'){if (A.elements[i].getAttribute('class')||A.elements[i].getAttribute('name')=='intro_cmdUpdate'){try{A.elements[i].click();} catch (ex) {};return;}}};if (typeof(A.submit)=='function') A.submit();else A.submit.click();};FCKFitWindow.prototype.Execute=function(){var A=window.frameElement;var B=A.style;var C=parent;var D=C.document.documentElement;var E=C.document.body;var F=E.style;var G;var H,I;if (FCK.EditMode==FCK_EDITMODE_WYSIWYG){H=new FCKDomRange(FCK.EditorWindow);H.MoveToSelection();I=FCKTools.GetScrollPosition(FCK.EditorWindow);}else{var J=FCK.EditingArea.Textarea;H=!FCKBrowserInfo.IsIE&&[J.selectionStart,J.selectionEnd];I=[J.scrollLeft,J.scrollTop];};if (!this.IsMaximized){if(FCKBrowserInfo.IsIE) C.attachEvent('onresize',FCKFitWindow_Resize);else C.addEventListener('resize',FCKFitWindow_Resize,true);this._ScrollPos=FCKTools.GetScrollPosition(C);G=A;while((G=G.parentNode)){if (G.nodeType==1){G._fckSavedStyles=FCKTools.SaveStyles(G);G.style.zIndex=FCKConfig.FloatingPanelsZIndex-1;}};if (FCKBrowserInfo.IsIE){this.documentElementOverflow=D.style.overflow;D.style.overflow='hidden';F.overflow='hidden';}else{F.overflow='hidden';F.width='0px';F.height='0px';};this._EditorFrameStyles=FCKTools.SaveStyles(A);var K=FCKTools.GetViewPaneSize(C);B.position="absolute";A.offsetLeft;B.zIndex=FCKConfig.FloatingPanelsZIndex-1;B.left="0px";B.top="0px";B.width=K.Width+"px";B.height=K.Height+"px";if (!FCKBrowserInfo.IsIE){B.borderRight=B.borderBottom="9999px solid white";B.backgroundColor="white";};C.scrollTo(0,0);var L=FCKTools.GetWindowPosition(C,A);if (L.x!=0) B.left=(-1*L.x)+"px";if (L.y!=0) B.top=(-1*L.y)+"px";var M=FCKURLParams['Toolbar']+'Maximized';if (FCKConfig.ToolbarSets[M]){var N=FCKeditorAPI.GetInstance(FCK.Name);if (M!=N.ToolbarSet.Name){N.ToolbarSet.Load(M);}};this.IsMaximized=true;}else{var M=FCKURLParams['Toolbar'];if (FCKConfig.ToolbarSets[M]){var N=FCKeditorAPI.GetInstance(FCK.Name);if (M!=N.ToolbarSet.Name){N.ToolbarSet.Load(M);}};if(FCKBrowserInfo.IsIE) C.detachEvent("onresize",FCKFitWindow_Resize);else C.removeEventListener("resize",FCKFitWindow_Resize,true);G=A;while((G=G.parentNode)){if (G._fckSavedStyles){FCKTools.RestoreStyles(G,G._fckSavedStyles);G._fckSavedStyles=null;}};if (FCKBrowserInfo.IsIE) D.style.overflow=this.documentElementOverflow;FCKTools.RestoreStyles(A,this._EditorFrameStyles);C.scrollTo(this._ScrollPos.X,this._ScrollPos.Y);this.IsMaximized=false;};FCKToolbarItems.GetItem('FitWindow').RefreshState();if (FCK.EditMode==FCK_EDITMODE_WYSIWYG) FCK.EditingArea.MakeEditable();FCK.Focus();if (FCK.EditMode==FCK_EDITMODE_WYSIWYG){H.Select();FCK.EditorWindow.scrollTo(I.X,I.Y);}else{if (!FCKBrowserInfo.IsIE){J.selectionStart=H[0];J.selectionEnd=H[1];};J.scrollLeft=I[0];J.scrollTop=I[1];}};var FCKImageCommand=function(A){this.Name=A;this.ImageProperties=new FCKDialogCommand('Image',FCKLang.DlgImgTitle,'dialog/fck_image.html',600,450);this.ImageManager=null;if (FCK.Plugins.IsLoaded('ImageManager')){this.ImageManager=new FCKImageManager('ImageManager');}};FCKImageCommand.prototype.Execute=function(){if (FCKConfig.AdvancedFileManager){this.ImageProperties.Execute();}else{if (!this.ImageManager){this.ImageProperties.Execute();}else{var A=FCK.Selection.GetSelectedElement();if (A){if (FCK.IsRealImage(A)){this.ImageProperties.Execute();}else{this.ImageManager.Execute();}}else{this.ImageManager.Execute();}}}};FCKImageCommand.prototype.GetState=function(){return FCK_TRISTATE_OFF;};FCKCommands.GetCommand=function(A){var B=FCKCommands.LoadedCommands[A];if (B) return B;switch (A){case 'Bold':case 'Italic':case 'Underline':case 'StrikeThrough':case 'Subscript':case 'Superscript':B=new FCKCoreStyleCommand(A);break;case 'RemoveFormat':B=new FCKRemoveFormatCommand();break;case 'DocProps':B=new FCKDialogCommand('DocProps',FCKLang.DocProps,'dialog/fck_docprops.html',540,380,FCKCommands.GetFullPageState);break;case 'Templates':B=new FCKDialogCommand('Templates',FCKLang.DlgTemplatesTitle,'dialog/fck_template.html',380,450);break;case 'Link':B=new FCKDialogCommand('Link',FCKLang.DlgLnkWindowTitle,'dialog/fck_link.html',600,300);break;case 'Unlink':B=new FCKUnlinkCommand();break;case 'VisitLink':B=new FCKVisitLinkCommand();break;case 'Anchor':B=new FCKDialogCommand('Anchor',FCKLang.DlgAnchorTitle,'dialog/fck_anchor.html',420,180);break;case 'AnchorDelete':B=new FCKAnchorDeleteCommand();break;case 'BulletedList':B=new FCKDialogCommand('BulletedList',FCKLang.BulletedListProp,'dialog/fck_listprop.html?UL',420,180);break;case 'NumberedList':B=new FCKDialogCommand('NumberedList',FCKLang.NumberedListProp,'dialog/fck_listprop.html?OL',420,180);break;case 'About':B=new FCKDialogCommand('About',FCKLang.About,'dialog/fck_about.html',500,380,function(){ return FCK_TRISTATE_OFF;});break;case 'Find':B=new FCKDialogCommand('Find',FCKLang.DlgFindAndReplaceTitle,'dialog/fck_replace.html',450,250,null,null,'Find');break;case 'Replace':B=new FCKDialogCommand('Replace',FCKLang.DlgFindAndReplaceTitle,'dialog/fck_replace.html',450,250,null,null,'Replace');break;case 'Image':B=new FCKImageCommand('Image');break;case 'Flash':B=new FCKDialogCommand('Flash',FCKLang.DlgFlashTitle,'dialog/fck_flash.html',600,450);break;case 'SpecialChar':B=new FCKDialogCommand('SpecialChar',FCKLang.DlgSpecialCharTitle,'dialog/fck_specialchar.html',540,450);break;case 'Smiley':B=new FCKDialogCommand('Smiley',FCKLang.DlgSmileyTitle,'dialog/fck_smiley.html',FCKConfig.SmileyWindowWidth,FCKConfig.SmileyWindowHeight);break;case 'Table':B=new FCKDialogCommand('Table',FCKLang.DlgTableTitle,'dialog/fck_table.html',600,300);break;case 'TableProp':B=new FCKDialogCommand('Table',FCKLang.DlgTableTitle,'dialog/fck_table.html?Parent',600,300);break;case 'TableCellProp':B=new FCKDialogCommand('TableCell',FCKLang.DlgCellTitle,'dialog/fck_tablecell.html',600,300);break;case 'Style':B=new FCKStyleCommand();break;case 'FontName':B=new FCKFontNameCommand();break;case 'FontSize':B=new FCKFontSizeCommand();break;case 'FontFormat':B=new FCKFormatBlockCommand();break;case 'Source':B=new FCKSourceCommand();break;case 'Preview':B=new FCKPreviewCommand();break;case 'Save':B=new FCKSaveCommand();break;case 'NewPage':B=new FCKNewPageCommand();break;case 'PageBreak':B=new FCKPageBreakCommand();break;case 'Rule':B=new FCKRuleCommand();break;case 'Nbsp':B=new FCKNbsp();break;case 'TextColor':B=new FCKTextColorCommand('ForeColor');break;case 'BGColor':B=new FCKTextColorCommand('BackColor');break;case 'Paste':B=new FCKPasteCommand();break;case 'PasteText':B=new FCKPastePlainTextCommand();break;case 'PasteWord':B=new FCKPasteWordCommand();break;case 'JustifyLeft':B=new FCKJustifyCommand('left');break;case 'JustifyCenter':B=new FCKJustifyCommand('center');break;case 'JustifyRight':B=new FCKJustifyCommand('right');break;case 'JustifyFull':B=new FCKJustifyCommand('justify');break;case 'Indent':B=new FCKIndentCommand('indent',FCKConfig.IndentLength);break;case 'Outdent':B=new FCKIndentCommand('outdent',FCKConfig.IndentLength*-1);break;case 'Blockquote':B=new FCKBlockQuoteCommand();break;case 'CreateDiv':B=new FCKDialogCommand('CreateDiv',FCKLang.CreateDiv,'dialog/fck_div.html',400,300,null,null,true);break;case 'EditDiv':B=new FCKDialogCommand('EditDiv',FCKLang.EditDiv,'dialog/fck_div.html',400,300,null,null,false);break;case 'DeleteDiv':B=new FCKDeleteDivCommand();break;case 'TableInsertRowAfter':B=new FCKTableCommand('TableInsertRowAfter');break;case 'TableInsertRowBefore':B=new FCKTableCommand('TableInsertRowBefore');break;case 'TableDeleteRows':B=new FCKTableCommand('TableDeleteRows');break;case 'TableInsertColumnAfter':B=new FCKTableCommand('TableInsertColumnAfter');break;case 'TableInsertColumnBefore':B=new FCKTableCommand('TableInsertColumnBefore');break;case 'TableDeleteColumns':B=new FCKTableCommand('TableDeleteColumns');break;case 'TableInsertCellAfter':B=new FCKTableCommand('TableInsertCellAfter');break;case 'TableInsertCellBefore':B=new FCKTableCommand('TableInsertCellBefore');break;case 'TableDeleteCells':B=new FCKTableCommand('TableDeleteCells');break;case 'TableMergeCells':B=new FCKTableCommand('TableMergeCells');break;case 'TableMergeRight':B=new FCKTableCommand('TableMergeRight');break;case 'TableMergeDown':B=new FCKTableCommand('TableMergeDown');break;case 'TableHorizontalSplitCell':B=new FCKTableCommand('TableHorizontalSplitCell');break;case 'TableVerticalSplitCell':B=new FCKTableCommand('TableVerticalSplitCell');break;case 'TableDelete':B=new FCKTableCommand('TableDelete');break;case 'Form':B=new FCKDialogCommand('Form',FCKLang.Form,'dialog/fck_form.html',380,210);break;case 'Checkbox':B=new FCKDialogCommand('Checkbox',FCKLang.Checkbox,'dialog/fck_checkbox.html',380,200);break;case 'Radio':B=new FCKDialogCommand('Radio',FCKLang.RadioButton,'dialog/fck_radiobutton.html',380,200);break;case 'TextField':B=new FCKDialogCommand('TextField',FCKLang.TextField,'dialog/fck_textfield.html',380,210);break;case 'Textarea':B=new FCKDialogCommand('Textarea',FCKLang.Textarea,'dialog/fck_textarea.html',380,210);break;case 'HiddenField':B=new FCKDialogCommand('HiddenField',FCKLang.HiddenField,'dialog/fck_hiddenfield.html',380,190);break;case 'Button':B=new FCKDialogCommand('Button',FCKLang.Button,'dialog/fck_button.html',380,210);break;case 'Select':B=new FCKDialogCommand('Select',FCKLang.SelectionField,'dialog/fck_select.html',450,380);break;case 'ImageButton':B=new FCKDialogCommand('ImageButton',FCKLang.ImageButton,'dialog/fck_image.html?ImageButton',600,450);break;case 'SpellCheck':B=new FCKSpellCheckCommand();break;case 'FitWindow':B=new FCKFitWindow();break;case 'Undo':B=new FCKUndoCommand();break;case 'Redo':B=new FCKRedoCommand();break;case 'Copy':B=new FCKCutCopyCommand(false);break;case 'Cut':B=new FCKCutCopyCommand(true);break;case 'SelectAll':B=new FCKSelectAllCommand();break;case 'InsertOrderedList':B=new FCKListCommand('insertorderedlist','ol');break;case 'InsertUnorderedList':B=new FCKListCommand('insertunorderedlist','ul');break;case 'ShowBlocks':B=new FCKShowBlockCommand('ShowBlocks',FCKConfig.StartupShowBlocks?FCK_TRISTATE_ON:FCK_TRISTATE_OFF);break;case 'Undefined':B=new FCKUndefinedCommand();break;case 'Scayt':B=FCKScayt.CreateCommand();break;case 'ScaytContext':B=FCKScayt.CreateContextCommand();break;default:if (FCKRegexLib.NamedCommands.test(A)) B=new FCKNamedCommand(A);else{alert(FCKLang.UnknownCommand.replace(/%1/g,A));return null;}};FCKCommands.LoadedCommands[A]=B;return B;};FCKLanguageManager.TranslatePage=function(A){this.TranslateElements(A,'INPUT','value');this.TranslateElements(A,'SPAN','innerHTML');this.TranslateElements(A,'LABEL','innerHTML');this.TranslateElements(A,'OPTION','innerHTML',true);this.TranslateElements(A,'LEGEND','innerHTML');this.TranslateElements(A,'BUTTON','innerHTML');};FCK.ResizeToFit=function(A,B,C,D){var E=[0,0];E[0]=A;E[1]=B;if (A<=C&&B<=D) return E;if (A>C){B=B*C/A;A=C;};if (B>D){A=A*D/B;B=D;};E[0]=parseInt (A,10);E[1]=parseInt (B,10);return E;};FCKDocumentProcessor_CreateFakeImage=function(A,B){var C=FCKTools.GetElementDocument(B).createElement('IMG');C.className=A;C.src=FCKConfig.BasePath+'images/spacer.gif';C.setAttribute('_fckfakelement','true',0);C.setAttribute('_fckrealelement',FCKTempBin.AddElement(B),0);if (A=='FCK__Video'){if (B.nodeName.IEquals('div')){for (var i=0;i<B.childNodes.length;i++){if (B.childNodes[i].nodeName.IEquals('div')){C.style.width=B.childNodes[i].style.width;C.style.height=B.childNodes[i].style.height;break;}}}else{var D=B.width;var E=B.height;if (D){C.style.width=D.toString().indexOf('%')!=-1?D:(D+'px');};if (E){C.style.height=E.toString().indexOf('%')!=-1?E:(E+'px');}}};return C;};FCKEmbedAndObjectProcessor.AddCustomHandler(function (A,B){if (!FCK.IsAudio(A)){return;};B.className='FCK__MP3';B.setAttribute('_fckmp3','true',0);});FCKDocumentProcessor.AppendNew().ProcessDocument=function (A){var B=A.getElementsByTagName('embed');var C;var i=B.length-1;while (i>=0&&(C=B[i--])){if (FCK.IsAudio(C)){var D=FCKDocumentProcessor_CreateFakeImage('FCK__MP3',C.cloneNode(true));D.setAttribute('_fckmp3','true',0);C.parentNode.insertBefore(D,C);C.parentNode.removeChild(C);}}};FCKEmbedAndObjectProcessor.AddCustomHandler(function (A,B){if (!FCK.IsVideo(A)){return;};B.className='FCK__Video';B.setAttribute('_fckvideo','true',0);});FCKDocumentProcessor.AppendNew().ProcessDocument=function (A){var B=A.getElementsByTagName('embed');var C;var i=B.length-1;while (i>=0&&(C=B[i--])){if (FCK.IsVideo(C)){var D=FCKDocumentProcessor_CreateFakeImage('FCK__Video',C.cloneNode(true));D.setAttribute('_fckvideo','true',0);C.parentNode.insertBefore(D,C);C.parentNode.removeChild(C);}};var E=A.getElementsByTagName('div');var F;var i=E.length-1;while (i>=0&&(F=E[i--])){if (FCK.IsVideo(F)){var D=FCKDocumentProcessor_CreateFakeImage('FCK__Video',F.cloneNode(true));D.setAttribute('_fckvideo','true',0);F.parentNode.insertBefore(D,F);F.parentNode.removeChild(F);}}};for (var i in FCK.ContextMenu.Listeners){var listener='';if (FCK.ContextMenu.Listeners[i].AddItems){listener=FCK.ContextMenu.Listeners[i].AddItems.toString();};if (listener.indexOf('IMG')!=-1&&listener.indexOf('_fckfakelement')!=-1){FCK.ContextMenu.Listeners[i].AddItems=function (A,B,C){return;};};if (listener.indexOf('IMG')!=-1&&listener.indexOf('_fckflash')!=-1){FCK.ContextMenu.Listeners[i].AddItems=function (A,B,C){return;};}};FCK.ContextMenu.RegisterListener({AddItems:function (A,B,C){if (FCK.IsRealImage(B)){A.AddSeparator();A.AddItem('Image',FCKLang.ImageProperties,FCKConfig.ImagesIcon);}} });FCK.ContextMenu.RegisterListener({AddItems:function (A,B,C){if (C=='IMG'&&B.getAttribute('_fckflash')&&!B.getAttribute('_fckmp3')&&!B.getAttribute('_fckvideo')){A.AddSeparator();A.AddItem('Flash',FCKLang.FlashProperties,38);}} });FCK.ContextMenu.RegisterListener({AddItems:function (A,B,C){var D='Import MP3';var E=FCKConfig.PluginsPath+'MP3/mp3.gif';if (FCKLang.DlgAudioTitle){D=FCKLang.DlgAudioTitle;E=FCKConfig.PluginsPath+'audio/audio.gif';}else if (FCKLang.DlgMP3Title){D=FCKLang.DlgMP3Title;};if (C=='IMG'&&B.getAttribute('_fckmp3')){if (FCK.Plugins.IsLoaded('audio')||FCK.Plugins.IsLoaded('MP3')){A.AddSeparator();A.AddItem('MP3',D,E);}}} });FCK.ContextMenu.RegisterListener({AddItems:function (A,B,C){if (C=='IMG'&&B.getAttribute('_fckvideo')){switch (FCK.GetVideoType(B)){case 'embedded_video':if (FCK.Plugins.IsLoaded('fckEmbedMovies')){A.AddSeparator();A.AddItem('EmbedMovies',FCKLang.DlgEmbedMoviesTitle,FCKConfig.PluginsPath+'fckEmbedMovies/embedmovies.gif');};break;case 'youtube':if (FCK.Plugins.IsLoaded('youtube')){A.AddSeparator();A.AddItem('YouTube',FCKLang.YouTubeTip,FCKConfig.PluginsPath+'youtube/youtube.gif');};break;case 'flv':if (FCK.Plugins.IsLoaded('flvPlayer')){A.AddSeparator();A.AddItem('flvPlayer',FCKLang.DlgFLVPlayerTitle,FCKConfig.PluginsPath+'flvPlayer/flvPlayer.gif');};break;default:break;}}} });FCK.RegisterDoubleClickHandler(function (A){if (FCK.IsRealImage(A)){var B=new FCKDialogCommand('Image',FCKLang.DlgImgTitle,'dialog/fck_image.html',600,450);B.Execute();}},'IMG');FCK.RegisterDoubleClickHandler(function (A){if (A.tagName=='IMG'&&A.getAttribute('_fckflash')&&!A.getAttribute('_fckmp3')&&!A.getAttribute('_fckvideo')){FCKCommands.GetCommand('Flash').Execute();}},'IMG');FCK.RegisterDoubleClickHandler(function (A){if (A.tagName=='IMG'&&A.getAttribute('_fckmp3')){if (FCK.Plugins.IsLoaded('audio')||FCK.Plugins.IsLoaded('MP3')){FCKCommands.GetCommand('MP3').Execute();}}},'IMG');FCK.RegisterDoubleClickHandler(function (A){if (A.tagName=='IMG'&&A.getAttribute('_fckvideo')){switch (FCK.GetVideoType(A)){case 'embedded_video':if (FCK.Plugins.IsLoaded('fckEmbedMovies')){FCKCommands.GetCommand('EmbedMovies').Execute();};break;case 'youtube':if (FCK.Plugins.IsLoaded('youtube')){FCKCommands.GetCommand('YouTube').Execute();};break;case 'flv':if (FCK.Plugins.IsLoaded('flvPlayer')){FCKCommands.GetCommand('flvPlayer').Execute();};break;default:break;}}},'IMG');FCK.IsRealImage=function (A){if (!A){return false;};return (A.nodeName.IEquals('img')&&!A.getAttribute('_fckfakelement')&&!A.getAttribute('_fckflash')&&!A.getAttribute('_fckmp3')&&!A.getAttribute('_fckvideo')&&!A.getAttribute('MapNumber')&&!(A.getAttribute('src')&&A.getAttribute('src').toString().indexOf('/cgi-bin/mimetex')>=0)&&!(A.getAttribute('src')&&A.getAttribute('src').toString().indexOf('/cgi-bin/mathtex')>=0))?true:false;};FCK.IsAudio=function (A){if (!A){return false;};if (A.nodeName.IEquals('embed')){if (!A.src){return false;};if (A.type=='application/x-shockwave-flash'||/\.swf($|#|\?|&)?/i.test(A.src)){if (/\.mp3/i.test(A.src)){return true;};var B=FCKDomTools.GetAttributeValue(A,'flashvars');B=B?B.toLowerCase():'';if (/\.mp3/i.test(B)){return true;}}};return false;};FCK.IsVideo=function (A){if (!A){return false;};if (A.nodeName.IEquals('embed')){if (!A.src){return false;};if (/\.(mpg|mpeg|mp4|avi|wmv|mov|asf)/i.test(A.src)){return true;};if (A.type=='application/x-shockwave-flash'||/\.swf($|#|\?|&)?/i.test(A.src)){if (/\.youtube\.com/i.test(A.src)){return true;};if (/\.flv/i.test(A.src)){return true;};var B=FCKDomTools.GetAttributeValue(A,'flashvars');B=B?B.toLowerCase():'';if (/\.flv/i.test(B)){return true;}}};if (A.nodeName.IEquals('div')){if (A.id){if (A.id.match(/^player[0-9]*-parent$/)){return true;}}};return false;};FCK.GetVideoType=function (A){var B=FCK.GetRealElement(A);if (!B){return false;};if (B.nodeName.IEquals('div')){if (B.id){if (B.id.match(/^player[0-9]*-parent$/)){return 'flv';}}};if (!B.src){return false;};if (/\.(mpg|mpeg|mp4|avi|wmv|mov|asf)/i.test(B.src)){return 'embedded_video';};if (/\.youtube\.com/i.test(B.src)){return 'youtube';};if (/\.flv/i.test(B.src)){return 'flv';};var C=FCKDomTools.GetAttributeValue(B,'flashvars');C=C?C.toLowerCase():'';if (/\.flv/i.test(C)){return 'flv';};return false;};var RELATIVE_URL='relative';var ABSOLUTE_URL='absolute';var SEMI_ABSOLUTE_URL='semi-absolute';FCK.RELATIVE_URL=RELATIVE_URL;FCK.ABSOLUTE_URL=ABSOLUTE_URL;FCK.SEMI_ABSOLUTE_URL=SEMI_ABSOLUTE_URL;var REPOSITORY_RELATIVE_URL='repository-relative';var DOCUMENT_RELATIVE_URL='document-relative';FCK.REPOSITORY_RELATIVE_URL=REPOSITORY_RELATIVE_URL;FCK.DOCUMENT_RELATIVE_URL=DOCUMENT_RELATIVE_URL;FCK.GetSelectedFlashUrl=function (A){if (FCKConfig.CreateDocumentDir=='document/'||/\.\.\/.*\/document\/$/.test(FCKConfig.CreateDocumentDir)){return FCK.GetUrl(A,SEMI_ABSOLUTE_URL);}else{return FCK.GetSelectedUrl(A);}};FCK.GetSelectedUrl=function (A){A=FCK.GetUrl (A,DOCUMENT_RELATIVE_URL);if (FCK.GetUrlType (A)!=RELATIVE_URL){A=FCK.GetUrl (A,SEMI_ABSOLUTE_URL);};return A;};FCK.GetUrl=function (A,B){if (!A){return A;};if (!B){return A;};A=A.toString().Trim();if (A.indexOf('./')==0){A=A.substr(2);};switch (B){case RELATIVE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:break;case ABSOLUTE_URL:case SEMI_ABSOLUTE_URL:A=FCK.ConvertUrl(A,RELATIVE_URL,FCKConfig.CreateDocumentWebDir);if (FCK.GetUrlType(A)==RELATIVE_URL){A=FCK.GetUrl(A,DOCUMENT_RELATIVE_URL);};break;default:break;};break;case REPOSITORY_RELATIVE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:if (A.indexOf(FCKConfig.CreateDocumentDir)==0){A=A.substr(FCKConfig.CreateDocumentDir.length);};break;case ABSOLUTE_URL:A=FCK.ConvertUrl(A,RELATIVE_URL,FCKConfig.CreateDocumentWebDir);break;case SEMI_ABSOLUTE_URL:A=FCK.ConvertUrl(A,RELATIVE_URL,FCKConfig.CreateDocumentWebDir);break;default:break;};break;case DOCUMENT_RELATIVE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:if (FCKConfig.CreateDocumentDir!='/'){A=FCKConfig.CreateDocumentDir+A;};break;case ABSOLUTE_URL:case SEMI_ABSOLUTE_URL:A=FCK.ConvertUrl(A,RELATIVE_URL,FCKConfig.CreateDocumentWebDir);if (FCK.GetUrlType(A)==RELATIVE_URL){A=FCK.GetUrl(A,DOCUMENT_RELATIVE_URL);};break;default:break;};break;case ABSOLUTE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:A=FCK.GetUrl(A,REPOSITORY_RELATIVE_URL);A=FCK.ConvertUrl(A,ABSOLUTE_URL,FCKConfig.CreateDocumentWebDir);break;case ABSOLUTE_URL:break;case SEMI_ABSOLUTE_URL:A=FCK.ConvertUrl(A,ABSOLUTE_URL,FCKConfig.CreateDocumentWebDir);break;default:break;};break;case SEMI_ABSOLUTE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:A=FCK.GetUrl(A,REPOSITORY_RELATIVE_URL);A=FCK.ConvertUrl(A,SEMI_ABSOLUTE_URL,FCKConfig.CreateDocumentWebDir);break;case ABSOLUTE_URL:A=FCK.ConvertUrl(A,SEMI_ABSOLUTE_URL,FCKConfig.CreateDocumentWebDir);break;case SEMI_ABSOLUTE_URL:break;default:break;};break;default:break;};return A;};FCK.ConvertUrl=function (A,B,C){if (!A){return '';};if (!B){return '';};A=A.toString().Trim();if (A.indexOf('./')==0){A=A.substr(2);};B=B.toString().Trim();if (!C){C='';};C=C.toString().Trim();if (C=='/'){C='';};switch (B){case RELATIVE_URL:switch (FCK.GetUrlType(A)){case ABSOLUTE_URL:C=FCK.ConvertUrl(C,ABSOLUTE_URL);if (A.indexOf(C)==0){A=A.substr(C.length);};break;case SEMI_ABSOLUTE_URL:C=FCK.ConvertUrl(C,SEMI_ABSOLUTE_URL);if (A.indexOf(C)==0){A=A.substr(C.length);};break;default:break;};break;case ABSOLUTE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:C=FCK.ConvertUrl(C,ABSOLUTE_URL);A=C+A;break;case SEMI_ABSOLUTE_URL:A=FCK.GetServerBase()+A.substr(1);break;default:break;};break;case SEMI_ABSOLUTE_URL:switch (FCK.GetUrlType(A)){case RELATIVE_URL:C=FCK.ConvertUrl(C,SEMI_ABSOLUTE_URL);A=C+A;break;case ABSOLUTE_URL:var D=FCK.GetServerBase();if (D==FCK.GetServerBase(A)){A='/'+A.substr(D.length);};break;default:break;};break;default:break;};return A;};FCK.GetUrlType=function (A){if (!A){return '';};A=A.toString().Trim();if (A.indexOf('/')==0){return SEMI_ABSOLUTE_URL;};if (A.match(/^([^:]+\:)?\/\//)){return ABSOLUTE_URL;};return RELATIVE_URL;};FCK.GetServerBase=function (A){if (!A){if (FCKConfig.CreateDocumentWebDir){A=FCKConfig.CreateDocumentWebDir;}else{A=location.href;}};A=A.toString().replace(/(https?:\/\/[^\/]*)\/.*/,'$1')+'/';return A;};FCKEvents.prototype.FireEvent=function(A,B){var C=true;var D=this._RegisteredEvents[A];if (D){for (var i=0;i<D.length;i++){try{C=(D[i](this.Owner,B)&&C);}catch(e){if (e.number==-2146823281){continue;};if (e.number!=-2146823277) throw e;}}};return C;};

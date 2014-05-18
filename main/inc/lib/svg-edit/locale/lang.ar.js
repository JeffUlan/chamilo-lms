/*globals svgEditor */
svgEditor.readLang({
	lang: "ar",
	dir : "ltr",
	common: {
		"ok": "حفظ",
		"cancel": "إلغاء",
		"key_backspace": "backspace", 
		"key_del": "delete", 
		"key_down": "down", 
		"key_up": "up", 
		"more_opts": "More Options",
		"url": "URL",
		"width": "Width",
		"height": "Height"
	},
	misc: {
		"powered_by": "Powered by"
	}, 
	ui: {
		"toggle_stroke_tools": "Show/hide more stroke tools",
		"palette_info": "انقر لتغيير لون التعبئة ، تحولا مزدوجا فوق لتغيير لون السكتة الدماغية",
		"zoom_level": "تغيير مستوى التكبير",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "تغير لون التعبئة",
		"stroke_color": "تغير لون السكتة الدماغية",
		"stroke_style": "تغيير نمط السكتة الدماغية اندفاعة",
		"stroke_width": "تغيير عرض السكتة الدماغية",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "تغيير زاوية الدوران",
		"blur": "Change gaussian blur value",
		"opacity": "تغيير مختارة غموض البند",
		"circle_cx": "دائرة التغيير لتنسيق cx",
		"circle_cy": "Change circle's cy coordinate",
		"circle_r": "التغيير في دائرة نصف قطرها",
		"ellipse_cx": "تغيير شكل البيضاوي cx تنسيق",
		"ellipse_cy": "تغيير شكل البيضاوي قبرصي تنسيق",
		"ellipse_rx": "تغيير شكل البيضاوي خ نصف قطرها",
		"ellipse_ry": "تغيير القطع الناقص في دائرة نصف قطرها ذ",
		"line_x1": "تغيير الخط لبدء تنسيق خ",
		"line_x2": "تغيير الخط لانهاء خ تنسيق",
		"line_y1": "تغيير الخط لبدء تنسيق ذ",
		"line_y2": "تغيير الخط لإنهاء تنسيق ذ",
		"rect_height": "تغيير المستطيل الارتفاع",
		"rect_width": "تغيير عرض المستطيل",
		"corner_radius": "تغيير مستطيل ركن الشعاع",
		"image_width": "تغيير صورة العرض",
		"image_height": "تغيير ارتفاع الصورة",
		"image_url": "تغيير العنوان",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "تغيير محتويات النص",
		"font_family": "تغيير الخط الأسرة",
		"font_size": "تغيير حجم الخط",
		"bold": "نص جريء",
		"italic": "مائل نص"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "تغير لون الخلفية / غموض",
		"connector_no_arrow": "No arrow",
		"fitToContent": "لائقا للمحتوى",
		"fit_to_all": "يصلح لجميع المحتويات",
		"fit_to_canvas": "يصلح لوحة زيتية على قماش",
		"fit_to_layer_content": "يصلح لطبقة المحتوى",
		"fit_to_sel": "يصلح لاختيار",
		"align_relative_to": "محاذاة النسبي ل ...",
		"relativeTo": "بالنسبة إلى:",
		"الصفحة": "الصفحة",
		"largest_object": "أكبر كائن",
		"selected_objects": "انتخب الأجسام",
		"smallest_object": "أصغر كائن",
		"new_doc": "صورة جديدة",
		"open_doc": "فتح الصورة",
		"export_img": "Export",
		"save_doc": "حفظ صورة",
		"import_doc": "Import SVG",
		"align_to_page": "Align Element to Page",
		"align_bottom": "محاذاة القاع",
		"align_center": "مركز محاذاة",
		"align_left": "محاذاة إلى اليسار",
		"align_middle": "محاذاة الأوسط",
		"align_right": "محاذاة إلى اليمين",
		"align_top": "محاذاة الأعلى",
		"mode_select": "اختر أداة",
		"mode_fhpath": "أداة قلم رصاص",
		"mode_line": "خط أداة",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "Free-Hand Rectangle",
		"mode_ellipse": "القطع الناقص",
		"mode_circle": "دائرة",
		"mode_fhellipse": "اليد الحرة البيضوي",
		"mode_path": "بولي أداة",
		"mode_shapelib": "Shape library",
		"mode_text": "النص أداة",
		"mode_image": "الصورة أداة",
		"mode_zoom": "أداة تكبير",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "التراجع",
		"redo": "إعادته",
		"tool_source": "عدل المصدر",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "مجموعة عناصر",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "فك تجميع عناصر",
		"docprops": "خصائص المستند",
		"imagelib": "Image Library",
		"move_bottom": "الانتقال إلى أسفل",
		"move_top": "الانتقال إلى أعلى",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "حفظ",
		"cut": "Cut",
		"copy": "Copy",
		"paste": "Paste",
		"paste_in_place": "Paste in Place",
		"delete": "Delete",
		"group": "Group",
		"move_front": "Bring to Front",
		"move_up": "Bring Forward",
		"move_down": "Send Backward",
		"move_back": "Send to Back"
	},
	layers: {
		"layer":"Layer",
		"layers": "Layers",
		"del": "حذف طبقة",
		"move_down": "تحرك لأسفل طبقة",
		"new": "طبقة جديدة",
		"rename": "تسمية الطبقة",
		"move_up": "تحرك لأعلى طبقة",
		"dupe": "Duplicate Layer",
		"merge_down": "Merge Down",
		"merge_all": "Merge All",
		"move_elems_to": "Move elements to:",
		"move_selected": "Move selected elements to a different layer"
	},
	config: {
		"image_props": "Image Properties",
		"doc_title": "Title",
		"doc_dims": "Canvas Dimensions",
		"included_images": "Included Images",
		"image_opt_embed": "Embed data (local files)",
		"image_opt_ref": "Use file reference",
		"editor_prefs": "Editor Preferences",
		"icon_size": "Icon size",
		"language": "Language",
		"background": "Editor Background",
		"editor_img_url": "Image URL",
		"editor_bg_note": "Note: Background will not be saved with image.",
		"icon_large": "Large",
		"icon_medium": "Medium",
		"icon_small": "Small",
		"icon_xlarge": "Extra Large",
		"select_predefined": "حدد سلفا:",
		"units_and_rulers": "Units & Rulers",
		"show_rulers": "Show rulers",
		"base_unit": "Base Unit:",
		"grid": "Grid",
		"snapping_onoff": "Snapping on/off",
		"snapping_stepsize": "Snapping Step-Size:",
		"grid_color": "Grid color"
	},
	shape_cats: {
		"basic": "Basic",
		"object": "Objects",
		"symbol": "Symbols",
		"arrow": "Arrows",
		"flowchart": "Flowchart",
		"animal": "Animals",
		"game": "Cards & Chess",
		"dialog_balloon": "Dialog balloons",
		"electronics": "Electronics",
		"math": "Mathematical",
		"music": "Music",
		"misc": "Miscellaneous",
		"raphael_1": "raphaeljs.com set 1",
		"raphael_2": "raphaeljs.com set 2"
	},
	imagelib: {
		"select_lib": "Select an image library",
		"show_list": "Show library list",
		"import_single": "Import single",
		"import_multi": "Import multiple",
		"open": "Open as new document"
	},
	notification: {
		"invalidAttrValGiven":"Invalid value given",
		"noContentToFitTo":"No content to fit to",
		"dupeLayerName":"There is already a layer named that!",
		"enterUniqueLayerName":"Please enter a unique layer name",
		"enterNewLayerName":"Please enter the new layer name",
		"layerHasThatName":"Layer already has that name",
		"QmoveElemsToLayer":"Move selected elements to layer '%s'?",
		"QwantToClear":"Do you want to clear the drawing?\nThis will also erase your undo history!",
		"QwantToOpen":"Do you want to open a new file?\nThis will also erase your undo history!",
		"QerrorsRevertToSource":"There were parsing errors in your SVG source.\nRevert back to original SVG source?",
		"QignoreSourceChanges":"Ignore changes made to SVG source?",
		"featNotSupported":"Feature not supported",
		"enterNewImgURL":"Enter the new image URL",
		"defsFailOnSave": "NOTE: Due to a bug in your browser, this image may appear wrong (missing gradients or elements). It will however appear correct once actually saved.",
		"loadingImage":"Loading image, please wait...",
		"saveFromBrowser": "Select \"Save As...\" in your browser to save this image as a %s file.",
		"noteTheseIssues": "Also note the following issues: ",
		"unsavedChanges": "There are unsaved changes.",
		"enterNewLinkURL": "Enter the new hyperlink URL",
		"errorLoadingSVG": "Error: Unable to load SVG data",
		"URLloadFail": "Unable to load from URL",
		"retrieving": "Retrieving \"%s\"..."
	},
	confirmSetStorage: {
		message: "By default and where supported, SVG-Edit can store your editor "+
		"preferences and SVG content locally on your machine so you do not "+
		"need to add these back each time you load SVG-Edit. If, for privacy "+
		"reasons, you do not wish to store this information on your machine, "+
		"you can change away from the default option below.",
		storagePrefsAndContent: "Store preferences and SVG content locally",
		storagePrefsOnly: "Only store preferences locally",
		storagePrefs: "Store preferences locally",
		storageNoPrefsOrContent: "Do not store my preferences or SVG content locally",
		storageNoPrefs: "Do not store my preferences locally",
		rememberLabel: "Remember this choice?",
		rememberTooltip: "If you choose to opt out of storage while remembering this choice, the URL will change so as to avoid asking again."
	}
});
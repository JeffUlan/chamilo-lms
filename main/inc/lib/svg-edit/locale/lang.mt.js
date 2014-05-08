/*globals svgEditor */
svgEditor.readLang({
	lang: "mt",
	dir : "ltr",
	common: {
		"ok": "Save",
		"cancel": "Ikkanċella",
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
		"palette_info": "Ikklikkja biex timla l-bidla fil-kulur, ikklikkja-bidla għall-bidla color stroke",
		"zoom_level": "Bidla zoom livell",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "Bidla imla color",
		"stroke_color": "Color stroke Bidla",
		"stroke_style": "Bidla stroke dash stil",
		"stroke_width": "Wisa &#39;puplesija Bidla",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "Angolu ta &#39;rotazzjoni Bidla",
		"blur": "Change gaussian blur value",
		"opacity": "Bidla magħżula opaċità partita",
		"circle_cx": "CX ċirku Tibdil jikkoordinaw",
		"circle_cy": "Ċirku Tibdil cy jikkoordinaw",
		"circle_r": "Raġġ ta &#39;ċirku tal-Bidla",
		"ellipse_cx": "Bidla ellissi&#39;s CX jikkoordinaw",
		"ellipse_cy": "Ellissi Tibdil cy jikkoordinaw",
		"ellipse_rx": "Raġġ x ellissi Tibdil",
		"ellipse_ry": "Raġġ y ellissi Tibdil",
		"line_x1": "Bidla fil-linja tal-bidu tikkoordina x",
		"line_x2": "Linja tal-Bidla li jispiċċa x jikkoordinaw",
		"line_y1": "Bidla fil-linja tal-bidu y jikkoordinaw",
		"line_y2": "Linja Tibdil jispiċċa y jikkoordinaw",
		"rect_height": "Għoli rettangolu Bidla",
		"rect_width": "Wisa &#39;rettangolu Bidla",
		"corner_radius": "Bidla Rectangle Corner Radius",
		"image_width": "Wisa image Bidla",
		"image_height": "Għoli image Bidla",
		"image_url": "Bidla URL",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "Test kontenut Bidla",
		"font_family": "Bidla Font Familja",
		"font_size": "Change font size",
		"bold": "Bold Test",
		"italic": "Test korsiv"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "Bidla fil-kulur fl-isfond / opaċità",
		"connector_no_arrow": "No arrow",
		"fitToContent": "Fit għall-kontenut",
		"fit_to_all": "Tajbin għall-kontenut",
		"fit_to_canvas": "Xieraq li kanvas",
		"fit_to_layer_content": "Fit-kontenut ta &#39;saff għal",
		"fit_to_sel": "Fit-għażla",
		"align_relative_to": "Jallinjaw relattiv għall - ...",
		"relativeTo": "relattiv għall -:",
		"paġna": "paġna",
		"largest_object": "akbar oġġett",
		"selected_objects": "oġġetti elett",
		"smallest_object": "iżgħar oġġett",
		"new_doc": "Image New",
		"open_doc": "Open Image",
		"export_img": "Export",
		"save_doc": "Image Save",
		"import_doc": "Import SVG",
		"align_to_page": "Align Element to Page",
		"align_bottom": "Tallinja Bottom",
		"align_center": "Tallinja Center",
		"align_left": "Tallinja Left",
		"align_middle": "Tallinja Nofsani",
		"align_right": "Tallinja Dritt",
		"align_top": "Tallinja Top",
		"mode_select": "Select Tool",
		"mode_fhpath": "Lapes Tool",
		"mode_line": "Line Tool",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "Free Hand-Rectangle",
		"mode_ellipse": "Ellissi",
		"mode_circle": "Circle",
		"mode_fhellipse": "Free Hand-ellissi",
		"mode_path": "Path Tool",
		"mode_shapelib": "Shape library",
		"mode_text": "Text Tool",
		"mode_image": "Image Tool",
		"mode_zoom": "Zoom Tool",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "Jneħħu",
		"redo": "Jerġa &#39;jagħmel",
		"tool_source": "Source Edit",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "Grupp Elements",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "Ungroup Elements",
		"docprops": "Dokument Properties",
		"imagelib": "Image Library",
		"move_bottom": "Move to Bottom",
		"move_top": "Move to Top",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "Save",
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
		"del": "Ħassar Layer",
		"move_down": "Move Layer Down",
		"new": "New Layer",
		"rename": "Semmi mill-ġdid Layer",
		"move_up": "Move Layer Up",
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
		"select_predefined": "Select predefiniti:",
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
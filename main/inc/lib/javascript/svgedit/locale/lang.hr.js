/*globals svgEditor */
svgEditor.readLang({
	lang: "hr",
	dir : "ltr",
	common: {
		"ok": "Spremiti",
		"cancel": "Odustani",
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
		"palette_info": "Kliknite promijeniti boju ispune, shift-click to promijeniti boju moždanog udara",
		"zoom_level": "Promjena razine zumiranja",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "Promjena boje ispune",
		"stroke_color": "Promjena boje moždani udar",
		"stroke_style": "Promijeni stroke crtica stil",
		"stroke_width": "Promjena širine moždani udar",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "Promijeni rotation angle",
		"blur": "Change gaussian blur value",
		"opacity": "Promjena odabrane stavke neprozirnost",
		"circle_cx": "Promjena krug&#39;s CX koordinirati",
		"circle_cy": "Cy Promijeni krug je koordinirati",
		"circle_r": "Promjena krug je radijusa",
		"ellipse_cx": "Promjena elipsa&#39;s CX koordinirati",
		"ellipse_cy": "Cy Promijeni elipsa je koordinirati",
		"ellipse_rx": "Promijeniti elipsa&#39;s x polumjer",
		"ellipse_ry": "Promjena elipsa&#39;s y polumjer",
		"line_x1": "Promijeni linija je početak x koordinatu",
		"line_x2": "Promjena linije završetak x koordinatu",
		"line_y1": "Promijeni linija je početak y koordinatu",
		"line_y2": "Promjena linije završetak y koordinatu",
		"rect_height": "Promijeni pravokutnik visine",
		"rect_width": "Promijeni pravokutnik širine",
		"corner_radius": "Promijeni Pravokutnik Corner Radius",
		"image_width": "Promijeni sliku širine",
		"image_height": "Promijeni sliku visina",
		"image_url": "Promijeni URL",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "Promjena sadržaja teksta",
		"font_family": "Promjena fontova",
		"font_size": "Change font size",
		"bold": "Podebljani tekst",
		"italic": "Italic Text"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "Promijeni boju pozadine / neprozirnost",
		"connector_no_arrow": "No arrow",
		"fitToContent": "Fit to Content",
		"fit_to_all": "Prilagodi na sve sadržaje",
		"fit_to_canvas": "Prilagodi na platnu",
		"fit_to_layer_content": "Prilagodi sloj sadržaj",
		"fit_to_sel": "Prilagodi odabir",
		"align_relative_to": "Poravnaj u odnosu na ...",
		"relativeTo": "u odnosu na:",
		"page": "stranica",
		"largest_object": "najveći objekt",
		"selected_objects": "izabrani objekti",
		"smallest_object": "najmanji objekt",
		"new_doc": "Nove slike",
		"open_doc": "Otvori sliku",
		"export_img": "Export",
		"save_doc": "Spremanje slike",
		"import_doc": "Import Image",
		"align_to_page": "Align Element to Page",
		"align_bottom": "Poravnaj dolje",
		"align_center": "Centriraj",
		"align_left": "Poravnaj lijevo",
		"align_middle": "Poravnaj Srednji",
		"align_right": "Poravnaj desno",
		"align_top": "Poravnaj Top",
		"mode_select": "Odaberite alat",
		"mode_fhpath": "Pencil Tool",
		"mode_line": "Line Tool",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "Free-Hand Pravokutnik",
		"mode_ellipse": "Elipsa",
		"mode_circle": "Circle",
		"mode_fhellipse": "Free-Hand Ellipse",
		"mode_path": "Path Tool",
		"mode_shapelib": "Shape library",
		"mode_text": "Tekst Alat",
		"mode_image": "Image Tool",
		"mode_zoom": "Alat za zumiranje",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "Poništi",
		"redo": "Redo",
		"tool_source": "Uredi Source",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "Grupa Elementi",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "Razgrupiranje Elementi",
		"docprops": "Svojstva dokumenta",
		"imagelib": "Image Library",
		"move_bottom": "Move to Bottom",
		"move_top": "Pomakni na vrh",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "Spremiti",
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
		"del": "Brisanje sloja",
		"move_down": "Move Layer Down",
		"new": "New Layer",
		"rename": "Preimenuj Layer",
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
		"select_predefined": "Select predefinirane:",
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

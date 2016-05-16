/*globals svgEditor */
svgEditor.readLang({
	lang: "et",
	dir : "ltr",
	common: {
		"ok": "Salvestama",
		"cancel": "Tühista",
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
		"palette_info": "Click muuta täitke värvi, Shift-nuppu, et muuta insult värvi",
		"zoom_level": "Muuda suumi taset",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "Muuda täitke värvi",
		"stroke_color": "Muuda insult värvi",
		"stroke_style": "Muuda insult kriips stiil",
		"stroke_width": "Muuda insult laius",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "Muuda Pöördenurk",
		"blur": "Change gaussian blur value",
		"opacity": "Muuda valitud elemendi läbipaistmatus",
		"circle_cx": "Muuda ringi&#39;s cx kooskõlastada",
		"circle_cy": "Muuda ringi&#39;s cy kooskõlastada",
		"circle_r": "Muuda ring on raadiusega",
		"ellipse_cx": "Muuda ellips&#39;s cx kooskõlastada",
		"ellipse_cy": "Muuda ellips&#39;s cy kooskõlastada",
		"ellipse_rx": "Muuda ellips&#39;s x raadius",
		"ellipse_ry": "Muuda ellips&#39;s y raadius",
		"line_x1": "Muuda rööbastee algab x-koordinaadi",
		"line_x2": "Muuda Line lõpeb x-koordinaadi",
		"line_y1": "Muuda rööbastee algab y-koordinaadi",
		"line_y2": "Muuda Line lõppenud y-koordinaadi",
		"rect_height": "Muuda ristküliku kõrgus",
		"rect_width": "Muuda ristküliku laius",
		"corner_radius": "Muuda ristkülik Nurgakabe Raadius",
		"image_width": "Muuda pilt laius",
		"image_height": "Muuda pilt kõrgus",
		"image_url": "Change URL",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "Muuda teksti sisu",
		"font_family": "Muutke Kirjasinperhe",
		"font_size": "Change font size",
		"bold": "Rasvane kiri",
		"italic": "Kursiiv"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "Muuda tausta värvi / läbipaistmatus",
		"connector_no_arrow": "No arrow",
		"fitToContent": "Fit to Content",
		"fit_to_all": "Sobita kogu sisu",
		"fit_to_canvas": "Sobita lõuend",
		"fit_to_layer_content": "Sobita kiht sisu",
		"fit_to_sel": "Fit valiku",
		"align_relative_to": "Viia võrreldes ...",
		"relativeTo": "võrreldes:",
		"page": "lehekülg",
		"largest_object": "suurim objekt",
		"selected_objects": "valitud objektide",
		"smallest_object": "väikseim objekt",
		"new_doc": "Uus pilt",
		"open_doc": "Pildi avamine",
		"export_img": "Export",
		"save_doc": "Salvesta pilt",
		"import_doc": "Import Image",
		"align_to_page": "Align Element to Page",
		"align_bottom": "Viia Bottom",
		"align_center": "Keskele joondamine",
		"align_left": "Vasakjoondus",
		"align_middle": "Viia Lähis -",
		"align_right": "Paremjoondus",
		"align_top": "Viia Üles",
		"mode_select": "Vali Tool",
		"mode_fhpath": "Pencil Tool",
		"mode_line": "Line Tool",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "Online-Hand Ristkülik",
		"mode_ellipse": "Ellips",
		"mode_circle": "Circle",
		"mode_fhellipse": "Online-Hand Ellips",
		"mode_path": "Path Tool",
		"mode_shapelib": "Shape library",
		"mode_text": "Tekst Tool",
		"mode_image": "Pilt Tool",
		"mode_zoom": "Zoom Tool",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "Undo",
		"redo": "Redo",
		"tool_source": "Muuda Allikas",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "Rühma elemendid",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "Lõhu Elements",
		"docprops": "Dokumendi omadused",
		"imagelib": "Image Library",
		"move_bottom": "Liiguta alla",
		"move_top": "Liiguta üles",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "Salvestama",
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
		"del": "Kustuta Kiht",
		"move_down": "Liiguta kiht alla",
		"new": "Uus kiht",
		"rename": "Nimeta kiht",
		"move_up": "Liiguta kiht üles",
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
		"select_predefined": "Valige eelmääratletud:",
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

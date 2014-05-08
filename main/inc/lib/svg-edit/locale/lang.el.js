/*globals svgEditor */
svgEditor.readLang({
	lang: "el",
	dir : "ltr",
	common: {
		"ok": "Αποθηκεύω",
		"cancel": "Άκυρο",
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
		"palette_info": "Κάντε κλικ για να συμπληρώσετε την αλλαγή χρώματος, στροφή κλικ για να αλλάξετε το χρώμα εγκεφαλικό",
		"zoom_level": "Αλλαγή επίπεδο μεγέθυνσης",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "Αλλαγή συμπληρώστε χρώμα",
		"stroke_color": "Αλλαγή χρώματος εγκεφαλικό",
		"stroke_style": "Αλλαγή στυλ παύλα εγκεφαλικό",
		"stroke_width": "Αλλαγή πλάτος γραμμής",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "Αλλαγή γωνία περιστροφής",
		"blur": "Change gaussian blur value",
		"opacity": "Αλλαγή αδιαφάνεια επιλεγμένο σημείο",
		"circle_cx": "Cx Αλλαγή κύκλου συντονίζουν",
		"circle_cy": "Αλλαγή κύκλου cy συντονίζουν",
		"circle_r": "Αλλαγή ακτίνα κύκλου",
		"ellipse_cx": "Αλλαγή ellipse του CX συντονίζουν",
		"ellipse_cy": "Αλλαγή ellipse του cy συντονίζουν",
		"ellipse_rx": "X ακτίνα Αλλαγή ellipse του",
		"ellipse_ry": "Y ακτίνα Αλλαγή ellipse του",
		"line_x1": "Αλλαγή γραμμής εκκίνησης x συντονίζουν",
		"line_x2": "Αλλαγή γραμμής λήγει x συντονίζουν",
		"line_y1": "Αλλαγή γραμμής εκκίνησης y συντονίζουν",
		"line_y2": "Αλλαγή γραμμής λήγει y συντονίζουν",
		"rect_height": "Αλλαγή ύψος ορθογωνίου",
		"rect_width": "Αλλαγή πλάτους ορθογώνιο",
		"corner_radius": "Αλλαγή ορθογώνιο Corner Radius",
		"image_width": "Αλλαγή πλάτος εικόνας",
		"image_height": "Αλλαγή ύψος εικόνας",
		"image_url": "Αλλαγή URL",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "Αλλαγή περιεχόμενο κειμένου",
		"font_family": "Αλλαγή γραμματοσειράς Οικογένεια",
		"font_size": "Αλλαγή μεγέθους γραμματοσειράς",
		"bold": "Bold Text",
		"italic": "Πλάγιους"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "Αλλαγή χρώματος φόντου / αδιαφάνεια",
		"connector_no_arrow": "No arrow",
		"fitToContent": "Fit to Content",
		"fit_to_all": "Ταιριάζει σε όλο το περιεχόμενο",
		"fit_to_canvas": "Προσαρμογή στο μουσαμά",
		"fit_to_layer_content": "Προσαρμογή στο περιεχόμενο στρώμα",
		"fit_to_sel": "Fit to επιλογή",
		"align_relative_to": "Στοίχιση σε σχέση με ...",
		"relativeTo": "σε σχέση με:",
		"σελίδα": "σελίδα",
		"largest_object": "μεγαλύτερο αντικείμενο",
		"selected_objects": "εκλέγεται αντικείμενα",
		"smallest_object": "μικρότερο αντικείμενο",
		"new_doc": "Νέα εικόνα",
		"open_doc": "Άνοιγμα εικόνας",
		"export_img": "Export",
		"save_doc": "Αποθήκευση εικόνας",
		"import_doc": "Import SVG",
		"align_to_page": "Align Element to Page",
		"align_bottom": "Στοίχισηκάτω",
		"align_center": "Στοίχισηστοκέντρο",
		"align_left": "Στοίχισηαριστερά",
		"align_middle": "Ευθυγράμμιση Μέση",
		"align_right": "Στοίχισηδεξιά",
		"align_top": "Στοίχισηπάνω",
		"mode_select": "Select Tool",
		"mode_fhpath": "Εργαλείομολυβιού",
		"mode_line": "Line Tool",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "Δωρεάν-Hand ορθογώνιο",
		"mode_ellipse": "Ellipse",
		"mode_circle": "Κύκλος",
		"mode_fhellipse": "Δωρεάν-Hand Ellipse",
		"mode_path": "Path Tool",
		"mode_shapelib": "Shape library",
		"mode_text": "Κείμενο Tool",
		"mode_image": "Image Tool",
		"mode_zoom": "Zoom Tool",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "Αναίρεση",
		"redo": "Redo",
		"tool_source": "Επεξεργασία Πηγή",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "Ομάδα Στοιχεία",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "Κατάργηση ομαδοποίησης Στοιχεία",
		"docprops": "Ιδιότητες εγγράφου",
		"imagelib": "Image Library",
		"move_bottom": "Μετακίνηση προς τα κάτω",
		"move_top": "Μετακίνηση στην αρχή",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "Αποθηκεύω",
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
		"del": "Διαγραφήστρώματος",
		"move_down": "Μετακίνηση Layer Down",
		"new": "Νέο Layer",
		"rename": "Μετονομασία Layer",
		"move_up": "Μετακίνηση Layer Up",
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
		"select_predefined": "Επιλογή προκαθορισμένων:",
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
/*globals svgEditor */
svgEditor.readLang({
	lang: "ms",
	dir : "ltr",
	common: {
		"ok": "Simpan",
		"cancel": "Batal",
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
		"palette_info": "Klik untuk menukar warna mengisi, shift-klik untuk menukar warna stroke",
		"zoom_level": "Mengubah peringkat pembesaran",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "Tukar Warna mengisi",
		"stroke_color": "Tukar Warna stroke",
		"stroke_style": "Tukar gaya dash stroke",
		"stroke_width": "Tukar stroke width",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "Namakan sudut putaran",
		"blur": "Change gaussian blur value",
		"opacity": "Mengubah item yang dipilih keburaman",
		"circle_cx": "Mengubah koordinat bulatan cx",
		"circle_cy": "Mengubah koordinat cy bulatan",
		"circle_r": "Tukar jari-jari lingkaran",
		"ellipse_cx": "Tukar elips&#39;s cx koordinat",
		"ellipse_cy": "Tukar elips&#39;s cy koordinat",
		"ellipse_rx": "Tukar elips&#39;s x jari-jari",
		"ellipse_ry": "Tukar elips&#39;s y jari-jari",
		"line_x1": "Ubah baris mulai x koordinat",
		"line_x2": "Ubah baris&#39;s Berakhir x koordinat",
		"line_y1": "Ubah baris mulai y koordinat",
		"line_y2": "Ubah baris di tiap akhir y koordinat",
		"rect_height": "Perubahan quality persegi panjang",
		"rect_width": "Tukar persegi panjang lebar",
		"corner_radius": "Tukar Corner Rectangle Radius",
		"image_width": "Tukar Lebar imej",
		"image_height": "Tinggi gambar Kaca",
		"image_url": "Tukar URL",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "Tukar isi teks",
		"font_family": "Tukar Font Keluarga",
		"font_size": "Ubah Saiz Font",
		"bold": "Bold Teks",
		"italic": "Italic Teks"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "Mengubah warna latar belakang / keburaman",
		"connector_no_arrow": "No arrow",
		"fitToContent": "Fit to Content",
		"fit_to_all": "Cocok untuk semua kandungan",
		"fit_to_canvas": "Muat kanvas",
		"fit_to_layer_content": "Muat kandungan lapisan",
		"fit_to_sel": "Fit seleksi",
		"align_relative_to": "Rata relatif ...",
		"relativeTo": "relatif:",
		"Laman": "Laman",
		"largest_object": "objek terbesar",
		"selected_objects": "objek terpilih",
		"smallest_object": "objek terkecil",
		"new_doc": "Imej Baru",
		"open_doc": "Membuka Image",
		"export_img": "Export",
		"save_doc": "Save Image",
		"import_doc": "Import SVG",
		"align_to_page": "Align Element to Page",
		"align_bottom": "Rata Bottom",
		"align_center": "Rata Tengah",
		"align_left": "Rata Kiri",
		"align_middle": "Rata Tengah",
		"align_right": "Rata Kanan",
		"align_top": "Rata Popular",
		"mode_select": "Pilih Tool",
		"mode_fhpath": "Pencil Tool",
		"mode_line": "Line Tool",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "Free-Hand Persegi Panjang",
		"mode_ellipse": "Ellipse",
		"mode_circle": "Lingkaran",
		"mode_fhellipse": "Free-Hand Ellipse",
		"mode_path": "Path Tool",
		"mode_shapelib": "Shape library",
		"mode_text": "Teks Tool",
		"mode_image": "Image Tool",
		"mode_zoom": "Zoom Tool",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "Undo",
		"redo": "Redo",
		"tool_source": "Edit Source",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "Kelompok Elemen",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "Ungroup Elemen",
		"docprops": "Document Properties",
		"imagelib": "Image Library",
		"move_bottom": "Pindah ke Bawah",
		"move_top": "Pindah ke Atas",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "Simpan",
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
		"del": "Padam Layer",
		"move_down": "Pindah Layer Bawah",
		"new": "New Layer",
		"rename": "Rename Layer",
		"move_up": "Pindah Layer Up",
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
		"select_predefined": "Pilih standard:",
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
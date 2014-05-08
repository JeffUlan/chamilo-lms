/*globals svgEditor */
svgEditor.readLang({
	lang: "th",
	dir : "ltr",
	common: {
		"ok": "บันทึก",
		"cancel": "ยกเลิก",
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
		"palette_info": "คลิกเพื่อเปลี่ยนใส่สีกะคลิกเปลี่ยนสีจังหวะ",
		"zoom_level": "เปลี่ยนระดับการซูม",
		"panel_drag": "Drag left/right to resize side panel"
	},
	properties: {
		"id": "Identify the element",
		"fill_color": "เปลี่ยนใส่สี",
		"stroke_color": "สีจังหวะเปลี่ยน",
		"stroke_style": "รีบเปลี่ยนสไตล์จังหวะ",
		"stroke_width": "ความกว้างจังหวะเปลี่ยน",
		"pos_x": "Change X coordinate",
		"pos_y": "Change Y coordinate",
		"linecap_butt": "Linecap: Butt",
		"linecap_round": "Linecap: Round",
		"linecap_square": "Linecap: Square",
		"linejoin_bevel": "Linejoin: Bevel",
		"linejoin_miter": "Linejoin: Miter",
		"linejoin_round": "Linejoin: Round",
		"angle": "มุมหมุนเปลี่ยน",
		"blur": "Change gaussian blur value",
		"opacity": "เปลี่ยนความทึบเลือกรายการ",
		"circle_cx": "Cx วงกลมเปลี่ยนของพิกัด",
		"circle_cy": "วงกลมเปลี่ยนเป็น cy ประสานงาน",
		"circle_r": "รัศมีวงกลมเปลี่ยนเป็น",
		"ellipse_cx": "เปลี่ยน ellipse ของ cx ประสานงาน",
		"ellipse_cy": "Ellipse เปลี่ยนของ cy ประสานงาน",
		"ellipse_rx": "Ellipse เปลี่ยนของรัศมี x",
		"ellipse_ry": "Ellipse เปลี่ยนของรัศมี y",
		"line_x1": "สายเปลี่ยนเป็นเริ่มต้น x พิกัด",
		"line_x2": "สายเปลี่ยนเป็นสิ้นสุด x พิกัด",
		"line_y1": "สายเปลี่ยนเป็นเริ่มต้น y พิกัด",
		"line_y2": "สายเปลี่ยนเป็นสิ้นสุด y พิกัด",
		"rect_height": "ความสูงสี่เหลี่ยมผืนผ้าเปลี่ยน",
		"rect_width": "ความกว้างสี่เหลี่ยมผืนผ้าเปลี่ยน",
		"corner_radius": "รัศมีเปลี่ยนสี่เหลี่ยมผืนผ้า Corner",
		"image_width": "ความกว้างเปลี่ยนรูปภาพ",
		"image_height": "ความสูงเปลี่ยนรูปภาพ",
		"image_url": "URL เปลี่ยน",
		"node_x": "Change node's x coordinate",
		"node_y": "Change node's y coordinate",
		"seg_type": "Change Segment type",
		"straight_segments": "Straight",
		"curve_segments": "Curve",
		"text_contents": "เปลี่ยนเนื้อหาข้อความ",
		"font_family": "ครอบครัว Change Font",
		"font_size": "เปลี่ยนขนาดตัวอักษร",
		"bold": "ข้อความตัวหนา",
		"italic": "ข้อความตัวเอียง"
	},
	tools: { 
		"main_menu": "Main Menu",
		"bkgnd_color_opac": "สีพื้นหลังเปลี่ยน / ความทึบ",
		"connector_no_arrow": "No arrow",
		"fitToContent": "Fit to Content",
		"fit_to_all": "พอดีกับเนื้อหาทั้งหมด",
		"fit_to_canvas": "เหมาะสมในการผ้าใบ",
		"fit_to_layer_content": "พอดีเนื้อหาชั้นที่",
		"fit_to_sel": "เหมาะสมในการเลือก",
		"align_relative_to": "จัดชิดเทียบกับ ...",
		"relativeTo": "เทียบกับ:",
		"หน้า": "หน้า",
		"largest_object": "ที่ใหญ่ที่สุดในวัตถุ",
		"selected_objects": "วัตถุเลือกตั้ง",
		"smallest_object": "วัตถุที่เล็กที่สุด",
		"new_doc": "รูปภาพใหม่",
		"open_doc": "ภาพเปิด",
		"export_img": "Export",
		"save_doc": "บันทึกรูปภาพ",
		"import_doc": "Import SVG",
		"align_to_page": "Align Element to Page",
		"align_bottom": "ด้านล่างชิด",
		"align_center": "จัดแนวกึ่งกลาง",
		"align_left": "จัดชิดซ้าย",
		"align_middle": "กลางชิด",
		"align_right": "จัดชิดขวา",
		"align_top": "ด้านบนชิด",
		"mode_select": "เครื่องมือเลือก",
		"mode_fhpath": "เครื่องมือดินสอ",
		"mode_line": "เครื่องมือ Line",
		"mode_connect": "Connect two objects",
		"mode_rect": "Rectangle Tool",
		"mode_square": "Square Tool",
		"mode_fhrect": "สี่เหลี่ยมผืนผ้า Free-Hand",
		"mode_ellipse": "Ellipse",
		"mode_circle": "Circle",
		"mode_fhellipse": "Ellipse Free-Hand",
		"mode_path": "Path Tool",
		"mode_shapelib": "Shape library",
		"mode_text": "เครื่องมือ Text",
		"mode_image": "เครื่องมือ Image",
		"mode_zoom": "เครื่องมือซูม",
		"mode_eyedropper": "Eye Dropper Tool",
		"no_embed": "NOTE: This image cannot be embedded. It will depend on this path to be displayed",
		"undo": "เลิก",
		"redo": "ทำซ้ำ",
		"tool_source": "แหล่งที่มาแก้ไข",
		"wireframe_mode": "Wireframe Mode",
		"toggle_grid": "Show/Hide Grid",
		"clone": "Clone Element(s)",
		"del": "Delete Element(s)",
		"group_elements": "องค์ประกอบของกลุ่ม",
		"make_link": "Make (hyper)link",
		"set_link_url": "Set link URL (leave empty to remove)",
		"to_path": "Convert to Path",
		"reorient_path": "Reorient path",
		"ungroup": "องค์ประกอบ Ungroup",
		"docprops": "คุณสมบัติของเอกสาร",
		"imagelib": "Image Library",
		"move_bottom": "ย้ายไปด้านล่าง",
		"move_top": "ย้ายไปด้านบน",
		"node_clone": "Clone Node",
		"node_delete": "Delete Node",
		"node_link": "Link Control Points",
		"add_subpath": "Add sub-path",
		"openclose_path": "Open/close sub-path",
		"source_save": "บันทึก",
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
		"del": "Delete Layer",
		"move_down": "ย้าย Layer ลง",
		"new": "Layer ใหม่",
		"rename": "Layer เปลี่ยนชื่อ",
		"move_up": "ย้าย Layer Up",
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
		"select_predefined": "เลือกที่กำหนดไว้ล่วงหน้า:",
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
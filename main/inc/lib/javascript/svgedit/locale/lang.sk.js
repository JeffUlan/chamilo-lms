/*globals svgEditor */
svgEditor.readLang({
	lang: "sk",
	dir : "ltr",
	common: {
		"ok": "Uložiť",
		"cancel": "Zrušiť",
		"key_backspace": "Backspace", 
		"key_del": "Delete", 
		"key_down": "šípka dole", 
		"key_up": "šípka hore", 
		"more_opts": "Viac možností",
		"url": "URL",
		"width": "Šírka",
		"height": "Výška"
	},
	misc: {
		"powered_by": "Beží na"
	}, 
	ui: {
		"toggle_stroke_tools": "Skryť/ukázať viac nástrojov pre krivku",
		"palette_info": "Kliknutím zmeníte farbu výplne, so Shiftom zmeníte farbu obrysu",
		"zoom_level": "Zmena priblíženia",
		"panel_drag": "Potiahnutie vľavo/vpravo na zmenu veľkosti bočného panela"
	},
	properties: {
		"id": "Zmeniť ID elementu",
		"fill_color": "Zmeniť farbu výplne",
		"stroke_color": "Zmeniť farbu obrysu",
		"stroke_style": "Zmeniť štýl obrysu",
		"stroke_width": "Zmeniť hrúbku obrysu",
		"pos_x": "Zmeniť súradnicu X",
		"pos_y": "Zmeniť súradnicu Y",
		"linecap_butt": "Koniec čiary: presný",
		"linecap_round": "Koniec čiary: zaoblený",
		"linecap_square": "Koniec čiary: so štvorcovým presahom",
		"linejoin_bevel": "Napojenie čiar: skosené",
		"linejoin_miter": "Napojenie čiar: ostré",
		"linejoin_round": "Napojenie čiar: oblé",
		"angle": "Zmeniť uhol natočenia",
		"blur": "Zmeniť intenzitu rozmazania",
		"opacity": "Zmeniť prehľadnosť vybraných položiek",
		"circle_cx": "Zmeniť súradnicu X stredu kružnice",
		"circle_cy": "Zmeniť súradnicu Y stredu kružnice",
		"circle_r": "Zmeniť polomer kružnice",
		"ellipse_cx": "Zmeniť súradnicu X stredu elipsy",
		"ellipse_cy": "Zmeniť súradnicu Y stredu elipsy",
		"ellipse_rx": "Zmeniť polomer X elipsy",
		"ellipse_ry": "Zmeniť polomer Y elipsy",
		"line_x1": "Zmeniť počiatočnú súradnicu X čiary",
		"line_x2": "Zmeniť koncovú súradnicu X čiary",
		"line_y1": "Zmeniť počiatočnú súradnicu Y čiary",
		"line_y2": "Zmeniť koncovú súradnicu Y čiary",
		"rect_height": "Zmena výšku obdĺžnika",
		"rect_width": "Zmeniť šírku obdĺžnika",
		"corner_radius": "Zmeniť zaoblenie rohov obdĺžnika",
		"image_width": "Zmeniť šírku obrázka",
		"image_height": "Zmeniť výšku obrázka",
		"image_url": "Zmeniť URL",
		"node_x": "Zmeniť uzlu súradnicu X",
		"node_y": "Zmeniť uzlu súradnicu Y",
		"seg_type": "Zmeniť typ segmentu",
		"straight_segments": "Rovný",
		"curve_segments": "Krivka",
		"text_contents": "Zmeniť text",
		"font_family": "Zmeniť font",
		"font_size": "Zmeniť veľkosť písma",
		"bold": "Tučné",
		"italic": "Kurzíva"
	},
	tools: { 
		"main_menu": "Hlavné menu",
		"bkgnd_color_opac": "Zmeniť farbu a priehľadnosť pozadia",
		"connector_no_arrow": "Spojnica bez šípok",
		"fitToContent": "Prispôsobiť obsahu",
		"fit_to_all": "Prisposobiť celému obsahu",
		"fit_to_canvas": "Prispôsobiť stránke",
		"fit_to_layer_content": "Prispôsobiť obsahu vrstvy",
		"fit_to_sel": "Prispôsobiť výberu",
		"align_relative_to": "Zarovnať relatívne k ...",
		"relativeTo": "vzhľadom k:",
		"page": "stránke",
		"largest_object": "najväčšiemu objektu",
		"selected_objects": "zvoleným objektom",
		"smallest_object": "najmenšiemu objektu",
		"new_doc": "Nový obrázok",
		"open_doc": "Otvoriť obrázok",
		"export_img": "Export",
		"save_doc": "Uložiť obrázok",
		"import_doc": "Import Image",
		"align_to_page": "Zarovnať element na stránku",
		"align_bottom": "Zarovnať dole",
		"align_center": "Zarovnať na stred",
		"align_left": "Zarovnať doľava",
		"align_middle": "Zarovnať na stred",
		"align_right": "Zarovnať doprava",
		"align_top": "Zarovnať hore",
		"mode_select": "Výber",
		"mode_fhpath": "Ceruzka",
		"mode_line": "Čiara",
		"mode_connect": "Spojiť dva objekty",
		"mode_rect": "Obdĺžnik",
		"mode_square": "Štvorec",
		"mode_fhrect": "Obdĺžnik voľnou rukou",
		"mode_ellipse": "Elipsa",
		"mode_circle": "Kružnica",
		"mode_fhellipse": "Elipsa voľnou rukou",
		"mode_path": "Krivka",
		"mode_shapelib": "Knižnica Tvarov",
		"mode_text": "Text",
		"mode_image": "Obrázok",
		"mode_zoom": "Priblíženie",
		"mode_eyedropper": "Pipeta",
		"no_embed": "POZNÁMKA: Tento obrázok nemôže byť vložený. Jeho zobrazenie bude závisieť na jeho ceste",
		"undo": "Späť",
		"redo": "Opakovať",
		"tool_source": "Upraviť SVG kód",
		"wireframe_mode": "Drôtový model",
		"toggle_grid": "Zobraz/Skry mriežku",
		"clone": "Klonuj element(y)",
		"del": "Zmaž element(y)",
		"group_elements": "Zoskupiť elementy",
		"make_link": "Naviaž odkaz (hyper)link",
		"set_link_url": "Nastav odkaz URL (ak prázdny, odstráni sa)",
		"to_path": "Previesť na krivku",
		"reorient_path": "Zmeniť orientáciu krivky",
		"ungroup": "Zrušiť skupinu",
		"docprops": "Vlastnosti dokumentu",
		"imagelib": "Knižnica obrázkov",
		"move_bottom": "Presunúť spodok",
		"move_top": "Presunúť na vrch",
		"node_clone": "Klonovať uzol",
		"node_delete": "Zmazať uzol",
		"node_link": "Prepojiť kontrolné body",
		"add_subpath": "Pridať ďalšiu súčasť krivky",
		"openclose_path": "Otvoriť/uzatvoriť súčasť krivky",
		"source_save": "Uložiť",
		"cut": "Vystrihnutie",
		"copy": "Kópia",
		"paste": "Vloženie",
		"paste_in_place": "Vloženie na pôvodnom mieste",
		"delete": "Zmazanie",
		"group": "Group",
		"move_front": "Vysuň navrch",
		"move_up": "Vysuň vpred",
		"move_down": "Zasuň na spodok",
		"move_back": "Zasuň dozadu"
	},
	layers: {
		"layer": "Vrstva",
		"layers": "Vrstvy",
		"del": "Odstrániť vrstvu",
		"move_down": "Presunúť vrstvu dole",
		"new": "Nová vrstva",
		"rename": "Premenovať vrstvu",
		"move_up": "Presunúť vrstvu hore",
		"dupe": "Zduplikovať vrstvu",
		"merge_down": "Zlúčiť s vrstvou dole",
		"merge_all": "Zlúčiť všetko",
		"move_elems_to": "Presunúť elementy do:",
		"move_selected": "Presunúť vybrané elementy do inej vrstvy"
	},
	config: {
		"image_props": "Vlastnosti obrázka",
		"doc_title": "Titulok",
		"doc_dims": "Rozmery plátna",
		"included_images": "Vložené obrázky",
		"image_opt_embed": "Vložiť data (lokálne súbory)",
		"image_opt_ref": "Použiť referenciu na súbor",
		"editor_prefs": "Vlastnosti editora",
		"icon_size": "Veľkosť ikon",
		"language": "Jazyk",
		"background": "Zmeniť pozadie",
		"editor_img_url": "Image URL",
		"editor_bg_note": "Poznámka: Pozadie nebude uložené spolu s obrázkom.",
		"icon_large": "Veľká",
		"icon_medium": "Stredná",
		"icon_small": "Malá",
		"icon_xlarge": "Extra veľká",
		"select_predefined": "Vybrať preddefinovaný:",
		"units_and_rulers": "Jednotky & Pravítka",
		"show_rulers": "Ukáž pravítka",
		"base_unit": "Základné jednotky:",
		"grid": "Mriežka",
		"snapping_onoff": "Priväzovanie (do mriežky) zap/vyp",
		"snapping_stepsize": "Priväzovanie (do mriežky) veľkosť kroku:",
		"grid_color": "Grid color"
	},
	shape_cats: {
		"basic": "Základné",
		"object": "Objekty",
		"symbol": "Symboly",
		"arrow": "Šípky",
		"flowchart": "Vývojové diagramy",
		"animal": "Zvieratá",
		"game": "Karty & Šach",
		"dialog_balloon": "Dialogové balóny",
		"electronics": "Elektronika",
		"math": "Matematické",
		"music": "Hudba",
		"misc": "Rôzne",
		"raphael_1": "raphaeljs.com sada 1",
		"raphael_2": "raphaeljs.com sada 2"
	},
	imagelib: {
		"select_lib": "Výber knižnice obrázkov",
		"show_list": "Prehľad knižnice",
		"import_single": "Import jeden",
		"import_multi": "Import viacero",
		"open": "Otvoriť ako nový dokument"
	},
	notification: {
		"invalidAttrValGiven":"Neplatná hodnota",
		"noContentToFitTo":"Vyberte oblasť na prispôsobenie",
		"dupeLayerName":"Vrstva s daným názvom už existuje!",
		"enterUniqueLayerName":"Zadajte jedinečný názov vrstvy",
		"enterNewLayerName":"Zadajte názov vrstvy",
		"layerHasThatName":"Vrstva už má zadaný tento názov",
		"QmoveElemsToLayer":"Presunúť elementy do vrstvy '%s'?",
		"QwantToClear":"Naozaj chcete vymazať kresbu?\n(História bude taktiež vymazaná!)!",
		"QwantToOpen":"Chcete otvoriť nový súbor?\nTo však tiež vymaže Vašu UNDO knižnicu!",
		"QerrorsRevertToSource":"Chyba pri načítaní SVG dokumentu.\nVrátiť povodný SVG dokument?",
		"QignoreSourceChanges":"Ignorovať zmeny v SVG dokumente?",
		"featNotSupported":"Vlastnosť nie je podporovaná",
		"enterNewImgURL":"Zadajte nové URL obrázka",
		"defsFailOnSave": "POZNÁMKA: Kvôli chybe v prehliadači sa tento obrázok môže zobraziť nesprávne (napr. chýbajúce prechody či elementy). Po uložení sa zobrazí správne.",
		"loadingImage":"Nahrávam obrázok, prosím čakajte ...",
		"saveFromBrowser": "Vyberte \"Uložiť ako ...\" vo vašom prehliadači na uloženie tohoto obrázka do súboru %s.",
		"noteTheseIssues": "Môžu sa vyskytnúť nasledujúce problémy: ",
		"unsavedChanges": "Sú tu neuložené zmeny.",
		"enterNewLinkURL": "Zadajte nové URL odkazu (hyperlink)",
		"errorLoadingSVG": "Chyba: Nedajú sa načítať SVG data",
		"URLloadFail": "Nemožno čítať z URL",
		"retrieving": "Načítavanie \"%s\"..."
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
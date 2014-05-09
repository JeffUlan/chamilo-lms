/*globals svgEditor */
svgEditor.readLang({
	lang: "fr",
	dir : "ltr",
	common: {
		"ok": "OK",
		"cancel": "Annuler",
		"key_backspace": "Suppr.", 
		"key_del": "Retour Arr.", 
		"key_down": "Bas", 
		"key_up": "Haut", 
		"more_opts": "Plus d'options",
		"url": "URL",
		"width": "Largeur",
		"height": "Hauteur"
	},
	misc: {
		"powered_by": "Propulsé par"
	}, 
	ui: {
		"toggle_stroke_tools": "Montrer/Cacher plus d'outils de Contour",
		"palette_info": "Cliquer pour changer la couleur de remplissage, Maj-Clic pour changer la couleur de contour",
		"zoom_level": "Changer le niveau de zoom",
		"panel_drag": "Tirer vers la gauche/droite pour redimentionner le panneau"
	},
	properties: {
		"id": "Identifier l'élément",
		"fill_color": "Changer la couleur de remplissage",
		"stroke_color": "Changer la couleur du contour",
		"stroke_style": "Changer le style du contour",
		"stroke_width": "Changer la largeur du contour de 1, Shift-Click pour changer la largeur de 0.1",
		"pos_x": "Changer la position horizontale X",
		"pos_y": "Changer la position verticale Y",
		"linecap_butt": "Terminaison : Sur le nœud",
		"linecap_round": "Terminaison : Arrondie",
		"linecap_square": "Terminaison : Carrée",
		"linejoin_bevel": "Raccord : Biseauté",
		"linejoin_miter": "Raccord : Droit",
		"linejoin_round": "Raccord : Arrondi",
		"angle": "Changer l'angle de rotation",
		"blur": "Changer la valeur du flou gaussien",
		"opacity": "Changer l'opacité de l'élément sélectionné",
		"circle_cx": "Changer la position horizontale cx du cercle",
		"circle_cy": "Changer la position verticale cy du cercle",
		"circle_r": "Changer le rayon du cercle",
		"ellipse_cx": "Changer la position horizontale cx de l'ellipse",
		"ellipse_cy": "Changer la position verticale cy de l'ellipse",
		"ellipse_rx": "Changer le rayon horizontal x de l'ellipse",
		"ellipse_ry": "Changer le rayon vertical y de l'ellipse",
		"line_x1": "Changer la position horizontale x de début de la ligne",
		"line_x2": "Changer la position horizontale x de fin de la ligne",
		"line_y1": "Changer la position verticale y de début de la ligne",
		"line_y2": "Changer la position verticale y de fin de la ligne",
		"rect_height": "Changer la hauteur du rectangle",
		"rect_width": "Changer la largeur du rectangle",
		"corner_radius": "Changer le rayon des coins du rectangle",
		"image_width": "Changer la largeur de l'image",
		"image_height": "Changer la hauteur de l'image",
		"image_url": "Modifier l'URL",
		"node_x": "Changer la positon horizontale x du nœud",
		"node_y": "Changer la position verticale y du nœud",
		"seg_type": "Changer le type du Segment",
		"straight_segments": "Droit",
		"curve_segments": "Courbe",
		"text_contents": "Changer le contenu du texte",
		"font_family": "Changer la famille de police",
		"font_size": "Changer la taille de la police",
		"bold": "Texte en gras",
		"italic": "Texte en italique"
	},
	tools: { 
		"main_menu": "Menu principal",
		"bkgnd_color_opac": "Changer la couleur d'arrière-plan / l'opacité",
		"connector_no_arrow": "Sans flèches",
		"fitToContent": "Ajuster au contenu",
		"fit_to_all": "Ajuster au contenu de tous les calques",
		"fit_to_canvas": "Ajuster au canevas",
		"fit_to_layer_content": "Ajuster au contenu du calque",
		"fit_to_sel": "Ajuster à la sélection",
		"align_relative_to": "Aligner par rapport à ...",
		"relativeTo": "Relativement à:",
		"Page": "Page",
		"largest_object": "Objet plus gros ",
		"selected_objects": "Objets sélectionnés",
		"smallest_object": "Objet plus petit",
		"new_doc": "Nouvelle image",
		"open_doc": "Ouvrir une image",
		"export_img": "Export",
		"save_doc": "Enregistrer l'image",
		"import_doc": "Importer un objet SVG",
		"align_to_page": "Aligner l'élément relativement à la Page",
		"align_bottom": "Aligner le bas des objets",
		"align_center": "Centrer verticalement",
		"align_left": "Aligner les côtés gauches",
		"align_middle": "Centrer horizontalement",
		"align_right": "Aligner les côtés droits",
		"align_top": "Aligner le haut des objets",
		"mode_select": "Outil de sélection",
		"mode_fhpath": "Crayon à main levée",
		"mode_line": "Tracer des lignes",
		"mode_connect": "Connecter deux objets",
		"mode_rect": "Outil rectangle",
		"mode_square": "Outils carré",
		"mode_fhrect": "Rectangle main levée",
		"mode_ellipse": "Ellipse",
		"mode_circle": "Cercle",
		"mode_fhellipse": "Ellipse main levée",
		"mode_path": "Dessiner un tracé",
		"mode_shapelib": "Bibliothèque d'images",
		"mode_text": "Outil Texte",
		"mode_image": "Outil Image",
		"mode_zoom": "Zoom",
		"mode_eyedropper": "Outil Pipette",
		"no_embed": "NOTE: Cette image ne peut être incorporée en tant que données. Le contenu affiché sera celui de l'image située à cette adresse",
		"undo": "Annuler l'action",
		"redo": "Refaire l'action",
		"tool_source": "Modifier la source",
		"wireframe_mode": "Mode Fil de Fer",
		"toggle_grid": "Montrer/cacher la grille",
		"clone": "Cloner élement(s)",
		"del": "Supprimer élement(s)",
		"group_elements": "Grouper les éléments",
		"make_link": "Créer hyperlien",
		"set_link_url": "Définir le lien URL (laisser vide pour supprimer)",
		"to_path": "Convertir en tracé",
		"reorient_path": "Réorienter le tracé",
		"ungroup": "Dégrouper les éléments",
		"docprops": "Propriétés du document",
		"imagelib": "Bibliothèque d'images",
		"move_bottom": "Déplacer vers le bas",
		"move_top": "Déplacer vers le haut",
		"node_clone": "Cloner le nœud",
		"node_delete": "Supprimer le nœud",
		"node_link": "Rendre les points de contrôle solidaires",
		"add_subpath": "Ajouter un tracé secondaire",
		"openclose_path": "Ouvrir/Fermer sous-chemin",
		"source_save": "Appliquer Modifications",
		"cut": "Couper",
		"copy": "Copier",
		"paste": "Coller",
		"paste_in_place": "Coller sur place",
		"Retour Arr.": "Supprimer",
		"group": "Group",
		"move_front": "Placer au premier plan",
		"move_up": "Avancer d'un plan",
		"move_down": "Placer en arrière plan",
		"move_back": "Reculer d'un plan"
	},
	layers: {
		"layer":"Calque",
		"layers": "Calques",
		"del": "Supprimer le calque",
		"move_down": "Descendre le calque",
		"new": "Nouveau calque",
		"rename": "Renommer le calque",
		"move_up": "Monter le calque",
		"dupe": "Dupliquer calque",
		"merge_down": "Fusionner vers le bas",
		"merge_all": "Tout fusionner",
		"move_elems_to": "Déplacer éléments vers:",
		"move_selected": "Déplacer les éléments sélectionnés vers un autre calque"
	},
	config: {
		"image_props": "Propriétés de l'Image",
		"doc_title": "Titre",
		"doc_dims": "Dimensions du canevas",
		"included_images": "Images incorporées",
		"image_opt_embed": "Incorporer les images en tant que données (fichiers locaux)",
		"image_opt_ref": "Utiliser la référence des images ",
		"editor_prefs": "Préférences de l'Éditeur",
		"icon_size": "Taille des icônes",
		"language": "Langue",
		"background": "Toile de fond de l'Éditeur",
		"editor_img_url": "Image URL",
		"editor_bg_note": "Note: La toile de fond n'est pas sauvegardée avec l'image.",
		"icon_large": "Grande",
		"icon_medium": "Moyenne",
		"icon_small": "Petite",
		"icon_xlarge": "Super-Grande",
		"select_predefined": "Sélectionner prédéfinis:",
		"units_and_rulers": "Unités & Règles",
		"show_rulers": "Afficher les règles",
		"base_unit": "Unité de mesure:",
		"grid": "Grille",
		"snapping_onoff": "Épingler oui/non",
		"snapping_stepsize": "Snapping Step-Size:",
		"grid_color": "Couleur de la grille"
	},
	shape_cats: {
		"basic": "Basique",
		"object": "Objets",
		"symbol": "Symboles",
		"arrow": "Flèches",
		"flowchart": "Diagramme de flux",
		"animal": "Animaux",
		"game": "Cartes & Echecs",
		"dialog_balloon": "Bulles de dialogue",
		"electronics": "Electronique",
		"math": "Mathématiques",
		"music": "Musique",
		"misc": "Divers",
		"raphael_1": "raphaeljs.com ensemble 1",
		"raphael_2": "raphaeljs.com ensemble 2"
	},
	imagelib: {
		"select_lib": "Choisir une image dans la bibliothèque",
		"show_list": "Liste de la bibliotèque d'images",
		"import_single": "Importation simple",
		"import_multi": "Importation multiple",
		"open": "Ouvrir en tant que nouveau document"
	},
	notification: {
		"invalidAttrValGiven":"Valeur fournie invalide",
		"noContentToFitTo":"Il n'y a pas de contenu auquel ajuster",
		"dupeLayerName":"Il existe déjà un calque de ce nom !",
		"enterUniqueLayerName":"Veuillez entrer un nom (unique) pour le calque",
		"enterNewLayerName":"Veuillez entrer le nouveau nom du calque",
		"layerHasThatName":"Le calque porte déjà ce nom",
		"QmoveElemsToLayer":"Déplacer les éléments sélectionnés vers le calque '%s' ?",
		"QwantToClear":"Voulez-vous effacer le dessin ?\nL'historique de vos actions sera également effacé !",
		"QwantToOpen":"Voulez-vous ouvrir un nouveau document?\nVous perderez l'historique de vos modifications!",
		"QerrorsRevertToSource":"Il y a des erreurs d'analyse syntaxique dans votre code-source SVG.\nRevenir au code-source SVG avant modifications ?",
		"QignoreSourceChanges":"Ignorer les modifications faites à la source SVG ?",
		"featNotSupported":"Fonction non supportée",
		"enterNewImgURL":"Entrer la nouvelle URL de l'image",
		"defsFailOnSave": "NOTE : À cause d'un bug de votre navigateur, cette image peut être affichée de façon incorrecte (dégradés ou éléments manquants). Cependant, une fois enregistrée, elle sera correcte.",
		"loadingImage":"Chargement de l'image, veuillez patienter...",
		"saveFromBrowser": "Selectionner \"Enregistrer sous...\" dans votre navigateur pour sauvegarder l'image en tant que fichier %s.",
		"noteTheseIssues": "Notez également les problèmes suivants : ",
		"unsavedChanges": "Il y a des changements non sauvegardés.",
		"enterNewLinkURL": "Entrez la nouvel hyperlien URL",
		"errorLoadingSVG": "Erreur: Impossible de charger le document SVG",
		"URLloadFail": "Impossible de charger l'URL",
		"retrieving": "Récupère \"%s\"..."
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
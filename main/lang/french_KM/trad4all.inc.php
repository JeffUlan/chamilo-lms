<?php // $Id: trad4all.inc.php 4755 2005-05-02 13:38:51Z olivierb78 $
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.4.0 $Revision: 4755 $                            |
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2002 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
      |   This program is free software; you can redistribute it and/or      |
      |   modify it under the terms of the GNU General Public License        |
      |   as published by the Free Software Foundation; either version 2     |
      |   of the License, or (at your option) any later version.             |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      |          Olivier Brouckaert <oli.brouckaert@skynet.be>               |
      +----------------------------------------------------------------------+
*/

$englishLangName = "french";
$localLangName = "fran�ais";

$iso639_2_code = "fr";
$iso639_1_code = "fre";

$langNameOfLang['arabic']="arabe";
$langNameOfLang['brazilian']="br�silien";
$langNameOfLang['bulgarian']="bulgare";
$langNameOfLang['catalan']="catalan ";
$langNameOfLang['croatian']="croate";
$langNameOfLang['danish']="danois";
$langNameOfLang['dutch']="n�erlandais";
$langNameOfLang['english']="anglais";
$langNameOfLang['english_org']="anglais_org";
$langNameOfLang['finnish']="finlandais";
$langNameOfLang['french']="fran�ais";
$langNameOfLang['french_corporate']="fran�ais_corporation";
$langNameOfLang['french_KM']="fran�ais_KM";
$langNameOfLang['french_org']="fran�ais_org";
$langNameOfLang['galician']="galicien";
$langNameOfLang['hungarian']="hongrois";
$langNameOfLang['indonesian']="indon�sien";
$langNameOfLang['malay']="malais";
$langNameOfLang['slovenian']="slov�ne";
$langNameOfLang['german']="allemand";
$langNameOfLang['greek']="grec";
$langNameOfLang['italian']="italien";
$langNameOfLang['japanese']="japonnais";
$langNameOfLang['polish']="polonais";
$langNameOfLang['portuguese']="portugais";
$langNameOfLang['russian']="russe";
$langNameOfLang['simpl_chinese']="chinois simplifi�";
$langNameOfLang['spanish']="espagnol";
$langNameOfLang['spanish_latin']="espagnol Am�r. Sud";
$langNameOfLang['swedish']="su�dois";
$langNameOfLang['thai']="tha�landais";
$langNameOfLang['turkce']="turc";
$langNameOfLang['vietnamese']="vietnamien";

$charset = 'iso-8859-1';
$text_dir = 'ltr';
$left_font_family = 'verdana, helvetica, arial, geneva, sans-serif';
$right_font_family = 'helvetica, arial, geneva, sans-serif';
$number_thousands_separator = ' ';
$number_decimal_separator = ',';
$byteUnits = array('Octets', 'Ko', 'Mo', 'Go');

$langDay_of_weekNames['init'] = array('D', 'L', 'M', 'M', 'J', 'V', 'S');
$langDay_of_weekNames['short'] = array('Di', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam');
$langDay_of_weekNames['long'] = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

$langMonthNames['init']  = array('J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D');
$langMonthNames['short'] = array('Jan', 'F�v', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Ao�t', 'Sep', 'Oct', 'Nov', 'D�c');
$langMonthNames['long'] = array('Janvier', 'F�vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao�t', 'Septembre', 'Octobre', 'Novembre', 'D�cembre');

// Voir http://www.php.net/manual/en/function.strftime.php pour la variable
// ci-dessous

$dateFormatShort =  "%a %d %b %y";
$dateFormatLong  = '%A %d %B %Y';
$dateTimeFormatLong  = '%A %d %B %Y � %H:%M';
$timeNoSecFormat = '%H:%M';
// GENERIC
$langYes="Oui";
$langNo="Non";
$langBack="Retour";
$langNext="Suivant";
$langAllowed="Autoris�";
$langDenied="Refus�";
$langBackHome="Retour � la page principale";
$langPropositions="Propositions d'am�lioration de";
$langMaj="Mise � jour";
$langModify="Modifier";
$langDelete="Effacer";
$langMove="D�placer";
$langTitle="Titre";
$langHelp="Aide";
$langOk="Valider";
$langAdd="Ajouter";
$langAddIntro="Ajouter un texte d'introduction";
$langBackList="Retour � la liste";
$langText="Texte";
$langEmpty="Vide";
$langConfirmYourChoice="Veuillez confirmer votre choix";
$langAnd="et";
$langChoice="Votre choix";
$langFinish="Terminer";
$langCancel="Annuler";
$langNotAllowed="Vous n'�tes pas autoris� � acc�der � cette section";
$langManager="Responsable";
$langPlatform="Utilise la plate-forme";
$langOptional="Facultatif";
$langNextPage="Page suivante";
$langPreviousPage="Page pr�c�dente";
$langUse="Utiliser";
$langTotal="Total";
$langTake="prendre";
$langOne="Une";
$langSeveral="Plusieurs";
$langNotice="Remarque";
$langDate="Date";
$langAmong="parmi";

// banner

$langMyCourses="Mes espaces";
$langModifyProfile="Mon profil";
$langMyStats = "Mon parcours";
$langLogout="Quitter";
$langMyAgenda = "Mon agenda";

//needed for student view
$langCourseManagerview = "Vue responsable";
$langStudentView = "Vue membre";

//needed for resource linker
$lang_add_resource="Ajouter une ressource";
$lang_added_resources="Ressources ajout�es";
$lang_modify_resource="Modifier / Ajouter une ressource";
$lang_attachment="Joindre un fichier";

$langOnLine = "En ligne";
$langUsers = "utilisateurs";
$langUser = "utilisateur";

$langcourse_description = "Description du cours";
$langcalendar_event = "Agenda";
$langdocument = "Documents";
$langlearnpath = "Parcours";
$langlink = "Liens";
$langannouncement = "Annonces";
$langbb_forum = "Forums";
$langdropbox = "Dropbox";
$langquiz = "Tests";
$languser = "Utilisateurs";
$langgroup = "Groupes";
$langchat = "Discussion";
$langconference = "Conf�rence";
$langstudent_publication = "Publications";
$langtracking = "Statistiques";
$langhomepage_link = "Ajouter un lien sur la page d'accueil";
$langcourse_setting = "Param�tres du cours";
$langbackup = "Sauvegarder le cours";
$langcopy_course_content = "Copier le contenu du cours";
$langrecycle_course = "Recycler le cours";
?>

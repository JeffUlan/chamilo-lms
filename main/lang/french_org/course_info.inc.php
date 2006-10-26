<?php // $Id: course_info.inc.php 1996 2004-07-07 14:53:05Z olivierb78 $
/*
      +----------------------------------------------------------------------+
      | DOKEOS 1.5 $Revision: 1996 $                                          |
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2003 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
      |   This program is free software; you can redistribute it and/or      |
      |   modify it under the terms of the GNU General Public License        |
      |   as published by the Free Software Foundation; either version 2     |
      |   of the License, or (at your option) any later version.             |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <thomas.depraetere@dokeos.com>            |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      +----------------------------------------------------------------------+
 */

/***************************************************************
*                   Language translation
****************************************************************
GOAL
****
Translate the interface in chosen language
*****************************************************************/

$langOtherCategory	= "Autre cat�gorie";

$langModifInfo="Propri�t�s de l'espace";
$langModifDone="Les informations ont �t� modifi�es";
$langHome="Retour � la page d'accueil";
$langCode="Code de cet espace";
$langDelCourse="Supprimer cet espace";
$langProfessor="Responsable";
$langProfessors="Co-responsables";
$langTitle="Intitul�";
$langFaculty="Cat�gorie";
$langDescription="Description";
$langConfidentiality="Confidentialit�";
$langPublic="Acc�s public (depuis la page d'accueil du portail, sans identifiant)";
$langPrivOpen="Acc�s priv�, inscription ouverte";
$langPrivate="Acc�s priv� (site r�serv� aux personnes figurant dans la liste <a href=../user/user.php>membres</a>)";
$langForbidden="Acc�s non autoris�";
$langLanguage="Langue";
$langConfTip="Par d�faut votre espace est public. Mais vous pouvez d�finir le niveau de confidentialit� ci-dessus.";
$langTipLang="Cette langue vaudra pour tous les visiteurs de votre espace.";

// Change Home Page
$langAgenda="Agenda";
$langLink="Liens";
$langDocument="Documents";
$langVid="Vid�o";
$langWork="Travaux";
$langProgramMenu="Cahier des charges";
$langAnnouncement="Annonces";
$langUser="Membres";
$langForum="Forums";
$langExercise="Tests";
$langStats="Statistiques";
$langGroups ="Groupes";
$langChat ="Discussion";
$langUplPage="D�poser page et lier � l\'accueil";
$langLinkSite="Ajouter un lien sur la page d\'accueil";
$langModifGroups="Groupes";

// delete_course.php
$langDelCourse="Supprimer cet espace";
$langCourse="Le espace ";
$langHasDel="a �t� supprim�";
$langBackHome="Retour � la page d'accueil de ";
$langByDel="En supprimant cet espace, vous supprimerez tous les documents
qu'il contient et d�sinscrirez tous les membres qui y sont inscrits. <p>Voulez-vous r�ellement supprimer cet espace";
$langY="OUI";
$langN="NON";

$langDepartmentUrl = "URL du d�partement";
$langDepartmentUrlName = "D�partement";
$langDescriptionCours  = "Description de cet espace";

$langArchive="Archive";
$langArchiveCourse = "Archivage de cet espace";
$langRestoreCourse = "Restauration d'un espace";
$langRestore="Restaurer";
$langCreatedIn = "cr�� dans";
$langCreateMissingDirectories ="Cr�ation des r�pertoires manquants";
$langCopyDirectoryCourse = "Copie des fichiers de cet espace";
$langDisk_free_space = "Espace disque libre";
$langBuildTheCompressedFile ="Cr�ation du fichier compress�";
$langFileCopied = "fichier copi�";
$langArchiveLocation = "Emplacement de l'archive";
$langSizeOf ="Taille de";
$langArchiveName ="Nom de l'archive";
$langBackupSuccesfull = "Archiv� avec succ�s";
$langBUCourseDataOfMainBase = "Archivage des donn�es de cet espace dans la base de donn�es principale pour";
$langBUUsersInMainBase = "Archivage des donn�es des membres dans la base de donn�es principale pour";
$langBUAnnounceInMainBase="Archivage des donn�es des annonces dans la base de donn�es principale pour";
$langBackupOfDataBase="Archivage de la base de donn�es";
$langBackupCourse="Archiver cet espace";

$langCreationDate = "Cr��";
$langExpirationDate  = "Date d'expiration";
$langPostPone = "Post pone";
$langLastEdit = "Derni�re �dition";
$langLastVisit = "Derni�re visite";

$langSubscription="Inscription";
$langCourseAccess="Acc�s � cet espace";

$langDownload="T�l�charger";
$langConfirmBackup="Voulez-vous vraiment archiver cet espace";

$langCreateSite="Cr�er un espace";

$langRestoreDescription="Ce espace se trouve dans une archive que vous pouvez s�lectionner ci-dessous.<br><br>
Lorsque vous aurez cliqu� sur &quot;Restaurer&quot;, l'archive sera d�compress�e et l'espace recr��.";
$langRestoreNotice="Ce script ne permet pas encore la restauration automatique des membres, mais les donn�es sauvegard�es dans le fichier &quot;users.csv&quot; sont suffisantes pour que l'administrateur puisse effectuer cette op�ration manuellement.";
$langAvailableArchives="Liste des archives disponibles";
$langNoArchive="Aucune archive n'a �t� s�lectionn�e";
$langArchiveNotFound="Archive introuvable";
$langArchiveUncompressed="L'archive a �t� d�compress�e et install�e.";
$langCsvPutIntoDocTool="Le fichier &quot;users.csv&quot; a �t� plac� dans l'outil Documents.";
?>

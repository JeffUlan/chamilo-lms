<?php # $Id: tracking.inc.php 1996 2004-07-07 14:53:05Z olivierb78 $
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.5.0 $Revision: 1996 $                            |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      |          Piraux S�bastien  <piraux_seb@hotmail.com>                  |
      +----------------------------------------------------------------------+
 */

/***************************************************************
*                   Language translation
****************************************************************
GOAL
****
Translate the interface in chosen language

FRENCH
*****************************************************************/

/* general */
$langTrackingDisabled = "Le syst�me de suivi (tracking) a �t� d�sactiv� par l'administrateur syst�me.";
$langToolName="Suivi";
$langShowAll = "Montrer tout";
$langShowNone = "Cacher tout";

$langCourseStats = "Statistiques de l'espace";
$langToolsAccess = "Acc�s aux outils";
$langCourseAccess = "Acc�s � cet espace";
$langLinksAccess = "Liens";
$langDocumentsAccess = "Documents";
$langScormAccess = "Espace au format SCORM";


$langLinksDetails = "Liens visit�s par le membre";
$langWorksDetails = "Contributions post�es par le membre au nom de 'Auteurs'";
$langLoginsDetails = "Cliquez sur le nom du mois pour plus de d�tails";
$langDocumentsDetails = "Documents t�l�charg�s par le membre";
$langExercicesDetails = "R�sultats des tests effectu�s";

$langBackToList = "Retourner � la liste des membres";
$langDetails = "D�tails";
$langClose = "Fermer";

/* subtitles */
$langStatsOfCourse = "Statistiques de l'espace";
$langStatsOfUser = "Suivi d'un membre";
$langStatsOfPortail = "Statistiques du portail";
/* espacee */
$langCountUsers = "Nombre de membres inscrits";

/* espacee access */
$langCountToolAccess = "Nombre total de connexions � cet espace";

/* logins */
$langLoginsTitleMonthColumn = "Mois";
$langLoginsTitleCountColumn = "Nombre de logins";

/* tools */
$langToolTitleToolnameColumn = "Nom de l'outil";
$langToolTitleUsersColumn = "Clics des inscrits";
$langToolTitleCountColumn = "Total des clics";

/* links*/
$langLinksTitleLinkColumn = "Lien";
$langLinksTitleUsersColumn = "Clics des inscrits";
$langLinksTitleCountColumn = "Total des clics";

/* exercices */
$langExercicesTitleExerciceColumn = "Test";
$langExercicesTitleScoreColumn = "R�sultat";

/* documents */
$langDocumentsTitleDocumentColumn = "Document";
$langDocumentsTitleUsersColumn = "T�l�chargements des inscrits";
$langDocumentsTitleCountColumn = "Total des t�l�chargements";


/* scorm */
$langScormContentColumn="Titre";
$langScormStudentColumn="Membres";
$langScormTitleColumn="Le�on";
$langScormStatusColumn="Statut";
$langScormScoreColumn="Points";
$langScormTimeColumn="Dur�e";
$langScormNeverOpened="Ce espace n'a jamais �t� ouvert par le membre.";


/* works */
$langWorkTitle = "Titre";
$langWorkAuthors = "Auteurs";
$langWorkDescription = "Description";

$langDate = "Date";

/* user list */
$informationsAbout = "Suivi de";
$langUserName = "Pseudo";
$langFirstName = "Nom";
$langLastName = "Pr�nom";
$langEmail = "Email";
$langNoEmail = "Pas d'adresse email";
/* others */
$langNoResult = "Pas de r�sultat";

$langCourse = "Espace";

$langHits = "Hits";
$langTotal = "Total";
$langHour = "Heure";
$langDay = "Jour";
$langLittleHour = "h.";
$langLast31days = "Ces derniers 31 jours";
$langLast7days = "Ces derniers 7 jours";
$langThisday  = "Aujourd'hui";

/* perso stats */
$langLogins = "Derniers logins";
$langLoginsExplaination = "Voici la liste de vos derniers logins ainsi que les outils utilis�s pendant ces sessions.";

$langExercicesResults = "R�sultats des tests effectu�s";

$langVisits = "visites";
$langAt = "�";
$langLoginTitleDateColumn = "Date";
$langLoginTitleCountColumn = "Visites";

/* tutor view */
$langLoginsAndAccessTools = "Logins et acc�s aux outils";
$langWorkUploads = "Contributions envoy�es";
$langErrorUserNotInGroup = "Ce membre n'est pas dans votre groupe." ;
$langListStudents = "Liste des membres de ce groupe";

/* details page */
$langPeriodHour = "Heure";
$langPeriodDay = "Jour";
$langPeriodWeek = "Semaine";
$langPeriodMonth = "Mois";
$langPeriodYear = "Ann�e";

$langNextDay = "Jour suivant";
$langPreviousDay = "Jour pr�c�dent";
$langNextWeek = "Semaine suivante";
$langPreviousWeek = "Semaine pr�c�dente";
$langNextMonth = "Mois suivant";
$langPreviousMonth = "Mois pr�c�dent";
$langNextYear = "Ann�e suivante";
$langPreviousYear = "Ann�e pr�c�dente";

$langViewToolList = "Voir la liste de tous les outils";
$langToolList = "Liste de tous les outils";

$langFrom = "Du";
$langTo = "au";


/* traffic_details */
$langPeriodToDisplay = "P�riode";
$langDetailView = "Niveau de d�tail";

/* for interbredcrumps */
$langBredCrumpGroups = "Groupes";
$langBredCrumpGroupSpace = "Espace de groupe";
$langBredCrumpUsers = "Membres";

/* admin stats */
$langAdminToolName = "Statistiques d'administration";
$langPlatformStats = "Statistiques du portail";
$langStatsDatabase = "Statistiques de la base de donn�es";
$langPlatformAccess = "Acc�s au portail";
$langPlatformCoursesAccess = "Acc�s aux espace";
$langPlatformToolAccess = "Acc�s aux outils";
$langHardAndSoftUsed = "Pays Fournisseurs d'acc�s Navigateurs Os R�f�rants";
$langStrangeCases = "Cas particuliers";
$langStatsDatabaseLink = "Cliquez ici";
$langCountCours = "Nombre d'espaces";
$langCountUsers = "Nombre de membres";
$langCountCourseByFaculte  = "Nombre d'espaces par cat�gorie";
$langCountCourseByLanguage = "Nombre d'espaces par langue";
$langCountCourseByVisibility = "Nombre d'espaces par visibilit�";
$langCountUsersByCourse = "Nombre d'utilisateurs par espace";
$langCountUsersByFaculte = "Nombre de membres par cat�gorie";
$langCountUsersByStatus = "Nombre de membres par statut";
$langCourses = "Espaces";
$langUsers = "Membres";
$langAccess = "Acc�s";
$langCountries = "Pays";
$langProviders = "Fournisseurs d'acc�s";
$langOS = "OS";
$langBrowsers = "Navigateurs";
$langReferers = "R�f�rants";
$langAccessExplain = "Lorsqu'un membre acc�de au portail";
$langLogins = "Logins";
$langTotalPlatformAccess = "Total";
$langTotalPlatformLogin = "Total";
$langMultipleLogins = "Comptes avec le m�me <i>nom d'utilisateur</i>";
$langMultipleUsernameAndPassword = "Comptes avec le m�me <i>pseudo</i> et <i>mot de passe</i>";
$langMultipleEmails = "Comptes avec le m�me <i>Email</i>";
$langCourseWithoutProf = "Espace sans responsable";
$langCourseWithoutAccess = "Espaces inutilis�s";
$langLoginWithoutAccess  = "Comptes inutilis�s";
$langAllRight = "Tout va bien.";
$langDefcon = "Aie , cas particuliers d�tect�s !";
$langNULLValue = "Vide (ou <i>NULL</i>)";
$langTrafficDetails = "D�tails du trafic";

$langSeeIndividualTracking= "Pour le suivi individuel, voir l'outil <a href=../user/user.php>Membres</a>.";





?>

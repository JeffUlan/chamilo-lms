<?php // $Id: admin.inc.php 3402 2005-02-17 11:44:22Z olivierb78 $
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.4.* $Revision: 3402 $                            |
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2003 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
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
$langSendMailToUsers = "Envoyer un mail aux utilisateurs";

$langExampleXMLFile = "Exemple de fichier XML";
$langExampleCSVFile = "Exemple de fichier CSV";

$langCourseBackup="Sauvegarder (archiver) ce cours";

$langCourseCode="Code du cours";
$langCourseTitular="Responsable du cours";
$langCourseTitle="Intitul� du cours";
$langCourseFaculty="Cat�gorie du cours";
$langCourseDepartment="D�partement du cours";
$langCourseDepartmentURL="URL du d�partement";
$langCourseLanguage="Langue du cours";
$langCourseAccess="Acc�s � ce cours";
$langCourseSubscription="Inscription au cours";
$langPublicAccess="Acc�s public";
$langPrivateAccess="Acc�s priv�";
$langFromHomepageWithoutLogin="depuis la page d'accueil du portail, sans identifiant";
$langSiteReservedToPeopleInMemberList="site r�serv� aux personnes figurant dans la liste membres";
$langCode="Code";
$langUsers="Utilisateurs";
$langLanguage="Langue";
$langCategory="Cat�gorie";

$langClassName="Nom de la classe";

$langDBManagementOnlyForServerAdmin="La gestion des bases de donn�es n'est accessible qu'� l'administrateur du serveur";

$langShowUsersOfCourse="Afficher les utilisateurs inscrits au cours";
$langShowClassesOfCourse="Afficher les classes inscrites au cours";
$langShowGroupsOfCourse="Afficher les groupes du cours";
$langOfficialCode="Code officiel";
$langFirstName="Pr�nom";
$langLastName="Nom";
$langLoginName="Identifiant";
$langPhone="T�l�phone";
$langPhoneNumber="Num�ro de t�l�phone";
$langStatus="Statut";
$langEmail="Adresse e-mail";
$langPlatformAdmin="Administrateur de la plateforme";
$langActions="Actions";
$langAddToCourse="Inscrire � un cours";
$langDeleteFromPlatform="Supprimer de la plateforme";
$langDeleteCourse="Supprimer ce(s) cours";
$langDeleteFromCourse="D�sinscrire de ce(s) cours";
$langDeleteSelectedClasses="Supprimer les classes s�lectionn�es";
$langDeleteSelectedGroups="Supprimer les groupes s�lectionn�s";
$langAdministrator="Administrateur";
$langTeacher="Enseignant/Chef";
$langUser="Utilisateur";
$langAddPicture="Ajouter une photo";
$langChangePicture="Changer la photo";
$langDeletePicture="Supprimer la photo";
$langAddUsers="Ajouter des utilisateurs";
$langAddGroups="Ajouter des groupes";
$langAddClasses="Ajouter des classes";
$langAddCourse="Cr�er un cours";
$langExportUsers="Exporter les utilisateurs";
$langKeyword="Mot-cl�";
$langGroupName="Nom du groupe";
$langGroupTutor="Mod�rateur du groupe";
$langGroupForum="Forum du groupe";
$langGroupDescription="Description du groupe";
$langNumberOfParticipants="Nombre de participants";
$langNumberOfUsers="Nombre d'utilisateurs";
$langMaximum="maximum";
$langMaximumOfParticipants="Nombre maximum de participants";
$langParticipants="participants";
$langGroup="Groupe";

$langFirstLetterClass="Premi�re lettre (classe)";
$langFirstLetterUser="Premi�re lettre (nom)";
$langFirstLetterCourse="Premi�re lettre (code)";

$langCatCodeAlreadyUsed="Une cat�gorie porte d�j� ce code !";
$langPleaseEnterCategoryInfo="Veuillez introduire le code et le nom de la cat�gorie !";

$langModifyUserInfo="Modifier les informations d'un utilisateur";
$langModifyClassInfo="Modifier les informations d'une classe";
$langModifyGroupInfo="Modifier les informations d'un groupe";
$langModifyCourseInfo="Modifier les informations d'un cours";
$langPleaseEnterClassName="Veuillez introduire le nom de la classe !";
$langPleaseEnterLastName="Veuillez introduire le nom de l'utilisateur !";
$langPleaseEnterFirstName="Veuillez introduire le pr�nom de l'utilisateur !";
$langPleaseEnterValidEmail="Veuillez introduire une adresse e-mail valide !";
$langPleaseEnterValidLogin="Veuillez introduire un identifiant valide !";
$langPleaseEnterCourseCode="Veuillez introduire le code du cours !";
$langPleaseEnterTitularName="Veuillez introduire le nom du responsable !";
$langPleaseEnterCourseTitle="Veuillez introduire l'intitul� du cours !";
$langAcceptedPictureFormats="Les formats accept�s sont JPG, PNG et GIF !";
$langLoginAlreadyTaken="Cet identifiant est d�j� pris !";

$langImportUserListXMLCSV="Importer une liste d'utilisateurs au format XML/CSV";
$langExportUserListXMLCSV="Exporter la liste des utilisateurs dans un fichier XML/CSV";
$langOnlyUsersFromCourse="Seulement les utilisateurs du cours";
$langUserListHasBeenExportedTo="La liste des utilisateurs a �t� export�e vers";

$langAddClassesToACourse="Inscrire des classes d'utilisateurs � un cours";
$langAddUsersToACourse="Inscrire des utilisateurs � un cours";
$langAddUsersToAClass="Inscrire des utilisateurs dans une classe";
$langAddUsersToAGroup="Inscrire des utilisateurs � un groupe";
$langAtLeastOneClassAndOneCourse="Vous devez s�lectionner au moins une classe et un cours !";
$langAtLeastOneUser="Vous devez s�lectionner au moins un utilisateur !";
$langAtLeastOneUserAndOneCourse="Vous devez s�lectionner au moins un utilisateur et un cours !";
$langClassList="Liste des classes";
$langUserList="Liste des utilisateurs";
$langCourseList="Liste des cours";
$langAddToThatCourse="Inscrire � ce(s) cours";
$langAddToClass="Inscrire dans la classe";
$langRemoveFromClass="D�sinscrire de la classe";
$langAddToGroup="Inscrire au groupe";
$langRemoveFromGroup="D�sinscrire du groupe";

$langUsersOutsideClass="Utilisateurs en dehors de la classe";
$langUsersInsideClass="Utilisateurs dans la classe";
$langUsersOutsideGroup="Utilisateurs en dehors du groupe";
$langUsersInsideGroup="Utilisateurs dans le groupe";

$langImportFileLocation="Emplacement du fichier CSV / XML";
$langFileType="Type du fichier";
$langOutputFileType="Type du fichier de destination";
$langMustUseSeparator="doit utiliser le caract�re ';' comme s�parateur";
$langCSVMustLookLike="Le fichier CSV doit �tre dans le format suivant";
$langXMLMustLookLike="Le fichier XML doit �tre dans le format suivant";
$langMandatoryFields="les champs en <b>gras</b> sont obligatoires";
$langNotXML="Le fichier sp�cifi� n'est pas au format XML !";
$langNotCSV="Le fichier sp�cifi� n'est pas au format CSV !";
$langNoNeededData="Le fichier sp�cifi� ne contient pas toutes les donn�es n�cessaires !";
$langMaxImportUsers="Vous ne pouvez pas importer plus de 500 utilisateurs � la fois !";

$langAdminDatabases="Bases de donn�es (phpMyAdmin)";
$langAdminUsers="Utilisateurs";
$langAdminClasses="Classes d'utilisateurs";
$langAdminGroups="Groupes d'utilisateurs";
$langAdminCourses="Cours";
$langAdminCategories="Cat�gories de cours";
$langSubscribeUserGroupToCourse="Inscrire un utilisateur / groupe � un cours";
$langAddACategory="Ajouter une cat�gorie";
$langInto="dans";
$langNoCategories="Il n'y a aucune cat�gorie ici";
$langAllowCoursesInCategory="Permettre l'ajout de cours dans cette cat�gorie ?";
$langGoToForum="Aller sur le forum";

$langCategoryCode="Code de la cat�gorie";
$langCategoryName="Nom de la cat�gorie";

$langCourses="cours";
$langCategories="cat�gories";

$langEditNode = "Modifier cette cat�gorie";
$langOpenNode = "Ouvrir cette cat�gorie";
$langDeleteNode = "Supprimer cette cat�gorie";
$langAddChildNode ="Ajouter une sous-cat�gorie";
$langViewChildren = "Voir les fils";
$langTreeRebuildedIn = "Arborescence reconstruite en";
$langTreeRecountedIn = "Arborescence recompt�e en";
$langRebuildTree="Reconstruire l'arborescence";
$langRefreshNbChildren="Raffraichir le nombre de fils";
$langShowTree = "Voir l'arborescence";
$langBack = "Retour en arri�re";
$langLogDeleteCat  = "Cat�gorie supprim�e";
$langRecountChildren = "Recompter les fils";
$langUpInSameLevel ="Monter au m�me niveau";

$langSeconds="secondes";
$langIn="Dans";

$langMailTo = "Contact : ";
$lang_no_access_here ="Pas d'acc�s ";
$lang_php_info = "information sur le syst�me php";


$langAdminBy = "Administration  par";
$langAdministrationTools = "Outils d'administration";
$langTools = "Outils";
$langTechnicalTools = "administration technique";
$langConfig = "Configuration du syst�me";
$langState = "Etat du syst�me";
$langDevAdmin ="Administration du d�veloppement";
$langLinksToClaroProjectSite ="Liens vers le site du projet";
$langNomOutilTodo 		= "Gestion des suggestions"; // to do
$langNomPageAdmin 		= "Administration";
$langSysInfo  			= "Info Syst�me";        // Show system status
$langCheckDatabase  	= "V�rificateur d'�tat des bases";        // Check Database
$langDiffTranslation 	= "Comparaison des traductions"; // diff of translation
$langStatOf 			= "Statistiques de "; // Stats of...
$langSpeeSubscribe 		= "Inscription Rapide comme Testeur d'un cours";
$langLogIdentLogout 	= "Liste des logins";
$langLogIdentLogoutComplete = "Liste �tendue des logins";

// Stat
$langStatistiques = "Statistiques";

$langNbProf = "Nombre de responsables";
$langNbStudents = "Nombre de membres";
$langNbLogin = "Nombre de login";
$langToday   ="Aujourd'hui";
$langLast7Days ="Ces 7 derniers jours";
$langLast30Days ="Ces 30 derniers jours";


$langNbAnnoucement = "Nombre d'annonces";


// Check data base
$langCheckDatabase ="Analyse de la Base de donn�es";

// Check Data base
$langPleaseCheckConfigForMainDataBaseName = "Verifiez les variables
<br>
Nom de base de donn�e dans
<br>";
$langBaseFound ="trouv�e
<br>
V�rification des tables de cette base";
$langNeeded = "obligatoire";
$langNotNeeded = "non exig�";
$langArchive   ="archive";
$langUsed      ="utilis�";
$langPresent  ="Ok";
$langCreateMissingNow = "Voulez vous cr�er les tables manquantes maintenant ?";
$langMissing   ="manquant";
$langCheckingCourses ="V�rification des espaces";
$langExist     ="existe";


// create  Claro table

$langCreateClaroTables ="Creation des tables de la base principale";
$langTableStructureDontKnow ="Structure of this table unknown";

$langSETLOCALE="FRENCH";
// UNIX TIME SETTINGS, "15h00" instead of "3pm", for instance, "ENGLISH" is a possibility
$langManage				= "Gestion du portail";


$langMaintenance 	= "Maintenance";
$langUpgrade		= "Upgrade de la plateforme";
$langWebsite		= "Dokeos website";
$langDocumentation	= "Documentation";
$langForum			= "Forum";
$langContribute		= "Contribute";

$langStatistics = "Statistiques";
$langYourDokeosUses = "Votre installation de Dokeos utilise actuellement";
$langOnTheHardDisk = "sur le disque dur";
?>
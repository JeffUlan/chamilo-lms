<?php // $Id: registration.inc.php 3 2004-01-23 14:01:45Z olivierb78 $

/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.4.0 $Revision: 3 $
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2002 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      +----------------------------------------------------------------------+
 */
// user management

// lang vars
$langAdminOfCourse		= "admin";  //
$langSimpleUserOfCourse = "normal"; // strings for synopsis
$langIsTutor  			= "tuteur"; //

$langCourseCode			= "Cours";	// strings for list Mode
$langParamInTheCourse 	= "Statut"; //

$langAddNewUser = "Ajouter un utilisateur au syst�me";
$langMember ="inscrit";

$langDelete	="supprimer";
$langLock	= "bloquer";
$langUnlock	= "liberer";
// $langOk

$langHaveNoCourse = "Pas de Cours";

$langFirstname = "Prenom";
$langLastname = "Nom";
$langEmail = "Adresse de courrier �lectronique";
$langRetrieve ="Retrouver  mes param�tres d'identification";
$langMailSentToAdmin = "Un email � �t� adress� � l'administrateur.";
$langAccountNotExist = "Ce compte semble ne pas exister.<BR>".$langMailSentToAdmin." Il fera une recherche manuelle.<BR><BR>";
$langAccountExist = "Ce compte semble exister.<BR> Un email � �t� adress� � l'administrateur. <BR><BR>";
$langWaitAMailOn = "Attendez vous � une r�ponse sur ";
$langCaseSensitiveCaution = "Le syst�me fait la diff�rence entre les minuscules et les majuscules.";
$langDataFromUser = "Donn�es envoy�es par l'utilisateur";
$langDataFromDb = "Donn�es correspondantes dans la base de donn�e";
$langLoginRequest = "Demande de login";
$langExplainFormLostPass = "Entrez ce que  vous pensez avoir  introduit comme donn�es lors de votre inscription.";
$langTotalEntryFound = " Nombre d'entr�e trouv�es";
$langEmailNotSent = "Quelque chose n'as pas fonctionn�, veuillez envoyer ceci �";
$langYourAccountParam = "Voici vos param�tres de connection";
$langTryWith ="essayez avec ";
$langInPlaceOf ="au lieu de";
$langParamSentTo = "Vos param�tres de connection sont envoy�s sur l'adresse";



// REGISTRATION - AUTH - inscription.php
$langRegistration="Inscription";
$langName="Nom";
$langSurname="Pr�nom";
$langUsername="Nom d'utilisateur";
$langPass="Mot de passe";
$langConfirmation="confirmation";
$langStatus="Action";
$langRegStudent="M'inscrire � des cours";
$langRegAdmin="Cr�er des sites de cours";
$langTitular = "Titulaire";
// inscription_second.php


$langRegistration="Inscription";
$langPassTwice="Vous n'avez pas tap� deux fois le m�me mot de passe.
Utilisez le bouton de retour en arri�re de votre navigateur
et recommencez.";

$langEmptyFields="Vous n'avez pas rempli tous les champs.
Utilisez le bouton de retour en arri�re de votre navigateur et recommencez.";

$langPassTooEasy ="Ce mot de passe est trop simple. Choisissez un autre password  comme par exemple : ";

$langUserFree="Le nom d'utilisateur que vous avez choisi est d�j� pris.
Utilisez le bouton de retour en arri�re de votre navigateur
et choisissez-en un autre.";

$langYourReg="Votre inscription sur";
$langDear="Cher(�re)";
$langYouAreReg="Vous �tes inscrit(e) sur";
$langSettings="avec les param�tre suivants:\nNom d'utilisateur:";
$langAddress="L'adresse de";
$langIs="est";
$langProblem = "En cas de probl�me, n'h�sitez pas � prendre contact avec nous";
$langFormula="Cordialement";
$langManager="Responsable";
$langPersonalSettings="Vos coordonn�es personnelles ont �t� enregistr�es et un email vous a �t� envoy�
pour vous rappeler votre nom d'utilisateur et votre mot de passe.</p>";
$langNowGoChooseYourCourses ="Vous  pouvez maintenant aller s�lectionner les cours auxquels vous souhaitez avoir acc�s.";
$langNowGoCreateYourCourse  ="Vous  pouvez maintenant aller cr�er votre cours";
$langYourRegTo="Vos modifications";
$langIsReg="Vos modifications ont �t� enregistr�es";
$langCanEnter="Vous pouvez maintenant <a href=../../index.php>entrer dans le campus</a>";

// profile.php

$langModifProfile="Modifier mon profil";
$langPassTwo="Vous n'avez pas tap� deux fois le m�me mot de passe";
$langAgain="Recommencez!";
$langFields="Vous n'avez pas rempli tous les champs";
$langUserTaken="Le nom d'utilisateur que vous avez choisi est d�j� pris";
$langEmailWrong="L'adresse email que vous avez introduite n'est pas compl�te
ou contient certains caract�res non valides";
$langProfileReg="Votre nouveau profil a �t� enregistr�";
$langHome="Retourner � l'accueil";
$langMyStats = "Voir mes statistiques";


// user.php

$langUsers="Utilisateurs";
$langModRight="Modifier les droits de : ";
$langNone="non";
$langAll="oui";
$langNoAdmin="n'a d�sormais <b>aucun droit d'administration sur ce site</b>";
$langAllAdmin="a d�sormais <b>tous les droits d'administration sur ce site</b>";
$langModRole="Modifier le r�le de";
$langRole="R�le (facultatif)";
$langIsNow="est d�sormais";
$langInC="dans ce cours";
$langFilled="Vous n'avez pas rempli tous les champs.";
$langUserNo="Le nom d'utilisateur que vous avez choisi";
$langTaken="est d�j� pris. Choisissez-en un autre.";
$langOneResp="L'un des responsables du cours";
$langRegYou="vous a inscrit sur";
$langTheU="L'utilisateur";
$langAddedU="a �t� ajout�. Si vous avez introduit son adresse, un 
			message lui a �t� envoy� pour lui communiquer son nom d'utilisateur";
$langAndP="et son mot de passe";
$langDereg="a �t� d�sinscrit de ce cours";
$langAddAU="Ajouter des utilisateurs";
$langStudent="participant";
$langBegin="d�but";
$langPreced50="50 pr�c�dents";
$langFollow50="50 suivants";
$langEnd="fin";
$langAdmR="Admin";
$langUnreg="D�sinscrire";
$langAddHereSomeCourses = "<font size=2 face='arial, helvetica'><big>Mes cours</big><br><br>
			Cochez les cours que vous souhaitez suivre et d�cochez ceux que vous 
			ne voulez plus suivre (les cours dont vous �tes responsable 
			ne peuvent �tre d�coch�s). Cliquez ensuite sur Ok en bas de la liste.";

$langCanNotUnsubscribeYourSelf = "Vous ne pouvez pas vous d�sinscrire
				vous-m�me d'un cours dont vous �tes administrateur. 
				Seul un autre administrateur du cours peut le faire.";

$langGroup="Groupe";
$langUserNoneMasc="-";

$langTutor="Tuteur";
$langTutorDefinition="Tuteur (droit de superviser des groupes)";
$langAdminDefinition="Administrateur (droit de modifier le contenu du site)";
$langDeleteUserDefinition="D�sinscrire (supprimer de la liste des utilisateurs de <b>ce</b> cours)";
$langNoTutor = "n'est pas tuteur pour ce cours";
$langYesTutor = "est tuteur dans ce cours";
$langUserRights="Droits des utilisateurs";
$langNow="actuellement";
$langOneByOne="Ajouter manuellement un utilisateur";
$langUserMany="Importer une liste d'utilisateurs via un fichier texte";
$langNo="non";
$langYes="oui";
$langUserAddExplanation="Chaque ligne du fichier � envoyer 
		contiendra n�cessairement et uniquement les 
		5 champs <b>Nom&nbsp;&nbsp;&nbsp;Pr�nom&nbsp;&nbsp;&nbsp;
		Nom d'utilisateur&nbsp;&nbsp;&nbsp;Mot de passe&nbsp;
		&nbsp;&nbsp;Courriel</b> s�par�s par des tabulations 
		et pr�sent�s dans cet ordre. Les utilisateurs recevront 
		par courriel nom d'utilisateur et mot de passe.";
$langSend="Envoyer";
$langDownloadUserList="Envoyer la liste";
$langUserNumber="nombre";
$langGiveAdmin="Rendre admin";
$langRemoveRight="Retirer ce droit";
$langGiveTutor="Rendre tuteur";
$langUserOneByOneExplanation="Il recevra par courriel nom d'utilisateur et mot de passe";
$langBackUser="Retour � la liste des utilisateurs";
$langUserAlreadyRegistered="Un utilisateur ayant m�mes nom et pr�nom est d�j� inscrit dans le cours.";

$langAddedToCourse="a �t� inscrit � votre site";

$langGroupUserManagement="Gestion des groupes";

$langIfYouWantToAddManyUsers="Si vous voulez ajouter une liste d'utilisateurs � votre site, 
		contactez votre web administrateur.";

$langCourses="cours.";
$langLastVisits="Mes derni�res visites";

$langSee		= "Voir";
$langSubscribe	= "M'inscrire<br>coch�&nbsp;=&nbsp;oui";
$langCourseName	= "Nom&nbsp;du&nbsp;cours";
$langLanguage	= "Langue";

$langConfirmUnsubscribe = "Confirmez la d�sincription de cet utilisateur";
$langAdded = "Ajout�s";
$langDeleted = "Supprim�s";
$langPreserved = "Conserv�s";
$langDate = "Date";
$langAction = "Action";
$langCourseManager = "Gestionnaire du site";
$langManage				= "Gestion du portail";
$langAdministrationTools = "Outils d'administration";

?>
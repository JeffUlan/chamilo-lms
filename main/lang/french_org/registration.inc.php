<?php # $Id: registration.inc.php 1996 2004-07-07 14:53:05Z olivierb78 $
/*
==============================================================================
	Dokeos - elearning and espacee management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================

	  +----------------------------------------------------------------------+
	  | Translator :                                                         |
	  |          Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
	  |          Andrew Lynn       <Andrew.Lynn@strath.ac.uk>                |
	  +----------------------------------------------------------------------+
 */

$langCourseAdministratorOnly = "R�serv� au responsable";
$langDefineHeadings = "D�finir les intitul�s";

// lang vars
$langAdminOfCourse		= "admin";  //
$langSimpleUserOfCourse = "normal"; // strings for synopsis
$langIsTutor  			= "mod&eacute;rateur"; //

$langCourseCode			= "Espace";	// strings for list Mode
$langParamInTheCourse 	= "Statut"; //

$langSummaryTable = "Cette table dresse la liste des membres de l'espace.";
$langSummaryNavBar = "Barre de navigation";
$langAddNewUser = "Ajouter un membre";
$langMember ="inscrit";

$langDelete	="supprimer";
$langLock	= "bloquer";
$langUnlock	= "lib�rer";

$langHaveNoCourse = "Pas d'espaces";

$langFirstname = "Pr�nom";
$langLastname = "Nom";
$langEmail = "Adresse e-mail";
$langAbbrEmail = "Email";
$langRetrieve ="Retrouver mes param�tres d'identification";
$langMailSentToAdmin = "Un email � �t� adress� � l'administrateur du syst�me.";
$langAccountNotExist = "Ce compte semble ne pas exister.<BR>".$langMailSentToAdmin." Il fera une recherche manuelle.<BR><BR>";
$langAccountExist = "Ce compte semble exister.<BR> Un email � �t� adress� � l'administrateur. <BR><BR>";
$langWaitAMailOn = "Attendez vous � une r�ponse sur ";
$langCaseSensitiveCaution = "Le syst�me fait la diff�rence entre les minuscules et les majuscules.";
$langDataFromUser = "Donn�es envoy�es par le membre";
$langDataFromDb = "Donn�es correspondantes dans la base de donn�e";
$langLoginRequest = "Demande de login";
$langExplainFormLostPass = "Entrez ce que  vous pensez avoir  introduit comme donn�es lors de votre inscription.";
$langTotalEntryFound = " Nombre d'entr�e trouv�es";
$langEmailNotSent = "Quelque chose n'as pas fonctionn�, veuillez envoyer ceci �";
$langYourAccountParam = "Voici les param�tres qui vous permettront de vous connecter sur";
$langTryWith ="essayez avec ";
$langInPlaceOf ="au lieu de";
$langParamSentTo = "Vos param�tres de connexion sont envoy�s sur l'adresse";



// REGISTRATION - AUTH - inscription.php
$langRegistration="Inscription";
$langName=$langFirstname;
$langSurname=$langLastname;
$langUsername="Identifiant";
$langPass="Mot de passe";
$langConfirmation="Confirmation";
$langStatus="Statut";
$langRegStudent="M'inscrire � des espaces";
$langRegAdmin="Cr�er des espaces";
$langTitular = "Responsable";
// inscription_second.php


$langRegistration = "Inscription";
$langPassTwice    = "Vous n'avez pas tap� deux fois le m�me mot de passe.
Utilisez le bouton de retour en arri�re de votre navigateur
et recommencez.";

$langEmptyFields = "Vous n'avez pas rempli tous les champs.
Utilisez le bouton de retour en arri�re de votre navigateur et recommencez.";

$langPassTooEasy ="Ce mot de passe est trop simple. Veuillez choisir un autre mot de passe comme par exemple";

$langUserFree    = "L'identifiant que vous avez choisi est d�j� pris.
Utilisez le bouton de retour en arri�re de votre navigateur
et choisissez-en un autre.";

$langYourReg                = "Votre inscription sur";
$langDear                   = "Cher(�re)";
$langYouAreReg              = "Vous �tes inscrit(e) sur";
$langSettings               = "avec les param�tres suivants :\n\nIdentifiant :";
$langAddress                = "L'adresse de";
$langIs                     = "est";
$langProblem                = "En cas de probl�me, n'h�sitez pas � prendre contact avec nous";
$langFormula                = "Cordialement";
$langManager                = "Responsable";
$langPersonalSettings       = "Vos coordonn�es personnelles ont �t� enregistr�es";
$langMailHasBeenSent		= "Un email vous a �t� envoy� pour vous rappeler votre identifiant et votre mot de passe";
$langNowGoChooseYourCourses ="Vous pouvez maintenant aller s�lectionner les espacesauxquels vous souhaitez avoir acc�s";
$langNowGoCreateYourCourse  = "Vous pouvez maintenant cr�er votre espace";
$langYourRegTo              = "Vos modifications";
$langIsReg                  = "Vos modifications ont �t� enregistr�es";
$langCanEnter               = "Vous pouvez maintenant <a href=../../index.php>entrer dans le portail</a>";

// profile.php

$langModifProfile = "Modifier mon profil";
$langViewProfile  = "Voir mon profil (non modifiable)";
$langPassTwo      = "Vous n'avez pas introduit deux fois le m�me mot de passe";
$langAgain        = "Veuillez r�essayer ...";
$langFields       = "Vous n'avez pas rempli tous les champs";
$langUserTaken    = "L'identifiant que vous avez choisi est d�j� pris";
$langEmailWrong   = "L'adresse email que vous avez introduite n'est pas compl�te
ou contient certains caract�res non valides";
$langProfileReg   = "Votre nouveau profil a �t� enregistr�";
$langHome         = "Retourner � l'accueil";
$langMyStats      = "Voir mes statistiques";


// user.php

$langUsers    = "Membres";
$langModRight ="Modifier les droits de : ";
$langNone     ="non";
$langAll      ="oui";

$langNoAdmin            = "n'a d�sormais <b>aucun droit de responsable dans cet espace</b>";
$langAllAdmin           = "a d�sormais <b>tous les droits de responsable dans cet espace</b>";
$langModRole            = "Modifier la description de";
$langRole               = "Descriptif";
$langIsNow              = "est d�sormais";
$langInC                = "dans cet espace";
$langFilled             = "Vous n'avez pas rempli tous les champs.";
$langUserNo             = "L'identifiant que vous avez choisi";
$langTaken              = "est d�j� pris. Choisissez-en un autre.";
$langOneResp            = "L'un des responsables de cet espace";
$langRegYou             = "vous a inscrit sur";
$langTheU               ="Le membre";
$langAddedU             ="a �t� ajout�. Si vous avez introduit son adresse, un message lui a �t� envoy� pour lui communiquer son identifiant";
$langAndP               = "et son mot de passe";
$langDereg              = "a �t� d�sinscrit de cet espace";
$langAddAU              = "Ajouter des membres";
$langImportUserList		= "Importer une liste d'utilisateurs";
$langStudent            = "membre";
$langBegin              = "d�but";
$langPreced50           = "50 pr�c�dents";
$langFollow50           = "50 suivants";
$langEnd                = "fin";
$langAdmR               = "Admin";
$langUnreg              = "D�sinscrire";
$langAddHereSomeCourses = "<font size=2 face='arial, helvetica'><big>Mes espaces</big><br><br>
			Cochez les espacesauxquels vous souhaitez participer et d�cochez ceux auxquels vous
			ne voulez plus participer (les espacesdont vous �tes responsable
			ne peuvent �tre d�coch�s). Cliquez ensuite sur Ok en bas de la liste.";

$langCanNotUnsubscribeYourSelf = "Vous ne pouvez pas vous d�sinscrire
				vous-m�me d'un espace dont vous �tes responsable.
				Seul un autre responsable peut le faire.";

$langGroup="Groupe";
$langUserNoneMasc="-";

$langTutor                = "Mod�rateur";
$langTutorDefinition      = "Mod�rateur (droit de superviser des groupes)";
$langAdminDefinition      = "Responsable (droit de modifier le contenu de l'espace)";
$langDeleteUserDefinition ="D�sinscrire (supprimer de la liste des membres de <b>cet</b> espace)";
$langNoTutor              = "n'est pas mod�rateur pour cet espace";
$langYesTutor             = "est mod�rateur pour cet espace";
$langUserRights           = "Droits des membres";
$langNow                  = "actuellement";
$langOneByOne             = "Ajouter manuellement un utilisateur";
$langUserMany             = "Importer une liste de membres via un fichier CSV / XML";
$langNo                   = "non";
$langYes                  = "oui";

$langUserAddExplanation   = "Chaque ligne du fichier � envoyer
		contiendra n�cessairement et uniquement les
		5 champs <b>Nom&nbsp;&nbsp;&nbsp;Pr�nom&nbsp;&nbsp;&nbsp;
		Identifiant&nbsp;&nbsp;&nbsp;Mot de passe&nbsp;
		&nbsp;&nbsp;Courriel</b> s�par�s par des tabulations
		et pr�sent�s dans cet ordre. Les membres recevront
		par courriel identifiant et mot de passe.";

$langSend             = "Envoyer";
$langDownloadUserList = "Envoyer la liste";
$langUserNumber       = "nombre";
$langGiveAdmin        = "Rendre responsable";
$langRemoveRight      = "Retirer ce droit";
$langGiveTutor        = "Rendre coach";

$langUserOneByOneExplanation = "Il recevra par courriel identifiant et mot de passe";
$langBackUser                = "Retour � la liste des membres";
$langUserAlreadyRegistered   = "Un membre ayant m�mes nom et pr�nom est d�j� inscrit dans cet espace.";

$langAddedToCourse           = "a �t� inscrit � votre espace";

$langGroupUserManagement     = "Gestion des groupes";

$langIfYouWantToAddManyUsers = "Si vous voulez ajouter une liste des membres de votre espace, contactez votre web administrateur.";

$langCourses    = "cours.";
$langLastVisits = "Mes derni�res visites";
$langSee        = "Voir";
$langSubscribe  = "M'inscrire<br>coch�&nbsp;=&nbsp;oui";
$langCourseName = "Nom de l'espace";
$langLanguage   = "Langue";

$langConfirmUnsubscribe = "Confirmez la d�sincription de ce membre";
$langAdded              = "Ajout�s";
$langDeleted            = "Supprim�s";
$langPreserved          = "Conserv�s";
$langDate               = "Date";
$langAction             = "Action";
$langLogin              = "Log In";
//$langLogout             = "Quitter";
$langModify             = "Modifier";
$langUserName           = "Nom membre";
$langEdit               = "Editer";

$langCourseManager       = "Responsable";
$langManage              = "Gestion du portail";
$langAdministrationTools = "Outils d'administration";
$langUserProfileReg	     = "La modification a �t� effectu�e";
$lang_lost_password      = "Mot de passe perdu";

$lang_enter_email_and_well_send_you_password  = "Entrez l'adresse de courrier �lectronique que vous avez utilis�e pour vous enregistrer et nous vous enverrons votre mot de passe.";
$lang_your_password_has_been_emailed_to_you   = "Votre mot de passe vous a �t� envoy� par courrier �lectronique.";
$lang_no_user_account_with_this_email_address = "Il n'y a pas de compte utilisateur avec cette adresse de courrier �lectronique.";
$langCourses4User  = "Espace pour ce membre";
$langCoursesByUser = "Vue d'ensemble des espaces par membre";

$langAddImage = "Ajoutez une photo";
$langUpdateImage = "Changer de photo";
$langDelImage = "Supprimer la photo";
$langOfficialCode = "Code officiel (ID)";

$langAuthInfo = "Param�tres de connexion";
$langEnter2passToChange = "Introduisez 2x votre mot de passe pour le modifier. Laissez les champs vides dans le cas contraire.";
$langTracking="Suivi";

$langShouldBeCSVFormat="Le fichier devrait �tre au format CSV. N'ajoutez pas d'espaces. La structure devrait �tre exactement :";
?>
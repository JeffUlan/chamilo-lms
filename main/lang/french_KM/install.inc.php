<?php // $Id: install.inc.php 950 2004-04-01 20:24:14Z olivierb78 $
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.4.0 $Revision: 950 $                             |
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2002 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
	  |   French Translation                                                |
      +----------------------------------------------------------------------+
      |   This program is free software; you can redistribute it and/or      |
      |   modify it under the terms of the GNU General Public License        |
      |   as published by the Free Software Foundation; either version 2     |
      |   of the License, or (at your option) any later version.             |
      |                                                                      |
      |   This program is distributed in the hope that it will be useful,    |
      |   but WITHOUT ANY WARRANTY; without even the implied warranty of     |
      |   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
      |   GNU General Public License for more details.                       |
      |                                                                      |
      |   You should have received a copy of the GNU General Public License  |
      |   along with this program; if not, write to the Free Software        |
      |   Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA          |
      |   02111-1307, USA. The GNU GPL license is also available through     |
      |   the world-wide-web at http://www.gnu.org/copyleft/gpl.html         |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      +----------------------------------------------------------------------+
      | Translator :                                                         |
      |          Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
      |          Andrew Lynn       <Andrew.Lynn@strath.ac.uk>                |
      +----------------------------------------------------------------------+
 */
$langEG 			= "par ex.";
$langDBHost			= "Database H�te";
$langDBLogin		= "Database User";
$langDBPassword 	= "Database Mot de passe";
$langMainDB			= "Base principale de Dokeos";
$langStatDB             = "Base pour le tracking.  Utile uniquement si vous s�parez les bases centrale et tracking";
$langEnableTracking     = "Activer le Tracking";
$langAllFieldsRequired	= "Toutes ces donn�es sont requises";
$langPrintVers			= "Version imprimable";
$langLocalPath			= "Corresponding local path";
$langAdminEmail			= "Email de l'administrateur";
$langAdminName			= "Nom de l'administrateur";
$langAdminSurname		= "Pr�nom de l'administrateur";
$langAdminLogin			= "Identifiant de l'administrator";
$langAdminPass			= "Mot de passe de l'administrator";
$langEducationManager	= "Responsable du contenu";
$langHelpDeskPhone		= "N� de t�l�phone de l'assisance technique";
$langCampusName			= "Nom du campus";
$langInstituteShortName = "Nom abr�g� de l'institution";
$langInstituteName		= "URL de l'institution";


$langDBSettingIntro		= "
				Install script will create claroline main DB. Please note that Dokeos
				will need to create many DBs. If you are allowed only one
				DB for your website by your Hosting Services, Dokeos will not work.";
$langStep1 			= "�tape 1 sur 6 ";
$langStep2 			= "�tape 2 sur 6 ";
$langStep3 			= "�tape 3 sur 6 ";
$langStep4 			= "�tape 4 sur 6 ";
$langStep5 			= "�tape 5 sur 6 ";
$langStep6 			= "�tape 6 sur 6 ";
$langCfgSetting		= "Config settings";
$langDBSetting 		= "MySQL database settings";
$langMainLang 		= "Langue principale";
$langLicence		= "Licence";
$langLastCheck		= "Last check before install";
$langRequirements	= "Requirements";

$langDbPrefixForm	= "Prefix pour le nom de base MySQL";
$langDbPrefixCom	= "Laissez vide si non requis";
$langEncryptUserPass	= "Crypter les mots de passes des utilisateur dans la base de donn�es";
$langSingleDb	= "Use one or several DB for Dokeos";

$langWarningResponsible = "Utilisez ce script apr�s avoir fait un backup. Nous ne pourrons �tre tenu responsable pour tout probl�me qui vous ferai perdre des donn�es.";
$langAllowSelfReg	=	"Auto-inscription autoris�e";
$langRecommended	=	"(recomamnd�)";


?>

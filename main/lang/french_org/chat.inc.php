<?php
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.3.0 $Revision: 3652 $                             |
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2002 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
      |   $Id: chat.inc.php 3652 2005-03-03 18:17:59Z olivierb78 $     |
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
 */

/***************************************************************
*                   Language translation
****************************************************************
GOAL
****
Translate the interface in chosen language
*****************************************************************/
// chat.php

 $langChat = "Discuter";
 $langOnlineConference = "Conf�rence";
 $langWash = "Nettoyer";
 $langReset = "Effacer";
 $langSave = "Enregistrer";
 $langRefresh = "Actualiser";
 $langConfirmReset = "Etes-vous s�r de vouloir supprimer tous les messages ?";
 $langTypeMessage = "Veuillez introduire votre message !";
 $langHasResetChat = "a effac� les messages";
 $langIsNowInYourDocDir = "est maintenant dans votre outil document. <br><B>Attention il est visible pour tous</B>";
 $langCopyFailed = "La copie du fichier courrant n'a pas r�ussi...";
 $langChat_reset_by = "La discussion � �t� rafraichie par ";
 $langNoOnlineConference = "Il n'y pas de de conf�rence online pour le moment ...";
 $langMediaFile = "Fichier audio ou vid�o";
 $langContentFile = "Pr�sentation";
 $langListOfParticipants = "Liste des participants";
 $langYourPicture = "Votre photo";
 $langOnlineDescription = "Description de la conf�rence";
 $langOnlyCheckForImportantQuestion='Veuillez cocher cette case uniquement pour poser une question importante !';
 $langQuestion='question';
 $langClearList='Effacer la liste';
 $langWhiteBoard='Editeur';
 $langHome='Accueil';
 $langTextEditorDefault='<h2>Editeur de texte</h2>Coupez et collez ici un texte provenant de MS Word&reg; et �ditez-le. Les autres participants verront vos modifications en direct.';
 $langLinks='Liens';
 $langStreaming='Streaming';
 $langStreamURL='URL du stream';
 $langStreamType='Type du stream';
 $langLinkName='Nom du lien';
 $langLinkURL='URL du lien';
 $langWelcomeToOnlineConf='Bienvenue � la <b>Conf�rence en ligne</b>';
 $langNoLinkAvailable='Aucun lien disponible';
 $langOrFile="Ou fichier";
?>
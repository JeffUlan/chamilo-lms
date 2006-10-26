<?php // $Id: external_module.inc.php 3 2004-01-23 14:01:45Z olivierb78 $
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.3.0 $Revision: 3 $                             |
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
      +----------------------------------------------------------------------+
*/

/***************************************************************
*                   Language translation
****************************************************************
GOAL
****
Translate the interface in chosen language

*****************************************************************/

$langLinkSite          = "Lier � un site";
$langSubTitle          = "Ajoute un lien ou une page sur le sommaire de votre site. Astuce : si vous souhaitez ajouter un lien vers une page, allez sur cette page, copiez son URL qui se trouve dans la barre d'adresse de votre navigateur, et ins�rez-la dans le champ \"Lien\" ci-dessous.";
$langHome              = "Retour � la page d'accueil";
$langName              = "Nom";
$langLink              = "Lien";
$langAddPage           = "Ajouter une page";
$langSendPage          = "Page � envoyer";
$langCouldNot          = "Le fichier ne peut �tre envoy�";
$langOkSentPage        = "Votre page a �t� envoy�e. <p>Elle est � pr�sent accessible depuise le <a href=\"../../".$_course['path']."/index.php\">sommaire du site</a>";
$langOkSentLink        = "Votre lien a �t� ajout�. <p>Il est � pr�sent accessible depuise le <a href=\"../../".$_course['path']."/index.php\">sommaire du site</a>";
$langTooBig            = "Vous n'avez choisi aucun fichier � envoyer, ou celui-ci est trop gros";
$langExplanation       = "La page doit �tre au format HTML (ex. \"ma_page.htm\"). Elle devra pouvoir �tre accessible depuis le sommaire de votre site. Si vous souhaitez envoyer un document non HTML (PDF, Word, Power Point, Video, etc.) utilisez <a href=\"../document/document.php\">l'outil de documents</a>.";
$langPgTitle           = "Titre de la page";
?>
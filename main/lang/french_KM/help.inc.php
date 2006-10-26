<?php
/*
      +----------------------------------------------------------------------+
      | DOKEOS version 1.5.0 $Revision: 7370 $                                |      |
      +----------------------------------------------------------------------+
      |   $Id: help.inc.php 7370 2005-12-12 12:27:15Z d13tr1ch $       |
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
      | Authors: Thomas Depraetere <thomas.depraetere@dokeos.com>                |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      +----------------------------------------------------------------------+
 */

// HELP

// help.php?open=For

$langHFor="Aide forums";
$langClose="Fermer la fen�tre";



// help.php?open=For

$langForContent="Le forum est un outil de discussion asynchrone par �crit.
 A la diff�rence de l'email, le forum situe la discussion dans un espace
 public ou semi-public (� plusieurs).</p><p>Pour utiliser l'outil de forum
 de Dokeos, les membres n'ont besoin que d'un simple navigateur web
 (Netscape, Explorer...), pas besoin d'outil de courriel (Eudora,
 Outlook...).</P><p>Pour organiser les forums, cliquez sur 'administrer'.
 Les �changes sont organis�s de fa�on hi�rarchique selon l'arborescence
 suivante:</p><p><b>Cat�gorie > Forum > Sujet > R�ponse</b></p>Pour
 permettre aux membres de discuter de fa�on structur�e, il est
 indispensable d'organiser les �changes pr�alablement en cat�gories et
 forums (� eux de cr�er sujets et r�ponses). Par d�faut, le forum contient
 uniquement la cat�gorie Public, un sujet d'exemple et un message exemple.
 Vous pouvez ajouter des forums dans la cat�gorie public, ou bien modifier
 son intitul� ou encore cr�er d'autres cat�gories dans lesquelles il vous
 faudra alors cr�er de nouveaux forums. Une cat�gorie qui ne contient aucun
 forum ne s'affiche pas et est inutilisable. Si par exemple vous cr�ez une
 cat�gorie 'discussions par petits groupes', il vous faudra cr�er une s�rie
 de forums dans cette seconde cat�gorie, par exemple 'groupe 1', 'groupe
 2', 'groupe 3', etc.</p><p>La description d'un forum de groupe peut �tre
 la liste des personnes qui sont convi�es � y discuter, mais aussi une
 explication sur sa raison d'�tre. Si vous cr�ez, pour quelque raison que
 ce soit, un forum 'Appels � l'aide', vous pouvez y ajouter comme
 description: 'Signaler des difficult�s par rapport au contenu ou par
 rapport au dispositif'.";

// help.php?open=Dropbox

$langHDropbox="Partage de fichiers";

$langDropboxContent="L'outil de partage affiche les fichiers qui vous ont �t� envoy�s (la bo�te de r�ception)
et les fichiers que vous envoyez aux autres membres de ce cours (la bo�te d'envoi).
Si vous envoyez un fichier avec deux fois le m�me nom, vous pouvez �craser la version pr�c�dente avec la nouvelle.
<br><br>
En tant qu'�tudiant, vous pouvez seulement envoyer un fichier � un enseignant du cours,
� moins que l'administrateur ait activ� la communication �tudiant-vers-�tudiant.
<br><br>
Un enseignant peut envoyer un fichier � n'importe quel membre du cours.
<br><br>
L'administrateur peut activer l'upload de fichiers sans sp�cifier de destinataires.
<br><br>
Si la liste des fichiers re�us ou envoy�s devient trop longue, vous pouvez supprimer tous les fichiers de la liste,
ou juste une partie. Le fichier lui-m�me n'est pas supprim� tant que l'autre partie peut le voir.
<br>
<h5>Informations suppl�mentaires pour les enseignants</h5>
Si l'envoi de mail est activ�, vous pouvez uploader un mailing compress� au format ZIP en s�lectionnant
---Mailing--- comme destination. Ensuite, vous devriez examiner le contenu du fichier ZIP, et s'il est correct,
l'envoyer � vos destinataires.
Les 2 op�rations sont effectu�es en cliquant sur les icones � la droite du nom du fichier ZIP, pr�s de l'icone de suppression.
<br>
L'op�ration d'examen reporte l'erreur si un fichier incorrect est upload� avec
---Mailing--- comme destination. Le fichier est automatiquement supprim�.
<br><br>
Le nom du fichier ZIP doit correspondre aux noms des fichiers qu'il contient.
Par exemple, si les fichiers se nomment mailing_19970001_20040229.doc,
mailing_19981234_20040229.pdf, ..., o� 19970001 et 19981234 sont les codes officiels du cours,
alors donnez au fichier ZIP le nom mailing_STUDENTID_20040229.zip. Si les fichiers � l'int�rieur du ZIP utilisent le nom d'identification du destinataire,
alors remplacez STUDENTID par LOGINNAME dans le nom du fichier ZIP.
<br><br>
Apr�s avoir envoy� les fichiers, vous pouvez cliquer sur l'icone repr�sentant un dossier de mailing, � la gauche du nom du fichier ZIP
pour obtenir la liste des fichiers avec leurs destinataires. Comme avec les autres fichiers g�r�s par l'outil de partage, la suppression des �l�ments de la liste ou du fichier ZIP lui-m�me
ne supprime pas le fichier de la boite de r�ception du destinataire.
<br><br>
Si l'�crasement de fichiers est activ�, alors vous pouvez r�envoyer un fichier en lui donnant un nom existant.
Si le fichier ZIP contient des anciens fichiers, ils sont � nouveau envoy�s.
<br><br>
Ne mettez aucun espace dans le nom des fichiers ZIP.
<br>";

// help.php?open=Home

$langHHome="Aide Page d'accueil";

$langHomeContent="La plupart des rubriques de votre espace Dokeos sont d�j� remplies
 d'un texte ou d'un lien donn�s par d�faut ou pour l'exemple. Il vous
 revient de les modifier.</p><p>Ainsi un petit texte est l�, bien visible,
 en en-t�te de votre espace. Il commence par 'Ceci est le texte
 d'introduction de votre site...' Modifiez-le et profitez-en pour d�crire
 votre espace, vos objectifs, votre dispositif. Il y va de la bonne
 visibilit� de votre travail.</p><p>A la cr�ation de votre espace, de
 nombreux outils (Agenda, documents, tests...) sont activ�s pour vous
 par d�faut. Il vous est conseill� de d�sactiver ceux que vous n'utilisez
 pas afin de ne pas faire perdre du temps � vos membres ou � vos
 visiteurs.</p><p>Il vous revient aussi
 de d�cider si votre espace doit appara�tre dans la liste des espaces sur la page d'accueil du portail. Il est
 souhaitable qu'un espace � l'essai ou 'en chantier' n'apparaisse pas dans
 la liste (voir la fonction 'Propri�t�s de l'espace') et demeure priv� sans
 possibilit� d'inscription le temps de sa conception.</p>";

// help.php?open=Online

$langHOnline="Aide Conf�rence";
$langOnlineContent="Le syst�me de conf�rence de Dokeos vous permet d'enseigner,
 informer ou rassembler jusqu'� 200 personnes d'une fa�on simple et rapide.<br>
 <ul>
   <li><b>audio :</b> la voix du pr�sentateur est �mise en direct vers les participants
   dans une qualit� de radio FM gr�ce au streaming MP3</li>
   <li><b>contenu :</b> les participants suivent la pr�sentation sur un fichier de type Power Point
   ou n'importe quel autre document</li>
   <li><b>interaction :</b> les participants posent leurs questions au pr�sentateur � traver
   un chat web textuel</li>
 </ul>
 Le syst�me de conf�rence de Dokeos repr�sente le meilleur compromis entre faibles conditions
 techniques (impliquant bas prix, interop�rabilit�, grand nombre de participants potentiels)
 et interaction riche (question/r�ponses en temps r�el).<br><br>
 Le pr�sentateur a besoin de :<br>
 <ul>
   <li>n'importe quel ordinateur connect� � Internet</li>
   <li>n'importe quel microphone main libre bas prix</li>
   <li>n'importe quel navigateur web</li>
   <li>si Windows : le logiciel Winamp (<a href=\"http://www.winamp.com\" target=\"_blank\">www.winamp.com</a>)</li>
   <li>si Mac : le logiciel Quicktime broadcaster (<a href=\"http://www.quicktime.com\" target=\"_blank\">www.quicktime.com</a>)</li>
 </ul>
 Les participants ont besoin de :<br>
 <ul>
   <li>n'importe quel ordinateur connect� � Internet</li>
   <li>haut-parleurs ou �couteurs</li>
   <li>n'importe quel navigateur web</li>
   <li>si Windows ou Mac : Quicktime (<a href=\"http://www.quicktime.com\" target=\"_blank\">www.quicktime.com</a>)</li>
   <li>si Linux : XMMS</li>
 </ul>
 En cas de probl�me, n'h�sitez pas � contacter Dokeos : <a href=\"http://www.dokeos.com\" target=\"_blank\">http://www.dokeos.com</a>.";

// help.php?open=Clar

$langHClar="Aide au d�marrage";

$langClarContent="<br><p><b>Membre</b></p>
<p>
Pour visiter les espaces accessibles depuis la page d'accueil du portail,
il suffit de cliquer sur le code de l'espace dans la liste, sans inscription pr�alable.</p>
<p>Pour acc�der aux espaces non accessibles depuis la page d'accueil du portail, il
est n�cessaire de s'inscrire. Inscription > Entrez vos param�tres personnels >
Action: M'inscrire � des espaces > Cochez les espaces et validez.</p>
<p>Un courriel vous sera envoy�
pour vous rappeler pseudo et mot de passe � introduire lors de votre prochaine visite.</p>
<hr noshade size=1>
<p><b>Responsable</b></p>
<p><b>Cr�er un espace</b></p>
<p>Proc�dez come suit. Inscription > Remplissez tous les champs et choissez 'Cr�er des espaces' comme action > Validez > Entrer le nom de l'espace, s�lectionnez une cat�gorie, entrez le code de l'espace (inventez-en un au besoin > Validez. Et vous voici dans la lsite de vos espaces. Cliquez sur l'intitul� de l'espace que vous venez de cr�er. Vous voici dans un espace vide � l'exception de quelques contenus factices destin�s � vous �viter l'angoisse de la page blanche. A l'inscription, un courriel vous a �t� envoy� pour vous rappeler le pseudo et le mot de passe que vous devrez taper lors de votre prochaine visite.</p>
<p>En cas de probl�me, contactez votre responsable r�seau ou le responsable de ce portail. Vous pouvez aussi publier un message sur le forum de support de <a href=http://www.dokeos.net>http://www.dokeos.net</a>.
</p>";




// help.php?open=Doc

$langHDoc="Aide documents";

$langDocContent="<p>Le module de gestion de document fonctionne de
 mani&egrave;re semblable &agrave; la gestion de vos documents sur un
 ordinateur. </p><p>Vous pouvez y d&eacute;poser des documents de tout type
 (HTML, Word, Powerpoint, Excel, Acrobat, Flash, Quicktime, etc.). Soyez
 attentifs cependant &agrave; ce que les membres disposent des
 outils n&eacute;cessaires &agrave; leur consultation. Soyez
 &eacute;galement vigilants &agrave; ne pas envoyer
  des documents infect&eacute;s par des virus. Il est prudent de soumettre
 son
  document &agrave; un logiciel antivirus &agrave; jour avant de le
 d&eacute;poser
  sur le portail.</p>
<p>Les documents sont pr&eacute;sent&eacute;s par ordre
 alphab&eacute;tique.<br>
  <b>Astuces:</b> si vous souhaitez que les documents soient class&eacute;s
 de
  mani&egrave;re diff&eacute;rente, vous pouvez les faire
 pr&eacute;c&eacute;der
  d'un num&eacute;ro, le classement se fera d&egrave;s lors sur cette base.
 </p>
<p>Vous pouvez :</p>
<h4>T&eacute;l&eacute;charger un document dans ce module</h4>
<ul>
  <li>S&eacute;lectionnez le document sur votre ordinateur &agrave; l'aide
 du
	bouton &quot;Parcourir&quot;
	<input type=submit value=Parcourir name=submit2>
	&agrave; droite de votre &eacute;cran.</li>
  <li>Ex&eacute;cutez le t&eacute;l&eacute;chargement &agrave; l'aide du
 bouton&quot;
	t&eacute;lecharger&quot;
	<input type=submit value=t&eacute;l&eacute;charger name=submit2>
	.</li>
</ul>
<h4>Renommer un document (ou un r&eacute;pertoire)</h4>
<ul>
  <li>cliquez sur le bouton <img src=../img/rename.gif width=20
 height=20 align=baseline>
	dans la colonne &quot;Renommer&quot;.</li>
  <li>Tapez le nouveau nom dans la zone pr&eacute;vue &agrave; cet effet
 qui appara&icirc;t
	en haut &agrave; gauche</li>
  <li>Valider en cliquant sur &quot;OK&quot;
	<input type=submit value=OK name=submit24>
	.
</ul>
	<h4>Supprimer un document (ou un r&eacute;pertoire)</h4>
	<ul>

  <li>Cliquer sur le bouton <img src=../img/delete.gif width=20
 height=20>
	dans la colonne &quot;Supprimer&quot;.</li>
	</ul>
	<h4>Rendre un document (ou un
 r&eacute;pertoire) invisible aux membres</h4>
	<ul>

  <li>Cliquez sur le bouton <img src=../img/visible.gif width=20
 height=20>dans
	la colonne &quot;Visible/invisible&quot;.</li>
	  <li>Le document (ou le r&eacute;pertoire) existe toujours, mais il n'est

		plus visible pour les membres.</li>
	</ul>
	<ul>

  <li> Si vous souhaitez rendre cet &eacute;l&eacute;ment &agrave; nouveau
 visible,
	cliquez sur le bouton <img src=../document/../img/invisible.gif
 width=24 height=20>
	dans la colonne Visible/invisible</li>
	</ul>
	<h4>Ajouter ou modifier un commentaire au document (ou au
 r&eacute;pertoire)</h4>
	<ul>

  <li>Cliquez sur le bouton <img
 src=../document/../img/comment.gif width=20 height=20>
	dans la colonne &quot;Commentaire&quot;</li>
	  <li>Tapez le nouveau commentaire dans la zone pr&eacute;vue &agrave; cet

		effet qui appara&icirc;tra en haut &agrave; gauche.</li>
	  <li>Validez en cliquant sur &quot;OK&quot;
		<input type=submit value=OK name=submit2>
		.</li>
	</ul>
	<p>Si vous souhaitez supprimer un commentaire, cliquez sur le bouton <img
 src=../document/../img/comment.gif width=20 height=20>,
	  effacez l'ancien commentaire de la zonne et validez en cliquant
 &quot;OK&quot;
	  <input type=submit value=OK name=submit22>
	  .
	<hr>
	<p>Vous pouvez aussi organiser le contenu du module de document en
 rangeant
	  les documents dans de r&eacute;pertoires. Pour ce faire vous devez :</p>
	<h4><b>Cr&eacute;er un r&eacute;pertoire</b></h4>
	<ul>
	  <li>Cliquez sur la commande &quot;<img
 src=../document/../img/file.gif width=20
 height=20>cr&eacute;er
		un r&eacute;pertoire&quot; en haut &agrave; gauche de l'&eacute;cran</li>
	  <li>Tapez le nom de votre nouveau r&eacute;pertoire dans la zone
 pr&eacute;vue
		&agrave; cet effet en haut &agrave; gauche de l'&eacute;cran.</li>
	  <li>Validez en cliquant &quot;OK&quot;
		<input type=submit value=OK name=submit23>
		.</li>
	</ul>
	<h4>D&eacute;placer un document (ou un r&eacute;pertoire)</h4>
	<ul>
	  <li>Cliquez sur le bouton <img
 src=../document/../img/move.gif width=34 height=16>
		dans la colonne d&eacute;placer</li>
	  <li>Choisissez le r&eacute;pertoire dans lequel vous souhaitez
 d&eacute;placer
		le document ou le r&eacute;pertoire dans le menu d&eacute;roulant
 pr&eacute;vu
		&agrave; cet effet qui appara&icirc;tra en haut &agrave; gauche.(note:
		le mot &quot;racine&quot; dans ce menu repr&eacute;sente la racine de
		votre module document).</li>
	  <li>Validez en cliquant &quot;OK&quot;
		<input type=submit value=OK name=submit232>
		.</li>
	</ul>
	<center>
	  <p>";



// help.php?open=User

$langHUser="Aide membres";
$langUserContent="<b>Droits d'administration</b>
<p>Pour permettre � un co-responsable, un chef de projet ou qui que ce
 soit de co-administrer l'espace avec vous, vous devez pr�alablement
 l'inscrire � votre espace ou vous assurer qu'il est inscrit puis modifier
 ses droits en cochant 'modifier' sous 'droits d'admin.' puis
 'tous'.</P><hr>
<b>Co-responsables</b>
<p>Pour faire figurer le nom d'un co-responsable dans l'en-t�te de votre
 esace, utilisez la page 'Modifier info espace' (dans les outils orange
 sur la page d'accueil de votre formation). Cette modification de l'en-t�te
 de l'espace n'inscrit pas automatiquement ce co-responsable comme membre de l'espace. Ce sont deux actions distinctes.</p><hr>
<b>Ajouter un membre</b>
<p>Pour ajouter un membre � votre espace, remplissez les champs
et validez. La personne recevra un courriel de confirmation de son
inscription contenant son pseudo et son mot de passe, sauf si
vous n'avez pas introduit son email.</p>";



// Help Group

$langGroupManagement="Gestion des groupes";
$langGroupContent="<p><b>Introduction</b></p>
	<p>Cet outil permet de cr�er et de g�rer des groupes de travail.
	A la cr�ation, les groupes sont vides. Le responsable dispose de
	plusieurs fa�ons de les remplir:
	<ul><li>automatique ('Remplir les groupes'),</li>
	<li>� la pi�ce ('Editer'),</li>
	<li>par les membres (Propri�t�s: 'Membres autoris�s ...').</li></ul>
	Ces modes de remplissage sont combinables entre eux. Ainsi, on peut demander aux membres
	de s'inscrire eux-m�mes puis constater que certains d'entre eux ont oubli� de s'inscrire
	et choisir alors de remplir les groupes, ce qui aura pour effet de les compl�ter. On peut
	aussi (via la fonction 'Editer') modifier manuellement la composition de chacun des groupes
	apr�s remplissage automatique ou apr�s auto-inscription par les membres.</p>
	<p>Le remplissage des groupes, qu'il soit automatique ou manuel, ne fonctionne que
	si les membres sont d�j� inscrits � l'espace, ce qui peut �tre v�rifi� via l'outil
	'Membres'.</p><hr noshade size=1>
	<p><b>Cr�er des groupes</b></p>
	<p>Pour cr�er de nouveaux groupes, cliquez sur 'Cr�er nouveau(x) groupe(s)' et d�terminez
	le nombre de groupes � cr�er. Le nombre maximum de participants est facultatif. Si
	vous laissez ce champ inchang�, la taille des groupes sera illimit�e.</p><hr noshade size=1>
	<p><b>Propri�t�s des groupes</b></p>
	<p>Vous pouvez d�terminer de fa�on globale les propri�t�s des groupes.
	<ul><li><b>Membres autoris�s � s'inscrire eux-m�mes dans les groupes</b>:
	vous cr�ez des groupes vides et laissez les membres s'y ajouter eux-m�mes.
	Si vous avez d�fini un nombre de places maximum
	par groupe, les groupes complets n'acceptent plus de nouveaux membres.
	Cette m�thode convient particuli�rement au responsable qui ne conna�t pas la
	liste des membres au moment de cr�er les groupes.</li>
	<li><b>Acc�s aux groupes r�serv� uniquement � leurs membres</b>: les groupes n'acc�dent
	pas aux forums et documents partag�s des autres groupes. Cette propri�t� n'exclut pas
	la publication de documents par les groupes hors de leur espace priv�.</li>
	<li><b>Outils</b>: chaque groupe dispose soit d'un forum, soit d'un r�pertoire partag� associ�
	� un gestionnaire de documents, soit (cas le plus fr�quent) les deux.</li></ul>
	<hr noshade size=1>
	<p><b>Edition manuelle</b></p>
	<p>Une fois des groupes cr�es, vous voyez appara�tre leur liste assortie d'une s�rie d'informations
	et de fonctions. <ul><li><b>Editer</b> permet de modifier manuellement la composition du groupe.</li>
	<li><b>Supprimer</b> d�truit un groupe.</li></ul>
	<hr noshade size=1>";


// help.php?open=Exercise

$langHExercise="Aide Tests";

$langExerciseContent="<p>Le module de tests vous permet de cr�er des tests d'auto-�valuation pouvant contenir un nombre quelconque de questions.<br><br>
Il existe diff�rents types de r�ponses disponibles pour la cr�ation de vos questions :<br><br>
<ul>
  <li>Choix multiple (R�ponse unique)</li>
  <li>Choix multiple (R�ponses multiples)</li>
  <li>Correspondance</li>
  <li>Remplissage de blancs</li>
</ul>
Un test rassemble un certain nombre de questions sous un th�me commun.</p>
<hr>
<b>Cr�ation d'un test</b>
<p>Pour cr�er un test, cliquez sur le lien &quot;Nouveau test&quot;.<br><br>
Introduisez l'intitul� de votre test, ainsi qu'une �ventuelle description de celui-ci.<br><br>
Vous pouvez �galement choisir entre 2 types de tests :<br><br>
<ul>
  <li>Questions sur une seule page</li>
  <li>Une question par page (s�quentiel)</li>
</ul>
et pr�ciser si vous souhaitez ou non que les questions soient tri�es al�atoirement lors de l'ex�cution du test par le membre.<br><br>
Enregistrez ensuite votre test. Vous arriverez � la gestion des questions de ce test.</p>
<hr>
<b>Ajout d'une question</b>
<p>Vous pouvez � pr�sent ajouter une question au test pr�c�demment cr��. La description est facultative, de m�me que l'image que vous avez la possibilit� d'associer � votre question.</p>
<hr>
<b>Choix multiple</b>
<p>Il s'agit du classique QRM (question � r�ponse multiple) / QCM (question � choix multiple).<br><br>
Pour cr�er un QRM / QCM :<br><br>
<ul>
  <li>D�finissez les r�ponses � votre question. Vous pouvez ajouter ou supprimer une r�ponse en cliquant sur le bouton ad�quat</li>
  <li>Cochez gr�ce aux cases de gauche la ou les r�ponses exactes</li>
  <li>Ajoutez un �ventuel commentaire. Celui-ci ne sera vu par le membre qu'une fois qu'il aura r�pondu � la question</li>
  <li>Donnez une pond�ration � chaque r�ponse. La pond�ration peut �tre n'importe quel nombre entier, positif, n�gatif ou nul</li>
  <li>Enregistrez vos r�ponses</li>
</ul></p>
<hr>
<b>Remplissage de blancs</b>
<p>Il s'agit du texte � trous. Le but est de faire trouver par le membre des mots que vous avez pr�alablement retir�s du texte.<br><br>
Pour retirer un mot du texte, et donc cr�er un blanc, placez ce mot entre crochets [comme ceci].<br><br>
Une fois le texte introduit et les blancs d�finis, vous pouvez �ventuellement ajouter un commentaire qui sera vu par le membre lorsqu'il aura r�pondu � la question.<br><br>
Enregistrez votre texte, et vous arriverez � l'�tape suivante qui vous permettra d'attribuer une pond�ration � chacun des blancs. Par exemple si la question est sur 10 points et que vous avez 5 blancs, vous pouvez donner une pond�ration de 2 points � chaque blanc.</p>
<hr>
<b>Correspondance</b>
<p>Ce type de r�ponse peut �tre choisi pour cr�er une question o� le membre devra relier des �l�ments d'un ensemble E1 avec les �l�ments d'un ensemble E2.<br><br>
Il peut �galement �tre utilis� pour demander au membre de trier des �l�ments dans un certain ordre.<br><br>
Commencez par d�finir les options parmi lesquelles le membre pourra choisir la bonne r�ponse. Ensuite, d�finissez les questions qui devront �tre reli�es � une des options d�finies pr�c�demment. Enfin, �tablissez les correspondances via les menus d�roulants.<br><br>
Remarque : Plusieurs �l�ments du premier ensemble peuvent pointer vers le m�me �l�ment du deuxi�me ensemble.<br><br>
Donnez une pond�ration � chaque correspondance correctement �tablie, et enregistrez votre r�ponse.</p>
<hr>
<b>Modification d'un test</b>
<p>Pour modifier un test, le principe est le m�me que pour la cr�ation. Cliquez simplement sur l'image <img src=\"../img/edit.gif\" border=\"0\" align=\"absmiddle\"> � c�t� du test � modifier, et suivez les instructions ci-dessus.</p>
<hr>
<b>Suppression d'un test</b>
<p>Pour supprimer un test, cliquez sur l'image <img src=\"../img/delete.gif\" border=\"0\" align=\"absmiddle\"> � c�t� du test � supprimer.</p>
<hr>
<b>Activation d'un test</b>
<p>Avant qu'un test ne puisse �tre utilis� par un membre, vous devez l'activer en cliquant sur l'image <img src=\"../img/invisible.gif\" border=\"0\" align=\"absmiddle\"> � c�t� du test � activer.</p>
<hr>
<b>Ex�cution d'un test</b>
<p>Vous pouvez tester votre test en cliquant sur son nom dans la liste des tests.</p>
<hr>
<b>Tests al�atoires</b>
<p>Lors de la cr�ation / modification d'un test, vous avez la possibilit� de pr�ciser si vous souhaitez que les questions soient tir�es dans un ordre al�atoire parmi toutes les questions du test.<br><br>
Cela signifie qu'en activant cette option, les questions seront � chaque fois dans un ordre diff�rent lorsque les membres ex�cuteront le test.<br><br>
Si vous avez un grand nombre de questions, vous pouvez aussi choisir de ne prendre al�atoirement que X questions sur l'ensemble des questions disponibles dans ce test.</p>
<hr>
<b>Banque de questions</b>
<p>Lorsque vous supprimez un test, les questions qu'il contenait ne le sont pas et peuvent �tre r�utilis�es dans un nouveau test, via la banque de questions.<br><br>
La banque de questions permet �galement de r�utiliser une m�me question dans plusieurs test.<br><br>
Par d�faut, toutes les questions de votre formation sont affich�es. Vous pouvez afficher les questions relatives � un test en particulier, en choisissant celui-ci dans le menu d�roulant &quot;Filtre&quot;.<br><br>
Des questions orphelines sont des questions n'appartenant � aucun test.</p>";
?>

<?php
/*
      +----------------------------------------------------------------------+
      | DOKEOS version 1.5.0 $Revision: 7366 $                                |      |
      +----------------------------------------------------------------------+
      |   $Id: help.inc.php 7366 2005-12-11 11:38:20Z d13tr1ch $       |
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

$langForContent="<p>Le forum est un outil de discussion asynchrone par �crit.
 A la diff�rence de l'email, le forum situe la discussion dans un espace
 public ou semi-public (� plusieurs).</p><p>Pour utiliser l'outil de forum
 de Dokeos, les membres n'ont besoin que d'un simple navigateur web
 (Netscape, Explorer...), pas besoin d'outil de courriel (Eudora,
 Outlook...).</P>
 <p>Pour organiser les forums, cliquez sur 'administrer'.
 Les �changes sont organis�s de fa�on hi�rarchique selon l'arborescence
 suivante:</p><p><b>Cat�gorie > Forum > Sujet > R�ponse</b></p>Pour
 permettre aux membres de discuter de fa�on structur�e, il est
 indispensable d'organiser les �changes pr�alablement en cat�gories et
 forums (� eux de cr�er sujets et r�ponses). Par d�faut, le forum contient
 uniquement la cat�gorie Public, un sujet d'exemple et un message exemple.
 Vous pouvez ajouter des forums dans la cat�gorie public, ou bien modifier
 son intitul� ou encore cr�er d'autres cat�gories dans lesquelles il vous
 faudra alors cr�er de nouveaux forums. Une cat�gorie qui ne contient aucun
 forum ne s'affiche pas et est inutilisable.</p>
 <b>Forums de groupes</b>
 <p>Pour cr�er des forums de groupes, utilisez l'outil Groupes et non l'outil Forums. Cela vous permettra de cr�er des forums privatifs (non accessibles aux membres des autres groupes) et de fournir simultan�ment un espace de documents aux groupes.</p>
 <p><b>Astuces p�dagogiques</b></p>
 Un forum d'apprentissage n'est pas identique aux forums que l'on trouve habituellement sur internet. D'une part il n'est pas possible pour les �tudiants/stagaires de modifier leurs messages une fois publi�s car l'espace suit une logique d'archivage et peut �tre utilis� pour v�rifier ce qui a �t� dit dans le pass�. Par ailleurs, les forums Dokeos permettent certains usages particuli�rement pertinents dans un contexte d'apprentissage. Ainsi certains responsables/responsables publient directement dans les forums leurs corrections:
 <ul><li>Un �tudiant/stagiaire est invit� � publier un rapport directement dans le forum,</li>
 <li>L'responsable le corrige en cliquant sur Editer (crayon jaune) puis introduit ses corrections � l'aide de l'�diteur graphique : couleur, soulignage etc.,</li>
 <li>Finalement, les autres �tudiants/stagiaires profitent de la correction qui a �t� faite sur la production de l'un d'entre eux,</li>
 <li>Notez que le m�me principe peut �tre utilis� d'un �tudiant � l'autre, mais il faudra alors copier/coller le message de son consdisciple car les �tudiants/stagiaires ne peuvent �diter les messages des autres �tudiants/stagiaires.<.li></ul>";



// help.php?open=Home

$langHHome="Aide Page d'accueil";

$langHomeContent="<p>La page d'accueil de votre espace pr�sente une s�rie d'outils : un texte d'introduction, une Description de l'espace, un outil de publication de Documents, etc. Cette page est modulaire. Vous pouvez masquer ou afficher chacun des outils.</p>
<b>Navigation</b>
<p>La navigation se fait soit au moyen du menu en arborescence situ� sous la banni�re de couleur, dans le coin sup�rieur gauche, soit au moyen des ic�nes permettant un acc�s direct aux outils et situ�es dans le coin sup�rieur droit. Que vous cliquiez � droite sur la maison ou � gauche sur le code de l'espace (toujours en majuscules), vous retournerez � la page d'accueil.</p>
<b>M�thodologie</b>
<p>Il importe de rendre votre espace dynamique afin de montrer aux participants qu'il y a quelqu'un derri�re l'�cran. Ainsi vous pouvez modifier r�guli�rement le texte d'introduction (en cliquant sur le crayon jaune) pour y signaler des �v�nements ou rappeler des �tapes de l'espace.</p>
<p>Pour construire votre espace, une mani�re classique de travailler est de proc�der come suit:
<ol><li>Dans Propri�t�s de l'espace, cochez Acc�s : priv� et Inscription : refus� afin d'interdire toute visite pendant la phase de fabrication de l'espace,</li>
<li>Affichez tous les outils en cliquant sur le lien gris 'Afficher' sous le nom des outils masqu�s dans le bas de l'�cran,</li>
<li>Utilisez les outils pour 'remplir' votre espace de contenus, d'�v�nements, de groupes, etc.,</li>
<li>D�sactivez tous les outils,</li>
<li>Utilisez l'outil Parcours pour construire un itin�raire � travers les autres outils</li>
li>Rendez le parcours ainsi cr�� visible : il s'affichera sur la page d'accueil</li>
<li> Votre espace est termin�. Il pr�sente un texte d'introduction suivi d'un lien portant le titre du parcours que vous avez cr��. Cliquez sur 'Vue membre' pour voir l'espace du point de vue de celui qui le suit.<I></I></li></ol>";

$langHClar="Aide Dokeos";

$langClarContent="<p><b>Responsable, responsable</b></p>
<p>Dokeos est un syst�me de gestion de la formation et de la connaissance. Il permet � des formateurs, des responsables de formation d'organiser des parcours d'apprentissage, de g�rer des interactions avec des apprenants et de construire des contenus sans quitter le navigateur web.</p>
<p>Pour utiliser Dokeos en tant que formateur/responsable, vous devez disposer d'un identifiant et d'un mot de passe. Ceux-ci pourront �tre obtenus soit par auto-inscription (si votre portail le permet, un lien 'Inscription' appara�t sur sa page d'accueil) soit par votre administration si l'inscription est g�r�e de fa�on centralis�e. Une fois en possession de votre identifiant et de votre mot de passe, introduisez-les dans le syst�me, cr�ez un espace (ou utilisez celui qui a �t� cr�� pour vous par votre administration) et familiarisez-vous avec les outils en d�posant des documents, en composant des textes de description etc.</p>
<p>Dans votre espace, commencez par ouvrir Param�tres de l'espace et fermez-en l'acc�s le temps de concevoir le dispositif. Vous pouvez, si vous le souhaitez, inscrire un coll�gue comme co-responsable de votre espace pendant cette p�riode, pour cela, si votre coll�gue n'est pas encore inscrit dans le portail, rendez-vous dans la rubrique Membres et inscrivez-le en cochant : 'Responsable'. S'il est d�j� inscrit dans le syst�me, ouvrez l'acc�s � l'inscription (dans Param�tres de l'espace) et demandez-lui de s'inscrire lui-m�me puis modifiez ses droits dans 'Membres' pour le rendre responsable au m�me titre que vous puis refermez l'acc�s � l'inscription. Si votre organisation le permet, vous pouvez aussi lui demander d'associer votre coll�gue � votre espace.</p><p>Chaque outil est muni d'une aide contextuelle (signal�e par la bou�e) qui vous en explique le fonctionnement. Si vous ne trouvez pas l'information voulue, consultez la page de documentation: <a href=\"http://www.dokeos.com/documentation.php\">http://www.dokeos.com/documentation.php</a> et t�l�chargez �ventuellement le Manuel du responsable.</p>
<p><b>Stagiaire, apprenant</b></p>
<p>Ce portail vous permet de suivre des formations et d'y participer. Le logiciel Dokeos a �t� sp�cialement con�u pour favoriser les sc�narios d'apprentissage actifs : par la collaboration, par le projet, le probl�me, etc. Vos responsables/responsables ont con�u pour vous des espacess d'apprentissage qui peuvent prendre la forme de simples r�pertoires de documents ou bien de parcours sophistiqu�s impliquant une chronologie et des �preuves � surmonter, seul ou en groupe.</p>
<p>Selon les d�cisions qui ont �t� prises par votre organisation, votre �cole, votre universit�, les modes d'inscription et de participation aux espace peuvent varier sensiblement. Dans certains portail, vous pouvez vous auto-inscrire dans le syst�me, vous auto-inscrire dans les espace. Dans d'autres, un syst�me d'administration centralis�e g�re l'inscription et vous recevrez par courriel ou par la poste votre identifiant et votre mot de passe.</p>";


// help.php?open=Online

$langHOnline="Aide Syst�me de conf�rence en direct";
$langOnlineContent="<br><span style=\"font-weight: bold;\">Introduction </span><br>
      <br>
      <div style=\"margin-left: 40px;\">L'outil de conf�rence en direct vous permet de former, d'informer ou de consulter jusqu'� 500 personnes distantes simultan�ment de fa�on simple et rapide � l'aide de:<br>
      </div>
      <ul>
        <ul>
          <li><b>audio en direct :</b> la voix do responsable/conf�rencier est diffus�e en direct aux participants sous forme de fichier mp3,<br>
          </li>
          <li><b>transparents :</b> les participants suivent la pr�sentation sur des transparents PowerPoint, un fichier PDF ou tout autre type de support,<br>
          </li>
          <li><b>interaction :</b> les participants posent des questions par chat.</li>
        </ul>
      </ul>
      <span style=\"font-weight: bold;\"></span><span
 style=\"font-weight: bold;\"><br>
Stagiaire/participant</span><br>
      <br>
      <div style=\"margin-left: 40px;\">Pour assiter � une conf�rence, vous devez diposer de:<br>
      </div>
      <br>
      <div style=\"margin-left: 40px;\">1. Des haut-parleurs (ou un casque) connect�s � votre PC<br>
      <br>
      <a href=\"http://www.logitech.com\"><img
 style=\"border: 0px solid ; width: 87px; height: 58px;\" alt=\"speakers\"
 src=\"../img/speaker.gif\"></a><br>
      <br>
2. Winamp Media player (ou tout autre logiciel permettant de lire du mp3 en streaming)<br>
      <br>
      <a href=\"http://www.winamp.com\"><img
 style=\"border: 0px solid ; width: 87px; height: 27px;\" alt=\"Winamp\"
 src=\"../img/winamp.gif\"></a><br>
      <br>
Mac : utilisez <a href=\"http://www.quicktime.com\">Quicktime</a><br>
Linux : utilisez <a href=\"http://www.xmms.org/\">XMMS</a> <br>
      <br>
&nbsp; 3. Acrobat PDF reader ou Word oo PowerPoint, en fonction du choix op�r� par le responsable/conf�rencier pour la diffusion de ses transparents.>br>
      <br>
      <a href=\"http://www.acrobat.com\"><img
 style=\"border: 0px solid ; width: 87px; height: 31px;\"
 alt=\"acrobat reader\" src=\"../img/acroread.gif\"></a><br>
      </div>
      <br>
      <span style=\"font-weight: bold;\"><br>
Responsable/conf�rencier</span><br>
      <br>
      <div style=\"margin-left: 40px;\">Pour donner une conf�rence, vous devez disposer de:<br>
      </div>
      <br>
      <div style=\"margin-left: 40px;\">1. Un casque avec microphone<br>
      <br>
      <a href=\"http://www.logitech.com\"><img
 style=\"border: 0px solid ; width: 87px; height: 87px;\" alt=\"Headset\"
 src=\"../img/headset.gif\"></a><br>
Nous vous recommandons d'utiliser un casque <a href=\"http://www.logitech.com/\">Logitech</a>
avec prise USB pour une qualit� de diffusion audio optimale et garantie.<br>
      <br>
2. Winamp<br>
      <br>
      <a href=\"http://www.winamp.com\"><img
 style=\"border: 0px solid ; width: 87px; height: 27px;\" alt=\"Winamp\"
 src=\"../img/winamp.gif\"></a><br>
      <br>
3. Le plugin SHOUTcast DSP pour Winamp 2.x <br>
      <br>
      <a href=\"http://www.shoutcast.com\"><img
 style=\"border: 0px solid ; width: 87px; height: 24px;\" alt=\"Shoutcast\"
 src=\"../img/shoutcast.gif\"></a><br>
      <br>
Suivez les instructions sur <a href=\"http://www.shoutcast.com\">www.shoutcast.com</a>
pour installer et param�trer le plugin Shoutcast Winamp.<br>
      </div>
      <br>
      <span style=\"font-weight: bold;\"><br>
Comment donner une conf�rence?<br>
      <br>
      </span>
      <div style=\"margin-left: 40px;\">Cr�ez un espace Dokeos &gt; Entrez dedans &gt; Affichez puis entrez dans l'outil Conf�rence &gt; Editez (ic�ne du crayon jaune en haut � gauche) les param�tres &gt; envoyez vos transparents (PDF, PowerPoint ou quelque document que ce soit) et votre photo (de pr�f�rence pas trop grande)&gt; tapez un texte d'introduction qui pourra si vous voulez renvoyer par des liens � d'autres sites ou d'autres documents
&gt; tapez l'URL de votre streaming audio en fonction des informations qui vous ont �t� communiqu�es par votre responsable informatique (Dokeos fournit, � titre payant, un tel service : info@dokeos.com) et lancez la diffusion par Winamp tout en mettant votre casque.<br><span style=\"font-weight: bold;\"></span><br>
      <span style=\"font-weight: bold;\"></span></div>
      <div style=\"margin-left: 40px;\"><img
 style=\"width: 256px; height: 182px;\" alt=\"conference config\"
 src=\"../img/conf_screen_conf.gif\"><br>
N'oubliez pas de fournir � vos futurs participants une date et une heure de rendez-vous pr�cise et de vous assurer que cvhacun poss�de identifiant et mot de passe pour acc�der � votre espace. Une fois la conf�rence commenc�e, il sera trop tard pour r�gler les probl�mes techniques ou d'acc�s).<br>
      <br>
      <span style=\"font-weight: bold;\">Astuce</span> : 10 minutes avant la conf�rence, tapez un court message dans le chat pour informer les participants de votre pr�sence et aider ceux qui auraient �ventuellement des probl�mes audio. Il est important aussi que vous soyez le premier connect� et que vous diffusiez l'audio quelques minutes � l'avance, sinon vous devrez demander � vos participants de relancer leur lecteur audio. <br>
      </div>
      <br>
      <br>
      <span style=\"font-weight: bold;\">Serveur de streaming</span><br>
      <br>
      <div style=\"margin-left: 40px;\">Il ne faut pas confondre la conf�rence en direct (de 1 � plusieurs) avec la t�l�phonie par internet (de 1 � 1). Pour donner une conf�rence en direct, vous avez n�cessairement besoin d'un serveur de streaming et probablement d'un responsable technique pour vous aider � configurer le flux audio (la vid�o fonctionne aussi, mais nous ne la recommandons pas). Cette personne vous communiquera l'URL de votre flux audio et vous devrez taper cette URL dans la configuration de votre conf�rence. 
	 <br>
      <br>
      <small><a href=\"http://www.dokeos.com/hosting.php#streaming\"><img
 style=\"border: 0px solid ; width: 258px; height: 103px;\"
 alt=\"dokeos streaming\" src=\"../img/streaming.jpg\"><br>
dokeos streaming</a></small><br>
      <br>
faites-le vous-m�me ou faites-le faire par un de vos proches : installez, configurez et administrez <a
 href=\"http://www.shoutcast.com\">Shoutcast</a> ou <a
 href=\"http://developer.apple.com/darwin/projects/streaming/\">Apple
Darwin</a>. <br>
      <br>
Ou contactez Dokeos. Nous pouvons vous aider � organiser votre conf�rence et vous assister dans sa mise en oeuvre vous louant un espace de streaming sur nos serveurs et en vous guidant dans son utilisation: <a
 href=\"http://www.dokeos.com/hosting.php#streaming\">http://www.dokeos.com/hosting.php</a><br>
      <br>
      <br>";



// help.php?open=Doc

$langHDoc="Aide documents";

$langDocContent="<p>Le module de gestion de document fonctionne de
 mani&egrave;re semblable &agrave; la gestion de vos documents sur un
 ordinateur. </p><p>Vous pouvez y cr�er des pages web simples et y d&eacute;poser des documents de tous type
 (HTML, Word, Powerpoint, Excel, Acrobat, Flash, Quicktime, etc.).</p>
 <p>Vous pouvez �galement envoyer des sites web complexes, sous forme de fichiers ZIP qui se d�compresseront � 'arriv�e (cochez 'd�zipper').</p>Soyez
 attentifs &agrave; ce que les membres disposent des
 outils n&eacute;cessaires &agrave; leur consultation. Soyez
 &eacute;galement vigilants &agrave; ne pas envoyer
  des documents infect&eacute;s par des virus. Il est prudent de soumettre
 son document &agrave; un logiciel antivirus &agrave; jour avant de le
 d&eacute;poser
  sur le portail.</p>
<p>Les documents sont pr&eacute;sent&eacute;s par ordre
 alphab&eacute;tique.<br><br>
  <b>Astuces:</b> si vous souhaitez que les documents soient class&eacute;s
 de
  mani&egrave;re diff&eacute;rente, vous pouvez les faire
 pr&eacute;c&eacute;der
  d'un num&eacute;ro, le classement se fera d&egrave;s lors sur cette base.
 </p>
<p>Vous pouvez :</p>
<H4>Cr�er un document</H4>
<p>Cliquez sur 'Cr�er un document' > donnez-lui un titre (ni espaces ni accents) > tapez votre texte > utilisez les boutons de l'�diteur WYSIWYG (What You See Is What You Get) pour structurer l'information, cr�er des tables, des styles, des listes � puces etc. </p>
<p>Pour produire des pages web acceptables, vous devrez apprendre � ma�triser 3 concepts : les Liens, l'insertion d'images par URL et la disposition dans l'espace � l'aide des Tables.</p>
<p>Ne perdez pas de vue qu'une page web n'est pas un document Word et qu'elle est soumise � des contraintes et des limitations plus importantes (taille du fichier, limites de mise en page, garantie d'affichage d'un navigateur et d'un ordinateur � l'autre).</p>
<p>Une fa�on rapide de produire du contenu � l'aide de l'�diteur est de copier/coller le contenu de vos pages Word ou de pages web. Vous perdrez certains �l�ments de mise en page et parfois les liens vers les images, mais vous obtiendrez rapidement un r�sultat.
</p>
<ul><li><b>Pour ajouter un lien</b>, vous devez pr�alablement copier la cible de votre lien. Nous vous conseillons d'ouvrir simultan�ment deux fen�tres de votre navigateur, l'une avec votre espace Dokeos et l'autre pour partir � la recherche de la page vers laquelle vous voulez pointer (cette page peut d'ailleurs se trouver � l'int�rieur de votre espace Dokeos).<br><br>Une fois la page cible obtenue, copiez son URL (s�lectionnez son URL dans la barre d'URL et tapez CTRL+C ou POMME+C), retournez dans la fen�tre o� vous tapez votre texte, s�lectionnez le mot qui servira de lien et cliquez dans l'�diteur Wysiwyg sur l'ic�ne repr�sentant un maillon de chaine. Collez alors (CTRL+V ou POMME+V) l'URL dans le champ d'URL et validez.<br><br>Le mot s�lectionn� est devenu bleu et constitue un lien. Il ne sera utilisable qu'une fois la page enregistr�e. Testez-le > enregistrez la page, ouvrez-la en mode navigation (et non �dition) et cliquez sur le lien pour observer le r�sultat. Notez que vous pouvez d�cider si le lien s'ouvrira dans la m�me fen�tre (�crasant possiblement votre espace ou le faisant dispara�tre) ou dans une nouvelle fen�tre.</li>


<li><b>Pour ajouter une image</b>, le principe est similaire: parcourez le web � l'aide d'une deuxi�me fen�tre de navigateur, trouvez l'image (si cette image se trouve dans votr r�pertoire de documents, cliquez sur 'Sans cadres' pour afficher l'image seule), copiez son URL (CTRL+C ou POMME+C) depuis la barre d'URL et retournez dans la fen�tre o� vous tapez votre texte.<br><br>Positionnez votre curseur dans le champ de saisie � l'endroit o� vous voulez voir appara�tre l'image et cliquez sur l'ic�ne repr�sentant un arbre. Copiez l'URL (CTRL+V ou POMME+V) dans le chapp URL, affichez 'Preview' puis validez.
<br><br>Notez que dans une page web, vous ne pouvez ni redimensionner ni d�placer une image � votre guise comme dans une page Word. De mani�re g�n�rale dans le web, il n'y a pas moyen de glisser/d�poser quoi que ce soit.</li>

<li><b>Pour ajouter une table</b> (ce qui est une des seules fa�ons de disposer les parties de texte et les images dans l'espace), positionnez votre curseur dans le champ de saisie � l'endroit o� vous voulez voir appara�tre le tableau, s�lectionnez l'ic�ne repr�sentant un tableau dans l'�diteur Wysiwyg, d�cidez d'un nombre de lignes et de colonnes et validez. Nous vous recommandons aussi de choisir les valeurs width=600 border=1, cellspacing=0 et cellpadding=4 pour obtenir de beaux tableaux. Notez que vous ne pourrez ni redimensionner ni modifier la structure de vos tableaux une fois cr��s.</li>
</ul>

<h4>Transf�rer un document</h4>
<ul>
  <li>S&eacute;lectionnez le document sur votre ordinateur &agrave; l'aide
 du
	bouton &quot;Parcourir...&quot;
	<input type=\"button\" value=\"Parcourir...\">
	&agrave; droite de votre &eacute;cran.</li>
  <li>Ex&eacute;cutez le transfert &agrave; l'aide du
 bouton &quot;Transf�rer&quot;
	<input type=\"button\" value=\"Transf�rer\">
	.</li>
</ul>
<h4>Renommer un document (ou un r&eacute;pertoire)</h4>
<ul>
  <li>cliquez sur le bouton <img src=\"../img/edit.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">
	dans la colonne &quot;Modifier&quot;.</li>
  <li>Tapez le nouveau nom dans la zone pr&eacute;vue &agrave; cet effet.</li>
  <li>Valider en cliquant sur &quot;Valider&quot;
	<input type=\"button\" value=\"Valider\">
	.
</ul>
	<h4>Supprimer un document (ou un r&eacute;pertoire)</h4>
	<ul>

  <li>Cliquer sur le bouton <img src=\"../img/delete.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">
	dans la colonne &quot;Effacer&quot;.</li>
	</ul>
	<h4>Rendre un document (ou un
 r&eacute;pertoire) invisible aux membres</h4>
	<ul>

  <li>Cliquez sur le bouton <img src=\"../img/visible.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">dans
	la colonne &quot;Visible/invisible&quot;.</li>
	  <li>Le document (ou le r&eacute;pertoire) existe toujours, mais il n'est

		plus visible pour les membres.</li>
	</ul>
	<ul>

  <li> Si vous souhaitez rendre cet &eacute;l&eacute;ment &agrave; nouveau
 visible,
	cliquez sur le bouton <img src=\"../img/invisible.gif\" width=24 height=20 align=\"absmiddle\">
	dans la colonne Visible/invisible</li>
	</ul>
	<h4>Ajouter ou modifier un commentaire au document (ou au
 r&eacute;pertoire)</h4>
	<ul>

  <li>Cliquez sur le bouton <img
 src=\"../img/comment.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">
	dans la colonne &quot;Modifier&quot;</li>
	  <li>Tapez le nouveau commentaire dans la zone pr&eacute;vue &agrave; cet

		effet.</li>
	  <li>Validez en cliquant sur &quot;Valider&quot;
		<input type=\"button\" value=\"Valider\">
		.</li>
	</ul>
	<p>Si vous souhaitez supprimer un commentaire, cliquez sur le bouton <img
 src=\"../img/comment.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">,
	  effacez l'ancien commentaire de la zone et validez en cliquant
 &quot;Valider&quot;
	  <input type=\"button\" value=\"Valider\">
	  .
	<hr>
	<p>Vous pouvez aussi organiser le contenu du module de document en
 rangeant
	  les documents dans des r&eacute;pertoires. Pour ce faire vous devez :</p>
	<h4><b>Cr&eacute;er un r&eacute;pertoire</b></h4>
	<ul>
	  <li>Cliquez sur le lien &quot;<img
 src=\"../img/file.gif\" width=\"20\" height=\"20\" align=\"absmiddle\">Cr&eacute;er
		un r&eacute;pertoire&quot; en haut de la liste des fichiers</li>
	  <li>Tapez le nom de votre nouveau r&eacute;pertoire dans la zone
 pr&eacute;vue
		&agrave; cet effet en haut &agrave; gauche de l'&eacute;cran.</li>
	  <li>Validez en cliquant &quot;Valider&quot;
		<input type=\"button\" value=\"Valider\">
		.</li>
	</ul>
	<h4>D&eacute;placer un document (ou un r&eacute;pertoire)</h4>
	<ul>
	  <li>Cliquez sur le bouton <img src=\"../img/move.gif\" width=\"34\" height=\"16\" align=\"absmiddle\">
		dans la colonne &quot;D&eacute;placer&quot;</li>
	  <li>Choisissez le r&eacute;pertoire dans lequel vous souhaitez
 d&eacute;placer
		le document ou le r&eacute;pertoire dans le menu d&eacute;roulant
 pr&eacute;vu
		&agrave; cet effet qui appara&icirc;tra en haut &agrave; gauche (note:
		le mot &quot;root&quot; dans ce menu repr&eacute;sente la racine de
		votre module document).</li>
	  <li>Validez en cliquant &quot;Valider&quot;
		<input type=\"button\" value=\"Valider\">.</li>
	</ul>
<h4>Cr�er un Parcours d'apprentissage</h4>L'outil de Parcours vous permet de construire des itin�raires dans le contenu et les activit�s. Le r�sultat ressemblera � une Table des mati�res mais offrira bien plus de possiblit�s qu'une Table des mati�res ordinaires. Voir l'aide de l'outil Parcours.</p>";



// help.php?open=User

$langHUser="Aide membres";
$langUserContent="L'outil Membres fournit la liste des personnes inscrites au espace. Elle offre en outre les fonctionnalit�s suivantes:
<ul><li><b>Nom et pr�nom</b> : pour acc�der � la fiche de l'utilisateur contenant sa photo, son adresse email et d'autres informations, cliquez sur son nom</li>
<li><b>Description</b> : remplissez ce champ pour donner informer les autres membres du r�le jou� par l'un d'entre eux dans votre dispositif</li>
<li><b>Editer (crayon jaune)</b> : permet d'attribuer des droits suppl�mentaires, comme celui de partager avec vous la responsabilit� d'administrer cet espace ou bien celui, plus modeste, de mod�rer les �changes dans les groupes</li>
<li><b>Suivi</b> : vous renseigne sur l'utilisation de l'espace par le membre/le membre. Combien de fois il/elle est venu(e), combien de points il/elle a obtenu aux tests, combien de temps il (elle) a pass� dans les modules d'espaces Scorm, quels documents il/elle a d�pos�s dans l'outil Travaux, etc.</li>
</ul>
Vous pouvez aussi, dans la page Membres, inscrire des membres � votre espace (ne le faites que si ils/elles ne sont pas encore inscrits dans le portail), g�rer les espaces des groupes ou d�finir des intitul�s qui permettront aux �tudiants de se d�crire ou de se pr�senter aux autres : num�ro de t�l�phone, curriculum vitae etc.


<p><b>Co-responsabilit� d'un espace</b>
<p>Pour permettre � un co-responsable de votre espace de l'administrer avec vous, vous devez pr�alablement
 lui demander de s'inscrire � votre espace ou vous assurer qu'il est inscrit puis modifier
 ses droits en cliquant sur l'ic�ne d'�dition puis sur 'Responsable'.</P>
<p>Pour faire figurer le nom de ce co-responsable dans l'en-t�te de votre
 espace, utilisez la page 'Propri�t�s de cet espace' (dans les outils orange
 sur la page d'accueil de votre espace). Cette modification de l'en-t�te
 de l'espace n'inscrit pas automatiquement ce co-responsable comme membre de l'espace. Ce sont deux actions distinctes.</p>";



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
	si les membres sont d�j� inscrits au espace, ce qui peut �tre v�rifi� via l'outil
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

// help.php?open=Dropbox

$langHDropbox="Dropbox";

$langDropboxContent="L'outil de partage affiche les fichiers qui vous ont �t� envoy�s
(dossier Recu) et les fichiers que vous avez communiqu�s � d'autres membres
(dossier Envoy�). Si vous envoyez deux fois un fichier du m�me nom, vous pouvez choisir d'�craser
le premier envoi par le second.
<br>
<br>
Comme membre, vous pouvez seulement envoyer un fichier au responsable de l'espace,
� moins que le gestionnaire syst�me ait activ� le partage entre les membres.
<br>
<br>
Un responsable peut choisir d'envoyer un fichier � tous les membres de l'espace.
<br><br>
L'administrateur syst�me peut activer l'envoi de fichiers sans destinataire.
<br><br>
Si la liste des fichiers devient trop longue, vous pouvez supprimer certains fichiers ou
tous les fichiers. Le fichier lui-m�me n'est toutefois pas supprim� pour les autres membres
qui y ont acc�s � moins que tous le suppriment.
<br>";




$langHPath="Aide outil Parcours";

$langPathContent="<br>L'outil Parcours a deux fonctions :
<ul><li>Cr�er un parcours</li>
<li>Importer un parcours au format Scorm ou IMS</li></ul>
<img src=\"../img/path_help.gif\">

<p><b>
Qu'est-ce qu'un parcours?</b>
</p><p>Un parcours est une s�quence d'apprentissage d�coup�e en modules eux-m�mes d�coup�s en �tapes. Il peut �tre organis� en fonction d'un contenu, il constituera alors une sorte de Table des mati�res, ou bien en fonction d'activit�s, il s'apparentera alors � un Agenda de 'choses � faire' pour acqu�rir la ma�trise d'un savoir, d'une comp�tence. Il vous appartient de baptiser les modules successifs de votre parcours 'chapitres', 'semaines', 'modules', 's�quences' ou toute autre appellation r�pondant � la nature de votre sc�nario p�dagogique.</p><p>En plus d'�tre structur�, un parcours peut �tre s�quenc�. Cela signifie que certaines �tapes peuvent constituer des pr�-requis pour d'autres ('Vous ne pouvez aller � l'�tape 2 avant d'avoir parcouru l'�tape 1'). Votre s�quence peut �tre suggestive (vous montrez les �tapes l'une apr�s l'autre) ou contraignante (le membre est oblig� de suivre les �tapes dans un ordre impos�).
</p>
<p><b>Comment cr�er un parcours?</b></p>
<p>
Cliquez sur Cr�er un parcours > Cr�er un nouveau parcours > Cr�er un module > Ajouter une �tape (=un document, une activit�, un outil etc.). Pour ajouter des �tapes, il vous suffit ensuite de parcourir les outils dans le menu de gauche puis d'ajouter les documents, les activit�s, forums, travaux etc. Cliquez sur Retour � 'nom du parcours' pour revenir au parcours d�sormais rempli d'�tapes et cliquez sur 'Vue �tudiant' pour un aper�u du parcours (pour revenir � la vue de l'responsable, cliquez sur la maison dans le coin sup�rieur droit puis sur Vue responsable).</p><p>Ensuite param�trez plus finement votre parcours pour:
<ul><li>renommer le titre des documents, des outils, des liens etc. afin de constituer une v�ritable 'table des mati�re pour le membre</li>
<li>r�ordonner les �tapes en fonction de votre sc�nario d'espaces : ic�nes en triangle blanc vers le haut et vers le bas</li>
<li>�tablir une s�quence en ajoutant des pr�requis: � l'aide de l'ic�ne grise repr�sentant deux documents, d�finissez quelle �tape est pr�requise pour l'�tape courante</li>
<li>d�finir si le parcours est visible ou invisible : si vous s�lectionnez visible, le parcours appra�tra sur la page d'accueil de l'espace</li>
</ul>
Il est important de comprendre qu'un parcours est plus que le d�coupage d'une mati�re : il est un itin�raire � travers le savoir qui inclut potentiellement des �preuves, des temps de discussion, d'�valuation, d'exp�rimentation, de publication, de regard-crois�... C'est pourquoi l'outil de parcours de Dokeos constitue une sorte de m�ta-outil permettant de puiser dans l'ensemble des autres outils pour s�quencer:
<ul>
<li>�v�nements de l'agenda</li>
<li>documents de toute nature : pages web, images, fichiers Word, PowerPoint etc.</li>
<li>Annonces</li>
<li>Forums</li>
<li>Sujets dans les forums</li>
<li>Messages dans les forums</li>
<li>Liens (ils s'ouvriront dans une fen�tre s�par�e)</li>
<li>Tests (n'oubliez pas de les rendre visibles dans l'outil de tests)</li>
<li>Page de travaux (o� les �tudiants peuvent envoyer leur copie)</li>
<li>Partage de fichiers (pour �changer des brouillons, travailler � plusieurs voix...)</li>
</ul>
</p><p><b>
Qu'est-ce qu'un parcours Scorm ou IMS et comment l'importer?</b>
</p>
<p>Outre la possibilit� qu'il vous offre de CONSTRUIRE des parcours, l'outil Parcours ACCUEILLE vos contenus e-Learning conformes � la norme Scorm. Ceux-ci peuvent �tre import�s sous forme de fichiers compress�s au format ZIP (seul ce format est accept�). Vous avez peut-�tre acquis des licences sur de tels espace ou bien vous pr�f�rez construire vos parcours localement sur votre disque dur plut�t que directement en ligne sur Dokeos. Dans ce cas, lisez ce qui suit.</p>
<p>SCORM (<i>Sharable Content Object Reference Model</i>) est un standard public respect� par les acteurs majeurs du e-Learning: NETg, Macromedia, Microsoft, Skillsoft, etc. Ce standard agit � trois niveaux:
</p>
<ul>
<li><b>Economique</b> : gr�ce au principe de s�paration du contenu et du contexte, Scorm permet de r�utiliser des espaces entiers ou des morceaux d'espaces dans diff�rents <i>Learning Management Systems</i> (LMS),</li>
<li><b>P&eacute;dagogie</b> : Scorm int�gre la notion de pr�-requis ou de <i>s�quence</i> (p.ex. \"Vous ne pouvez pas entrer dans le chapitre 2 tant que vous n'avez pas pass� le Quiz 1\"),</li>
<li><b>Technologie</b> : Scorm g�n�re une table des mati�res ind�pendante tant du contenu que du LMS. Ceci permet de faire communiquer contenu et LMS pour sauvegarder entre autres : la <i>progression</i> de l'apprenant (\"A quel chapitre de l'espace Jean est-il arriv�?\"), les r�sultats</i> (\"Quel est le r�sultat de Jean au Quiz 1?\") et le <i>temps</i> (\"Combien de temps Jean a-t-il pass� dans le chapitre 4?\").</li>
</ul>
<b>Comment g�n�rer localement (sur votre disque dur) un espace compatible Scorm?</b><br>
<br>
Utilisez des outils auteurs comme Dreamweaver, Lectora et/ou Reload puis sauvegardez votre parcours comme un fichier ZIP et t�l�chargez-le dans l'outil \"Parcours\".<br>
<br>
<b>Liens utiles</b><br>
<ul>
<li>Adlnet : autorit&eacute; responsable de la norme Scorm, <a
href=\"http://www.adlnet.org/\">http://www.adlnet.org</a></li>
<li>Reload : Editeur et player Scorm Open Source et gratuits, <a
href=\"http://www.reload.ac.uk/\">http://www.reload.ac.uk</a></li>
<li>Lectora : Logiciel auteur permettant d'exporter au format Scorm, <a
href=\"http://www.trivantis.com/\">http://www.trivantis.com</a><br>
</li>
</ul><b>
</p>";



$langHDescription="Aide outil Description";

$langDescriptionContent="<p>L'outil Description de l'espace vous invite � d�crire votre espace de mani�re synth�tique et globale dans une logique de cahier des charges. Cette description pourra servir � donner aux �tudiants ou aux participants un avant-go�t de ce qui les attend. Pour d�crire l'espace chronologiquement �tape par �tape, pr�f�rez l'Agenda ou le Parcours.</p>Les rubriques sont propos�es � titre de suggestion. Si vous souhaitez r�diger une description de l'espace qui ne tienne aucun compte de nos propositions, il vous suffit de ne cr�er que des rubriques 'Autre'.</p>
<p>Pour remplir la Description de l'espace, cliquez sur Cr�er et �diter une description... > D�roulez le menu d�roulant et s�lectionnez la rubrique de votre choix puis validez. Remplissez ensuite les champs. Il vous sera � tout moment possible de d�truire ou de modifier une rubrique en cliquant sur le crayon ou sur la croix rouge.</p>"; 



$langHLinks="Aide outil Liens";

$langLinksContent="<p>L'outil Liens vous permet de constituer une biblioth�que de ressources pour vos �tudiants et en particulier de ressources que vous n'avez pas produites vous-m�me.</p>
<p>Lorsque la liste s'allonge, il peut �tre utile d'organiser les liens en cat�gories afin de faciliter la recherche d'information par vos �tudiants. Veillez � v�rifier de temps en temps si les liens sont toujours valides.</p>
<p>Le champ description peut �tre utilis� de mani�re p�dagogiquement dynamique en y ajoutant non pas n�cessairement la description des documents ou des sites eux-m�mes, mais la description de l'activit� que vous attendez de vos �tudiants par rapport aux ressources. Si vous pointez, par exemple, vers une page sur Aristote, le champ Description peut inviter � �tudier la diff�rence entre synth�se et analyse. "; 

$langHAgenda="Aide Agenda";

$langAgendaContent="<p>L'agenda est un outil qui prend place � la fois dans chaque espace et comme outil de synth�se pour le membre ('Mon agenda') reprenant l'ensemble des �v�nements relatifs aux espace dans lesquels il est inscrit.</p>Depuis Dokeos 1.5.4 il est possible d'ajouter des annexes aux �v�nements : documents, liens divers. Ceci permet de traiter l'agenda comme un outil de programmation de l'apprentissage jour apr�s jour ou semaine apr�s semaine qui renvoie aux contenus et aux activit�s.</p>Toutefois, si l'on souhaite organiser les activit�s dans le temps de fa�on structur�e, il peut �tre pr�f�rable d'utiliser l'outil Parcours qui permettra de construire de v�ritables s�quences � travers le temps, les activit�s ou le contenu en pr�sentant l'espace selon une logique formelle de table des mati�res.</p>"; 

$langHGroups="Aide Groupes";

$langGroupsContent="<p>L'outil de groupes vous permet de fournir � des groupes d'�tudiants des espacess privatifs pour �changer des documents et discuter dans un forum. L'outil de documents des groupes leur permet, en outre, de publier un document dans 'Travaux' une fois ce document jug� d�finitif. On peut ainsi passer d'une logique de travail confin� � une logique de diffusion vers l'formateur/responsable ou vers les membres des autres groupes.</p>
<b>Remplir les groupes</b>
<p>Il existe 3 mani�res de remplir les groupes:
<ol><li>soit les participants s'auto-inscrivent dans les groupes dans la limite des places disponibles</li>
<li>soit ils sont inscrits manuellement un � un par le responsable,</li>
<li>soit les groupes sont remplis de fa�on automatique au hasard</li></ol>
Pour 1 : il faut �diter les Param�tres des groupes (milieu de la page) pour v�rifier que la case 'auto-inscription' est coch�e. Pour 2 : il faut cr�er des groupes (coin sup�rieur gauche) puis �diter chacun des groupes et le remplir en faisant passer les personnes du menu de gauche vers le menu de droite (CTRL+ clic ou POMME+ clic pour s�lectionner plusieurs personnes en m�me temps). Pour 3 : il faut cliquer sur 'Remplir les groupes au hasard'. Attention : 2 et 3 ne fonctionnent que si les participants sont d�j� inscrits au espace pr�alablement.</p>
<b>Editer les groupes</b>
<p>Editer les espaces des groupes (crayon jaune) permet de les renommer, de leur ajouter un descriptif (t�ches du groupe, num�ro de t�l�phone du coach...), de modifier leurs param�tres et de modifier leur composition, de leur ajouter un mod�rateur (ou coach). Pour cr�er un groupe uniquement pour les mod�rateurs, cr�er un groupe dont le nombre maximum de participants est z�ro (car les mod�rateurs ont tous acc�s � tous les groupes par d�faut).";


$langHAnnouncements="Aide Annonces";

$langAnnouncementsContent="<p>L'outil d'Annonces vous permet d'envoyer un message par courriel aux �tudiants/apprenants. Que ce soit pour leur signaler que vous avez d�pos� un nouveau documents, que la date de remise des rapports approche ou qu'untel a r�alis� un travail de qualit�, l'envoi de courriels, s'il est utilis� avec mod�ration, permet d'aller chercher les participants et peut-�tre de les ramener au site web s'il est d�sert�.</p>
<b>Message pour certains membres</b>
<p>Outre l'envoi d'un courriel � l'ensemble des membres de l'espace, vous pouvez envoyer un courriel � une ou plusieurs personnes et/ou un ou plusieurs groupes. Dans ce nouvel outil, utilisez CTRL+clic pour s�lectionner plusieurs �l�ments dna le menu de gauche puis cliquez sur la fl�che droite pour les amener dans le menu de droite. Tapez ensuite votre message dans le champ de saisie situ� en bas de la page.";

$langHChat="Aide Discussion";

$langChatContent="<p>L'outil de discussion est un 'chat' ou 'clavardage' qui vous permet de discuter en direct avec vos �tudiants/participants.</p>
<p>A la diff�rence des outils de chat que l'on trouve sur le march�, ce 'chat' fonctionne dans une page web et non � l'aide d'un client additionnel � t�l�charger : Microsoft Messenger&reg;, Yahoo! Messenger&reg; etc. L'avantage de cette solution est l'universalit� garantie de son utilisation sur tous ordinateurs et sans d�lai. L'inconv�nient est que la liste des messages ne se rafraichit pas instantam�ment mais peut prendre de 5 � 10 secondes.</p>
<p>Si les �tudiants/participants ont envoy� leur photo dans l'outil 'Mon profil', celle-ci appara�tra en r�duction � c�t� de leurs messages. Sinon, ce sera une photo par d�faut en noir sur fond blanc.</p>
<p>Il appartient au responsable d'effacer les discussions quand il/elle le juge pertinent. Par ailleurs, ces discussions sont archiv�es automatiquement dans l'outil 'Documents'.</p>
<b>Usages p�dagogiques</b>
<p>Si l'ajout d'un 'chat' dans l'espace n'apporte pas n�cessairement une valeur ajout�e dans les processus d'apprentissage, une utilisation m�thodique de celui-ci peut apporter une r�elle contribution. Ainsi, vous pouvez fixer des rendez-vous de questions-r�ponses � vos membres et d�sactiver l'outil le reste du temps, ou bien exploiter l'archivage des discussions pour revenir en classe sur un sujet abord� dans le pass�.";





$langHWork="Aide Travaux";

$langWorkContent="<p>L'outil Travaux est un outil tr�s simple permettant � vos �tudiants/participants d'envoyer des documents vers l'espace. Il peut servir � r�ceptionner des rapports individuels ou collectifs, des r�ponses � des questions ouvertes ou toute autre forme de document.</p>
<p>Beaucoup de responsables/d'responsables masquent l'outil Travaux jusqu'� la date de remise des rapports. Vous pouvez aussi pointer vers cet outil par un lien depuis le texte d'introduction de votre espace ou l'agenda. L'outil Travaux dispose lui aussi d'un texte d'introduction qui pourra vous servir � formuler une question ouverte, � pr�ciser les consignes pour la remise de rapports ou toute autre information.</p>
<p>Les travaux sont soit publics soit � destination du seul responsable. Publics, ils serviront un dispositif de regard crois� dans lequel vous invitez les participants � commenter mutuellement leurs productions selon un sc�nario et des crit�res �ventuellement formul�s dans le Texte d'intruduction. Priv�s, ils seront comme une bo�te aux lettres du responsable/ de l'responsable.";



$langHTracking="Aide Suivi statistique";

$langTrackingContent="<p>L'outil de suivi statistique vous permet de suivre l'�volution de l'espace � deux niveaux:
<ul><li><b>Globalement</b>: quelles sont les pages les plus visit�es, quel est le taux de connection par semaine...?</li>
<li><b>Nominativement</b>: quelles pages Jean Dupont a vues et quand, quels r�sultats a-t-il obtenu aux exercices, combien de temps est-il rest� dans chaque chapitre d'un espace Scorm, quels traavaux a-t-il d�pos� et � quelle date?</li></ul>
Pour obtenir les statistiques nominatives, cliquez sur 'Membres'. Pour les statistiques globales, cliquez sur 'Montrer tout'.</p>
<p>";


$langHSettings="Aide Propri�t�s de l'espace";

$langSettingsContent="<p>L'outil 'Propri�t�s de l'espace' vous permet de modifier le comportement global de votre espace.</p>
<p>La partie sup�rieure de la page permet de modifier les rubriques qui apparaissent dans l'ent�te de votre espace: nom du responsable/de l'responsable (n'h�sitez pas � en introduire plusieurs), intitul� de l'espace, code, langue. Le d�partement est facultatif et peut repr�senter un sous-ensemble de votre organisation : cellule, groupe de travail etc.</p>
<p>La partie m�diane de la page vous permet de d�terminer les param�trs de confidentialit�. Une utilisation classique consiste � fermer tout acc�s au espace pendant la p�riode de fabrication (pas d'acc�s, pas d'inscription), d'ouvrir ensuite � l'inscription mais non � la visibilit� publique, et ce le temps n�cessaire pour que chacun des participants s'inscrive, puis de refermer l'inscription et d'aller dans Membres chasser les �ventuels intrus. Certaines organisations pr�f�rent ne pas utiliser cette m�thode et recourir � une inscription administrative centralis�e. Dans ce cas, les participants n'ont pas m�me l'opportunit� de s'inscrire � votre espace, quand bien m�me vous, en tant que formateur/responsable, leur en donneriez l'acc�s. Observez donc la page d'accueil de votre portail (non celle de votre espace) pour voir si le lien 'S'inscrire' est pr�sent.</p>
<p>La partie inf�rieure de le page permet d'effectuer une sauvegarde de l'espace et/ou de supprimer celui-ci. La sauvegarde copiera une archive ZIP de votre espace sur le serveur et vous permettra en outre de la r�cup�rer sur votre ordinateur local par t�l�chargement. C'est une fa�on commode de r�cup�rer l'ensemble des documents qui se trouvent dans votre espace. Il vous faudra utiliser un outil de d�compression genre Winzip&reg; pour ouvrir l'archive une fois r�cup�r�e.";



$langHExternal="Aide Ajouter un lien";

$langExternalContent="<p>Dokeos est un outil modulaire. Il vous permet de masquer et d'afficher les outils � votre guise. Poussant plus loin cette logique, Dokeos vous permet aussi d'ajouter des liens sur votre page d'accueil.</p>
Ces liens peuvent �tre de deux types:
<ul><li><b>Lien externe</b> : par exemple vous renvoyez ver le site Google, http://www.google.be. Choisissez alors comme destination du lien : Dans une autre fen�tre,</li>
<li><b>Lien interne</b> : vous pouvez cr�er un raccourci sur votre page d'accueil qui pointe directement vers n'importe quelle page ou outil situ� � l'int�rieur de votre espace. Pour ce faire, rendez-vous sur cette page ou dans cet outil, copiez (CTRL+C) l'URL de la page, revenez sur la page d'accueil, ouvrez Ajouter un lien et collez (CTRL+V) l'URL de la page dans le champ URL puis donnez-lui le nom de votre choix. Dans ce cas, vous choisirez pr�f�rablement comme destination du lien : Dans la m�me fen�tre.</li></ul>
Remarque : une fois cr��s, les liens sur page d'accueil ne peuvent pas �tre modifi�s. Il vous faudra les masquer, puis les d�truire, puis recommencer en partant de z�ro.</p>";




$langHMycourses="Aide Ma page d'accueil";

$langMycoursesContent="<p>Une fois identifi� dans le syst�me, vous �tes ici sur <i>votre</i> page. Vous voyez:
<ul><li><b>Mes espaces</b> au milieu de la page, ainsi que la possibilit� de cr�er de nouveaux espace (bouton dans le menu de droite),</li>
<li>Dans l'ent�te, <b>Mon profil</b>: vous pouvez modifier l� votre mot de passe, importer votre photo dans le syst�me, modifier votre nom d'utilisateur,</li>
<li><b>Mon agenda</b>: il contient les �v�nements des espaces auxquels vous �tes inscrit,</li>

<li>Dans le menu de droite : <b>Modifier ma liste d'espaces</b> qui vous permet de vous inscrire � des espaces comme apprenant, si le responsable/l'responsable a autoris� l'inscription. C'est l� aussi que vous pourrez vous d�sinscrire d'un espace,</li>
<li>Les liens <b>Forum de Support</b> et <b>Documentation</b> vous renvoient vers le site central de Dokeos, o� vous pourrez poser des questions ou trouver des compl�ments d'information.</li></ul>
Pour entrer dans un espace (partie gauche de l'�cran), cliquez sur son intitul�. Votre profil peut varier d'un espace � l'autre. Il se pourrait que vous soyez responsable dans tel espace et apprenant dans un autre. Dans les espaceso� vous �tes responsable, vous disposez d'outils d'�dition, dans les espaceso� vous �tes apprenant, vous acc�dez aux outils sur un mode plus passif.</p>
<p>La disposition de <i>votre</i> page peut varier d'une organisation � l'autre selon les options qui ont �t� activ�es par l'administrateur syst�me. Ainsi il est possible que vous n'ayez pas acc�s � la fonction de cr�ation d'espaces, m�me en tant que responsable, parce que cette fonction est g�r�e par une administration centrale.";





?>
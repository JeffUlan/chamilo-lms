<?php
/*
      +----------------------------------------------------------------------+
      | CLAROLINE version 1.3.0 $Revision: 3594 $                            |
      +----------------------------------------------------------------------+
      | Copyright (c) 2001, 2002 Universite catholique de Louvain (UCL)      |
      +----------------------------------------------------------------------+
      |   $Id: exercice.inc.php 3594 2005-03-03 12:06:49Z olivierb78 $     |
      +----------------------------------------------------------------------+
      |   This program is free software; you can redistribute it and/or      |
      |   modify it under the terms of the GNU General Public License        |
      |   as published by the Free Software Foundation; either version 2     |
      |   of the License, or (at your option) any later version.             |
      +----------------------------------------------------------------------+
      | Authors: Thomas Depraetere <depraetere@ipm.ucl.ac.be>                |
      |          Hugues Peeters    <peeters@ipm.ucl.ac.be>                   |
      |          Christophe Gesch� <gesche@ipm.ucl.ac.be>                    |
      |          Olivier Brouckaert <oli.brouckaert@skynet.be>               |
      +----------------------------------------------------------------------+
*/

/***************************************************************
*                   Language translation
****************************************************************
GOAL
****
Translate the interface in chosen language

*****************************************************************/

// general

$langExercice="Quizz";
$langExercices="Quizz";
$langQuestion="Question";
$langQuestions="Questions";
$langAnswer="R�ponse";
$langAnswers="R�ponses";
$langActivate="Activer";
$langDeactivate="D�sactiver";
$langComment="Commentaire";
$langOk="Valider";


// exercice.php

$langNoEx="Il n'y a aucun Quizz actuellement";
$langNoResult="Il n'y a pas encore de r�sultats";

// question_pool.php

$langQuestionPool="Banque de questions";
$langOrphanQuestions="Questions orphelines";
$langNoQuestion="Il n'y a aucune question actuellement";
$langAllExercises="Tous les Quizz";
$langFilter="Filtre";
$langUnknownExercise="Quizz inconnu";
$langGoBackToEx="Retour au Quizz";
$langReuse="R�cup�rer";
$langReuseQuestion="R�cup�rer une question existante";


// [exercice/question/answer]_admin.php

$langElementList="Liste des �l�ments";
$langWeightingForEachBlank="Veuillez donner une pond�ration � chacun des blancs";
$langUseTagForBlank="utilisez des crochets [...] pour cr�er un ou des blancs";
$langExerciseType="Type de Quizz";
$langAnswerType="Type de r�ponse";
$langUniqueSelect="Choix multiple (R�ponse unique)";
$langMultipleSelect="Choix multiple (R�ponses multiples)";
$langFillBlanks="Remplissage de blancs";
$langMatching="Correspondance";
$langAddPicture="Ajouter une image";
$langReplacePicture="Remplacer l'image";
$langDeletePicture="Supprimer l'image";
$langQuestionWeighting="Pond�ration";
$langExerciseName="Intitul� du Quizz";
$langCreateExercise="Cr�er un Quizz";
$langCreateQuestion="Cr�er une question";
$langCreateAnswers="Cr�er des r�ponses";
$langModifyExercise="Modifier un Quizz";
$langModifyQuestion="Modifier une question";
$langModifyAnswers="Modifier des r�ponses";
$langNewEx="Nouveau Quizz";
$langNewQu="Nouvelle question";
$langExerciseDescription="Description du Quizz";
$langQuestionDescription="Description de la question";
$langTrue="Vrai";
$langMoreAnswers="+r�p";
$langLessAnswers="-r�p";
$langMoreElements="+�lem";
$langLessElements="-�lem";
$langTypeTextBelow="Veuillez introduire votre texte ci-dessous";
$langExerciseNotFound="Quizz introuvable";
$langQuestionNotFound="Question introuvable";
$langQuestionList="Liste des questions";
$langForExercise="pour le Quizz";
$langMoveUp="D�placer vers le haut";
$langMoveDown="D�placer vers le bas";
$langSimpleExercise="Questions sur une seule page";
$langSequentialExercise="Une question par page (s�quentiel)";
$langRandomQuestions="Questions al�atoires";
$langDefaultTextInBlanks="Les [anglais] vivent en [Angleterre].";
$langDefaultMatchingOptA="rich";
$langDefaultMatchingOptB="good looking";
$langDefaultMakeCorrespond1="Your dady is";
$langDefaultMakeCorrespond2="Your mother is";
$langUseExistantQuestion="Utiliser une question existante";
$langUsedInSeveralExercises="Attention ! Cette question et ses r�ponses sont utilis�es dans plusieurs Quizz. Souhaitez-vous les modifier";
$langModifyInAllExercises="pour l'ensemble des Quizz";
$langModifyInThisExercise="uniquement pour le Quizz courant";
$langDefineOptions="D�finissez la liste des options";
$langMakeCorrespond="Faites correspondre";
$langAmong="parmi";
$langGiveExerciseName="Veuillez introduire l'intitul� du Quizz";
$langFillLists="Veuillez remplir les deux listes ci-dessous";
$langGiveText="Veuillez introduire le texte";
$langDefineBlanks="Veuillez d�finir au moins un blanc en utilisant les crochets [...]";
$langGiveQuestion="Veuillez introduire la question";
$langGiveWeighting="Veuillez introduire la pond�ration de cette question";
$langGiveAnswers="Veuillez fournir les r�ponses de cette question";
$langChooseGoodAnswer="Veuillez choisir une bonne r�ponse";
$langChooseGoodAnswers="Veuillez choisir une ou plusieurs bonnes r�ponses";
$langTotalWeightingMultipleChoice="La somme des pond�rations des r�ponses coch�es doit �tre �gale � la pond�ration totale de la question";
$langTotalWeightingFillInBlanks="La somme des pond�rations des blancs doit �tre �gale � la pond�ration totale de la question";
$langTotalWeightingMatching="La somme des pond�rations des correspondances doit �tre �gale � la pond�ration totale de la question";


// exercice_submit.php

$langResult="R�sultat";
$langCorrect="Correct";
$langCorrespondsTo="Correspond �";
$langAlreadyAnswered="Vous avez d�j� r�pondu � la question";
$langShowQuestion="Visualiser une question";
?>
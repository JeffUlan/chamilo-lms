/*************************************************************
 *
 *  MathJax/localization/sk/TeX.js
 *
 *  Copyright (c) 2009-2016 The MathJax Consortium
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 */
MathJax.Localization.addTranslation("sk","TeX",{
        version: "2.7.2",
        isLoaded: true,
        strings: {
          ExtraOpenMissingClose: "Prebyto\u010Dn\u00E1 otv\u00E1raj\u00FAca alebo uzavieracia z\u00E1tvorka",
          ExtraCloseMissingOpen: "Prebyto\u010Dn\u00E1 uzavieracia alebo otv\u00E1raj\u00FAca z\u00E1tvorka",
          MissingLeftExtraRight: "Ch\u00FDbaj\u00FAci \\left alebo prebyto\u010Dn\u00FD \\right",
          MissingScript: "Ch\u00FDba argument horn\u00E9ho alebo doln\u00E9ho indexu",
          ExtraLeftMissingRight: "Prebyto\u010Dn\u00FD \\left alebo ch\u00FDbaj\u00FAci \\right",
          Misplaced: "Chybne umiestnen\u00FD %1",
          MissingOpenForSub: "Ch\u00FDbaj\u00FAca otv\u00E1racia zlo\u017Een\u00E1 z\u00E1tvorka pre doln\u00FD index",
          MissingOpenForSup: "Ch\u00FDbaj\u00FAca otv\u00E1racia zlo\u017Een\u00E1 z\u00E1tvorka pre horn\u00FD index",
          AmbiguousUseOf: "Nejednozna\u010Dn\u00E9 pou\u017Eitie %1",
          EnvBadEnd: "\\begin{%1} bolo uzavret\u00E9 \\end{%2}",
          EnvMissingEnd: "Ch\u00FDbaj\u00FAci \\end{%1}",
          MissingBoxFor: "Ch\u00FDba box pre %1",
          MissingCloseBrace: "Ch\u00FDba uzavieracia z\u00E1tvorka",
          UndefinedControlSequence: "Nedefinovan\u00E1 riadiaca sekvencia %1",
          DoubleExponent: "Dvojit\u00FD exponent: pre jednozna\u010Dnos\u0165 pou\u017Eite zlo\u017Een\u00E9 z\u00E1tvorky",
          DoubleSubscripts: "Dvojit\u00FD doln\u00FD index: pre jednozna\u010Dnos\u0165 pou\u017Eite zlo\u017Een\u00E9 z\u00E1tvorky",
          DoubleExponentPrime: "Symbol \u010Diarky sp\u00F4sobil dvojit\u00FD exponent: pre jednozna\u010Dnos\u0165 pou\u017Eite zlo\u017Een\u00E9 z\u00E1tvorky",
          CantUseHash1: "V matematickom re\u017Eime nem\u00F4\u017Eete pou\u017Ei\u0165 znak \u201E#\u201C pre parametre makier",
          MisplacedMiddle: "%1 mus\u00ED by\u0165 medzi \\left a \\right",
          MisplacedLimits: "%1 je povolen\u00E9 len pri oper\u00E1toroch",
          MisplacedMoveRoot: "%1 sa m\u00F4\u017Ee vyskytn\u00FA\u0165 len v koreni",
          MultipleCommand: "Viacn\u00E1sobn\u00FD %1",
          IntegerArg: "Argument pre %1 mus\u00ED by\u0165 cel\u00E9 \u010D\u00EDslo",
          NotMathMLToken: "%1 nie je primit\u00EDvny element",
          InvalidMathMLAttr: "Neplatn\u00FD atrib\u00FAt MathML: %1",
          UnknownAttrForElement: "%1 nie je zn\u00E1mym atrib\u00FAtom pre %2",
          MaxMacroSub1: "Prekro\u010Den\u00FD maxim\u00E1lny po\u010Det substit\u00FAci\u00ED makra MathJaxu; nejde o rekurz\u00EDvne volanie makra?",
          MaxMacroSub2: "Prekro\u010Den\u00FD maxim\u00E1lny po\u010Det substit\u00FAci\u00ED MathJaxu; nejde o rekurz\u00EDvne LaTeXov\u00E9 prostredie?",
          MissingArgFor: "Ch\u00FDba argument pre %1",
          ExtraAlignTab: "Prebyto\u010Dn\u00FD vyrovn\u00E1vac\u00ED tabul\u00E1tor v texte \\cases",
          BracketMustBeDimension: "Z\u00E1tvorkov\u00FD argument pre %1 mus\u00ED by\u0165 rozmer",
          InvalidEnv: "Neplatn\u00E1 premenn\u00E1 prostredia \u201E%1\u201C",
          UnknownEnv: "Nezn\u00E1me prostredie \u201E%1\u201C",
          ExtraCloseLooking: "Prebyto\u010Dn\u00E1 uzavieracia z\u00E1tvorka, zatia\u013E \u010Do bolo o\u010Dak\u00E1van\u00E9 %1",
          MissingCloseBracket: "Pri argumente pre %1 nebola n\u00E1jden\u00E1 uzavieracia \u201E]\u201C",
          MissingOrUnrecognizedDelim: "Ch\u00FDbaj\u00FAci alebo nerozpoznan\u00FD odde\u013Eova\u010D pre %1",
          MissingDimOrUnits: "Ch\u00FDbaj\u00FAci rozmer alebo jeho jednotka pre %1",
          TokenNotFoundForCommand: "Nen\u00E1jden\u00E9 %1 pre %2",
          MathNotTerminated: "V textovom boxe nie je ukon\u010Den\u00E1 matematika",
          IllegalMacroParam: "Neplatn\u00FD odkaz na parameter makra",
          MaxBufferSize: "Prekro\u010Den\u00E1 ve\u013Ekos\u0165 internej pam\u00E4te MathJaxu; nejde o rekurz\u00EDvne volanie makra?",
          CommandNotAllowedInEnv: "V prostred\u00ED %2 nie je povolen\u00FD %1",
          MultipleLabel: "Viacn\u00E1sobn\u00E1 defin\u00EDcia ozna\u010Denia %1",
          CommandAtTheBeginingOfLine: "%1 mus\u00ED by\u0165 umiestnen\u00E9 na za\u010Diatku riadku",
          IllegalAlign: "Pri %1 uveden\u00E9 neplatn\u00E9 zarovnanie",
          BadMathStyleFor: "Chybn\u00FD \u0161t\u00FDl matematiky pri %1",
          PositiveIntegerArg: "Argument %1 mus\u00ED by\u0165 kladn\u00E9 cel\u00E9 \u010D\u00EDslo",
          ErroneousNestingEq: "Chybn\u00E9 zanorovanie \u0161trukt\u00FAry rovn\u00EDc",
          MultlineRowsOneCol: "Riadky v prostred\u00ED %1 musia ma\u0165 pr\u00E1ve jeden st\u013Apec",
          MultipleBBoxProperty: "Pri %2 je %1 uveden\u00E9 dvakr\u00E1t",
          InvalidBBoxProperty: "\u201E%1\u201C nevyzer\u00E1 ako farba, rozmer paddingu alebo \u0161t\u00FDl",
          ExtraEndMissingBegin: "Prebato\u010Dn\u00FD %1 alebo ch\u00FDbaj\u00FAci \\begingroup",
          GlobalNotFollowedBy: "Za %1 ch\u00FDba \\let, \\def alebo \\newcommand",
          UndefinedColorModel: "Farebn\u00FD model \u201E%1\u201C nie je definovan\u00FD",
          ModelArg1: "Farebn\u00E9 hodnoty modelu %1 vy\u017Eaduj\u00FA tri \u010D\u00EDsla",
          InvalidDecimalNumber: "Neplatn\u00E9 desatinn\u00E9 \u010D\u00EDslo",
          ModelArg2: "Farebn\u00E9 hodnoty modelu %1 musia le\u017Ea\u0165 medzi %2 a %3",
          InvalidNumber: "Neplatn\u00E9 \u010D\u00EDslo",
          NewextarrowArg1: "Prv\u00FDm argumentom %1 mus\u00ED by\u0165 n\u00E1zov riadiacej sekvencie",
          NewextarrowArg2: "Druh\u00FDm argumentom %1 musia by\u0165 dve cel\u00E9 \u010D\u00EDsla oddelen\u00E9 \u010Diarkou",
          NewextarrowArg3: "Tret\u00EDm argumentom %1 mus\u00ED by\u0165 \u010D\u00EDslo znaku Unicode",
          NoClosingChar: "Nen\u00E1jden\u00FD uzavierac\u00ED %1",
          IllegalControlSequenceName: "Neplatn\u00FD n\u00E1zov riadiacej sekvencie pre %1",
          IllegalParamNumber: "Pre %1 uveden\u00FD neplatn\u00FD po\u010Det parametrov",
          MissingCS: "Za %1 mus\u00ED by\u0165 riadiaca sekvencia",
          CantUseHash2: "Chybn\u00E9 pou\u017Eitie # v \u0161abl\u00F3ne pre %1",
          SequentialParam: "Parametre pre %1 musia by\u0165 \u010D\u00EDslovan\u00E9 postupne",
          MissingReplacementString: "V defin\u00EDcii %1 ch\u00FDba nahradzuj\u00FAci re\u0165azec",
          MismatchUseDef: "Pou\u017Eitie %1 nezodpoved\u00E1 jeho defin\u00EDcii",
          RunawayArgument: "Zbl\u00FAdil\u00FD argument pre %1?",
          NoClosingDelim: "Nepodarilo sa n\u00E1js\u0165 ukon\u010Dovac\u00ED znak pre %1"
        }
});

MathJax.Ajax.loadComplete("[MathJax]/localization/sk/TeX.js");

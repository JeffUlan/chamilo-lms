/*************************************************************
 *
 *  MathJax/extensions/TeX/mediawiki-texvc.js
 *  
 *  Implements macros used by mediawiki with their texvc preprocessor.
 *
 *  ---------------------------------------------------------------------
 *  
 *  Copyright (c) 2015-2017 The MathJax Consortium
 * 
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

MathJax.Extension["TeX/mediawiki-texvc"] = {
  version: "2.7.2"
};

MathJax.Hub.Register.StartupHook("TeX Jax Ready", function () {
  MathJax.InputJax.TeX.Definitions.Add({
    macros: {
      AA: ["Macro", "\u00c5"],
      alef: ["Macro", "\\aleph"],
      alefsym: ["Macro", "\\aleph"],
      Alpha: ["Macro", "\\mathrm{A}"],
      and: ["Macro", "\\land"],
      ang: ["Macro", "\\angle"],
      Bbb: ["Macro", "\\mathbb"],
      Beta: ["Macro", "\\mathrm{B}"],
      bold: ["Macro", "\\mathbf"],
      bull: ["Macro", "\\bullet"],
      C: ["Macro", "\\mathbb{C}"],
      Chi: ["Macro", "\\mathrm{X}"],
      clubs: ["Macro", "\\clubsuit"],
      cnums: ["Macro", "\\mathbb{C}"],
      Complex: ["Macro", "\\mathbb{C}"],
      coppa: ["Macro", "\u03D9"],
      Coppa: ["Macro", "\u03D8"],
      Dagger: ["Macro", "\\ddagger"],
      Digamma: ["Macro", "\u03DC"],
      darr: ["Macro", "\\downarrow"],
      dArr: ["Macro", "\\Downarrow"],
      Darr: ["Macro", "\\Downarrow"],
      dashint: ["Macro", "\\unicodeInt{x2A0D}"],
      ddashint: ["Macro", "\\unicodeInt{x2A0E}"],
      diamonds: ["Macro", "\\diamondsuit"],
      empty: ["Macro", "\\emptyset"],
      Epsilon: ["Macro", "\\mathrm{E}"],
      Eta: ["Macro", "\\mathrm{H}"],
      euro: ["Macro", "\u20AC"],
      exist: ["Macro", "\\exists"],
      geneuro: ["Macro", "\u20AC"],
      geneuronarrow: ["Macro", "\u20AC"],
      geneurowide: ["Macro", "\u20AC"],
      H: ["Macro", "\\mathbb{H}"],
      hAar: ["Macro", "\\Leftrightarrow"],
      harr: ["Macro", "\\leftrightarrow"],
      Harr: ["Macro", "\\Leftrightarrow"],
      hearts: ["Macro", "\\heartsuit"],
      image: ["Macro", "\\Im"],
      infin: ["Macro", "\\infty"],
      Iota: ["Macro", "\\mathrm{I}"],
      isin: ["Macro", "\\in"],
      Kappa: ["Macro", "\\mathrm{K}"],
      koppa: ["Macro", "\u03DF"],
      Koppa: ["Macro", "\u03DE"],
      lang: ["Macro", "\\langle"],
      larr: ["Macro", "\\leftarrow"],
      Larr: ["Macro", "\\Leftarrow"],
      lArr: ["Macro", "\\Leftarrow"],
      lrarr: ["Macro", "\\leftrightarrow"],
      Lrarr: ["Macro", "\\Leftrightarrow"],
      lrArr: ["Macro", "\\Leftrightarrow"],
      Mu: ["Macro", "\\mathrm{M}"],
      N: ["Macro", "\\mathbb{N}"],
      natnums: ["Macro", "\\mathbb{N}"],
      Nu: ["Macro", "\\mathrm{N}"],
      O: ["Macro", "\\emptyset"],
      oint: ["Macro", "\\unicodeInt{x222E}"],
      oiint: ["Macro", "\\unicodeInt{x222F}"],
      oiiint: ["Macro", "\\unicodeInt{x2230}"],
      ointctrclockwise: ["Macro", "\\unicodeInt{x2233}"],
      officialeuro: ["Macro", "\u20AC"],
      Omicron: ["Macro", "\\mathrm{O}"],
      or: ["Macro", "\\lor"],
      P: ["Macro", "\u00B6"],
      pagecolor: ['Macro','',1],  // ignore \pagecolor{}
      part: ["Macro", "\\partial"],
      plusmn: ["Macro", "\\pm"],
      Q: ["Macro", "\\mathbb{Q}"],
      R: ["Macro", "\\mathbb{R}"],
      rang: ["Macro", "\\rangle"],
      rarr: ["Macro", "\\rightarrow"],
      Rarr: ["Macro", "\\Rightarrow"],
      rArr: ["Macro", "\\Rightarrow"],
      real: ["Macro", "\\Re"],
      reals: ["Macro", "\\mathbb{R}"],
      Reals: ["Macro", "\\mathbb{R}"],
      Rho: ["Macro", "\\mathrm{P}"],
      sdot: ["Macro", "\\cdot"],
      sampi: ["Macro", "\u03E1"],
      Sampi: ["Macro", "\u03E0"],
      sect: ["Macro", "\\S"],
      spades: ["Macro", "\\spadesuit"],
      stigma: ["Macro", "\u03DB"],
      Stigma: ["Macro", "\u03DA"],
      sub: ["Macro", "\\subset"],
      sube: ["Macro", "\\subseteq"],
      supe: ["Macro", "\\supseteq"],
      Tau: ["Macro", "\\mathrm{T}"],
      textvisiblespace: ["Macro", "\u2423"],
      thetasym: ["Macro", "\\vartheta"],
      uarr: ["Macro", "\\uparrow"],
      uArr: ["Macro", "\\Uparrow"],
      Uarr: ["Macro", "\\Uparrow"],
      unicodeInt: ["Macro", "\\mathop{\\vcenter{\\mathchoice{\\huge\\unicode{#1}\\,}{\\unicode{#1}}{\\unicode{#1}}{\\unicode{#1}}}\\,}\\nolimits", 1],
      varcoppa: ["Macro", "\u03D9"],
      varstigma: ["Macro", "\u03DB"],
      varointclockwise: ["Macro", "\\unicodeInt{x2232}"],
      vline: ['Macro','\\smash{\\large\\lvert}',0],
      weierp: ["Macro", "\\wp"],
      Z: ["Macro", "\\mathbb{Z}"],
      Zeta: ["Macro", "\\mathrm{Z}"]
    }
  });
});

MathJax.Ajax.loadComplete("[MathJax]/extensions/TeX/mediawiki-texvc.js");

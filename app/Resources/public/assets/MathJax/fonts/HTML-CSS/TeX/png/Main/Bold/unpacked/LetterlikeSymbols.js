/*************************************************************
 *
 *  MathJax/fonts/HTML-CSS/TeX/png/Main/Bold/LetterlikeSymbols.js
 *  
 *  Defines the image size data needed for the HTML-CSS OutputJax
 *  to display mathematics using fallback images when the fonts
 *  are not availble to the client browser.
 *
 *  ---------------------------------------------------------------------
 *
 *  Copyright (c) 2009-2013 The MathJax Consortium
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the
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

MathJax.OutputJax["HTML-CSS"].defineImageData({
  "MathJax_Main-bold": {
    0x210F: [  // stix-/hbar - Planck's over 2pi
      [5,5,0],[6,6,0],[7,7,0],[8,9,0],[9,10,0],[11,12,0],[13,15,1],[15,17,1],
      [18,20,1],[22,24,1],[25,28,1],[30,34,1],[36,39,1],[42,47,1]
    ],
    0x2111: [  // BLACK-LETTER CAPITAL I
      [6,5,0],[7,6,0],[8,7,0],[10,9,0],[12,10,0],[14,12,0],[16,14,0],[19,17,0],
      [23,21,1],[27,25,1],[32,29,1],[38,34,1],[45,40,1],[53,48,1]
    ],
    0x2113: [  // SCRIPT SMALL L
      [3,5,0],[4,6,0],[5,7,0],[6,9,0],[7,11,1],[8,13,1],[9,15,1],[11,18,1],
      [13,21,1],[15,25,1],[18,29,1],[21,34,1],[25,41,2],[30,49,2]
    ],
    0x2118: [  // SCRIPT CAPITAL P
      [5,6,2],[6,6,2],[8,7,2],[9,9,3],[11,10,3],[13,12,4],[14,14,5],[17,16,5],
      [21,19,6],[24,23,7],[29,27,9],[34,32,10],[41,38,12],[48,45,14]
    ],
    0x211C: [  // BLACK-LETTER CAPITAL R
      [6,5,0],[7,6,0],[8,7,0],[10,10,1],[12,11,1],[14,13,1],[17,15,1],[20,18,1],
      [23,21,1],[28,25,1],[32,29,1],[39,34,1],[46,42,2],[55,49,2]
    ],
    0x2135: [  // ALEF SYMBOL
      [5,5,0],[6,6,0],[7,7,0],[8,9,0],[9,10,0],[11,12,0],[13,14,0],[15,17,0],
      [18,20,0],[21,23,0],[25,28,0],[30,33,0],[36,39,0],[42,46,0]
    ]
  }
});

MathJax.Ajax.loadComplete(MathJax.OutputJax["HTML-CSS"].imgDir+"/Main/Bold"+
                          MathJax.OutputJax["HTML-CSS"].imgPacked+"/LetterlikeSymbols.js");

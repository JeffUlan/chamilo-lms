/*************************************************************
 *
 *  MathJax/fonts/HTML-CSS/TeX/png/Fraktur/Regular/PUA.js
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
  "MathJax_Fraktur": {
    0xE300: [  // ??
      [3,6,1],[4,7,1],[5,8,1],[6,9,1],[6,11,1],[8,13,1],[9,15,1],[10,17,1],
      [12,20,1],[15,25,2],[17,29,2],[21,34,2],[24,40,2],[29,48,3]
    ],
    0xE301: [  // ??
      [3,6,1],[4,7,1],[5,8,1],[6,9,1],[6,10,1],[8,12,1],[9,13,1],[11,16,1],
      [12,19,1],[15,22,1],[17,27,2],[21,31,2],[24,37,2],[29,43,2]
    ],
    0xE302: [  // ??
      [3,7,2],[3,8,2],[4,10,3],[4,11,3],[5,13,3],[6,16,4],[7,19,5],[9,21,5],
      [10,25,6],[12,30,7],[14,36,9],[16,42,10],[19,51,13],[23,60,15]
    ],
    0xE303: [  // ??
      [3,7,2],[3,8,2],[4,10,3],[4,11,3],[5,13,3],[6,16,4],[7,19,5],[8,22,6],
      [9,25,6],[11,31,8],[13,36,9],[15,43,11],[18,51,13],[21,60,15]
    ],
    0xE304: [  // ??
      [3,6,2],[4,6,2],[5,7,2],[6,9,3],[7,10,3],[8,12,4],[9,15,5],[11,16,5],
      [13,20,6],[15,24,8],[18,28,9],[21,32,10],[25,39,12],[30,46,15]
    ],
    0xE305: [  // ??
      [2,6,1],[3,7,1],[4,8,1],[4,10,1],[5,11,1],[6,13,1],[7,15,1],[8,17,1],
      [9,21,1],[11,24,1],[13,28,1],[15,33,1],[18,40,1],[21,48,2]
    ],
    0xE306: [  // ??
      [3,5,1],[3,6,1],[4,7,1],[4,8,1],[5,10,1],[6,11,1],[7,13,1],[9,15,1],
      [10,18,1],[12,21,1],[14,24,1],[17,28,1],[20,34,1],[23,41,2]
    ],
    0xE307: [  // ??
      [5,5,1],[5,5,1],[5,6,1],[7,7,1],[8,8,1],[9,9,1],[11,11,1],[12,13,1],
      [15,15,1],[17,17,1],[21,20,1],[24,24,1],[29,29,2],[34,34,2]
    ]
  }
});

MathJax.Ajax.loadComplete(MathJax.OutputJax["HTML-CSS"].imgDir+"/Fraktur/Regular"+
                          MathJax.OutputJax["HTML-CSS"].imgPacked+"/PUA.js");

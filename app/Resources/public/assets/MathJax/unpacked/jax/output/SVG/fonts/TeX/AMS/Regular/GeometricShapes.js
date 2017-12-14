/*************************************************************
 *
 *  MathJax/jax/output/SVG/fonts/TeX/svg/AMS/Regular/GeometricShapes.js
 *
 *  Copyright (c) 2011-2017 The MathJax Consortium
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

MathJax.Hub.Insert(
  MathJax.OutputJax.SVG.FONTDATA.FONTS['MathJax_AMS'],
  {
    // BLACK SQUARE
    0x25A0: [689,0,778,55,722,'71 0Q59 4 55 16V346L56 676Q64 686 70 689H709Q719 681 722 674V15Q719 10 709 1L390 0H71'],

    // WHITE SQUARE
    0x25A1: [689,0,778,55,722,'71 0Q59 4 55 16V346L56 676Q64 686 70 689H709Q719 681 722 674V15Q719 10 709 1L390 0H71ZM682 40V649H95V40H682'],

    // BLACK UP-POINTING TRIANGLE
    0x25B2: [575,20,722,84,637,'99 -20Q84 -11 84 0Q84 5 148 145T278 424L342 563Q347 575 360 575Q368 575 375 570Q376 569 441 430T571 148T637 0Q637 -11 622 -20H99'],

    // WHITE UP-POINTING TRIANGLE
    0x25B3: [575,20,722,84,637,'99 -20Q84 -11 84 0Q84 5 148 145T278 424L342 563Q347 575 360 575Q368 575 375 570Q376 569 441 430T571 148T637 0Q637 -11 622 -20H99ZM476 260L360 509L248 266Q137 24 135 22Q135 20 360 20Q586 20 586 21L476 260'],

    // BLACK RIGHT-POINTING TRIANGLE
    0x25B6: [540,41,778,83,694,'83 523Q83 524 85 527T92 535T103 539Q107 539 389 406T680 268Q694 260 694 249Q694 239 687 234Q685 232 395 95L107 -41H101Q90 -40 83 -26V523'],

    // BLACK DOWN-POINTING TRIANGLE
    0x25BC: [576,19,722,84,637,'84 556Q84 567 99 576H622Q637 567 637 556Q637 551 572 409T441 127T375 -14Q368 -19 360 -19H358Q349 -19 342 -7T296 92Q249 193 211 275Q84 550 84 556'],

    // WHITE DOWN-POINTING TRIANGLE
    0x25BD: [576,19,722,84,637,'84 556Q84 567 99 576H622Q637 567 637 556Q637 551 572 409T441 127T375 -14Q368 -19 360 -19H358Q349 -19 342 -7T296 92Q249 193 211 275Q84 550 84 556ZM586 534Q586 536 361 536Q135 536 135 535L358 52L361 47L473 290Q584 532 586 534'],

    // BLACK LEFT-POINTING TRIANGLE
    0x25C0: [539,41,778,83,694,'694 -26Q686 -40 676 -41H670L382 95Q92 232 90 234Q83 239 83 249Q83 262 96 267Q101 270 379 401T665 537Q671 539 674 539Q686 539 694 524V-26'],

    // LOZENGE
    0x25CA: [716,132,667,56,611,'318 709Q325 716 332 716Q340 716 344 713T474 511Q611 298 611 292Q611 285 526 152Q494 103 474 72Q347 -128 344 -130Q340 -132 333 -132T322 -130Q319 -128 257 -31T131 169T60 278Q56 285 56 292Q56 298 60 305Q73 326 194 516T318 709ZM567 290T567 291T451 475T333 658L100 293Q100 288 215 108L333 -74Q334 -74 450 108']
  }
);

MathJax.Ajax.loadComplete(MathJax.OutputJax.SVG.fontDir+"/AMS/Regular/GeometricShapes.js");

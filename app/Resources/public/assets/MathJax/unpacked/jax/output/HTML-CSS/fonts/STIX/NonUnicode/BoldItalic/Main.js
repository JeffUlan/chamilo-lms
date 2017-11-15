/*************************************************************
 *
 *  MathJax/jax/output/HTML-CSS/fonts/STIX/NonUnicode/BoldItalic/Main.js
 *
 *  Copyright (c) 2009-2017 The MathJax Consortium
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

MathJax.OutputJax['HTML-CSS'].FONTDATA.FONTS['STIXNonUnicode-bold-italic'] = {
  directory: 'NonUnicode/BoldItalic',
  family: 'STIXNonUnicode',
  weight: 'bold',
  style: 'italic',
  Ranges: [
    [0x20,0x20,"All"],
    [0xA0,0xA0,"All"],
    [0xE000,0xF8FF,"PrivateUse"]
  ]

};

MathJax.OutputJax["HTML-CSS"].initFont("STIXNonUnicode-bold-italic");

MathJax.Ajax.loadComplete(MathJax.OutputJax["HTML-CSS"].fontDir + "/NonUnicode/BoldItalic/Main.js");

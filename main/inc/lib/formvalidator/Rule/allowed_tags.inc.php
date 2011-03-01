<?php
/* For licensing terms, see /license.txt */

/**
 * This page defines all HTML-tages and their attributes that are allowed in
 * Chamilo. 2 arrays are defined, one contains the allowed HTML for students and
 * the other the allowed HTML for teachers.
 *
 * Modifying this page:
 * - for each allowed tag there should be a line like
 *    $allowed_tags_XXXX   ['tagname'] = array();
 * - for each of the attributes allowed in the tag, there should be a line like
 *    $allowed_tags_XXXX['tagname']['attributename'] = array();
 * - please keep the content of this file alphabetically structured
 *
 * @link http://www.w3schools.com/tags/
 */

// KSES-COMPATIBLE SETTINGS

// ALLOWED HTML FOR STUDENTS

// a
$allowed_tags_student['a'] = array();
$allowed_tags_student['a']['class'] = array();
$allowed_tags_student['a']['dir'] = array();
$allowed_tags_student['a']['id'] = array();
$allowed_tags_student['a']['href'] = array();
$allowed_tags_student['a']['lang'] = array();
$allowed_tags_student['a']['name'] = array();
$allowed_tags_student['a']['rel'] = array();
$allowed_tags_student['a']['rev'] = array();
$allowed_tags_student['a']['style'] = array();
$allowed_tags_student['a']['target'] = array();
$allowed_tags_student['a']['title'] = array();
$allowed_tags_student['a']['xml:lang'] = array();

// abbr
$allowed_tags_student['abbr'] = array();
$allowed_tags_student['abbr']['class'] = array();
$allowed_tags_student['abbr']['dir'] = array();
$allowed_tags_student['abbr']['id'] = array();
$allowed_tags_student['abbr']['lang'] = array();
$allowed_tags_student['abbr']['style'] = array();
$allowed_tags_student['abbr']['title'] = array();
$allowed_tags_student['abbr']['xml:lang'] = array();

// acronym
$allowed_tags_student['acronym'] = array();
$allowed_tags_student['acronym']['class'] = array();
$allowed_tags_student['acronym']['dir'] = array();
$allowed_tags_student['acronym']['id'] = array();
$allowed_tags_student['acronym']['lang'] = array();
$allowed_tags_student['acronym']['style'] = array();
$allowed_tags_student['acronym']['title'] = array();
$allowed_tags_student['acronym']['xml:lang'] = array();

// address
$allowed_tags_student['address'] = array();
$allowed_tags_student['address']['class'] = array();
$allowed_tags_student['address']['dir'] = array();
$allowed_tags_student['address']['id'] = array();
$allowed_tags_student['address']['lang'] = array();
$allowed_tags_student['address']['style'] = array();
$allowed_tags_student['address']['title'] = array();
$allowed_tags_student['address']['xml:lang'] = array();

// b
$allowed_tags_student['b'] = array();
$allowed_tags_student['b']['class'] = array();
$allowed_tags_student['b']['dir'] = array();
$allowed_tags_student['b']['id'] = array();
$allowed_tags_student['b']['lang'] = array();
$allowed_tags_student['b']['style'] = array();
$allowed_tags_student['b']['title'] = array();
$allowed_tags_student['b']['xml:lang'] = array();

// base
$allowed_tags_student_full_page['base'] = array();
$allowed_tags_student_full_page['base']['href'] = array();
$allowed_tags_student_full_page['base']['target'] = array();

// basefont (IE only)
$allowed_tags_student['basefont'] = array();
$allowed_tags_student['basefont']['face'] = array();
$allowed_tags_student['basefont']['color'] = array();
$allowed_tags_student['basefont']['id'] = array();
$allowed_tags_student['basefont']['size'] = array();

// bdo
$allowed_tags_student['bdo'] = array();
$allowed_tags_student['bdo']['class'] = array();
$allowed_tags_student['bdo']['dir'] = array();
$allowed_tags_student['bdo']['id'] = array();
$allowed_tags_student['bdo']['lang'] = array();
$allowed_tags_student['bdo']['style'] = array();
$allowed_tags_student['bdo']['title'] = array();
$allowed_tags_student['bdo']['xml:lang'] = array();

// big
$allowed_tags_student['big'] = array();
$allowed_tags_student['big']['class'] = array();
$allowed_tags_student['big']['dir'] = array();
$allowed_tags_student['big']['id'] = array();
$allowed_tags_student['big']['lang'] = array();
$allowed_tags_student['big']['style'] = array();
$allowed_tags_student['big']['title'] = array();
$allowed_tags_student['big']['xml:lang'] = array();

// blockquote
$allowed_tags_student['blockquote'] = array();
$allowed_tags_student['blockquote']['cite'] = array();
$allowed_tags_student['blockquote']['class'] = array();
$allowed_tags_student['blockquote']['dir'] = array();
$allowed_tags_student['blockquote']['id'] = array();
$allowed_tags_student['blockquote']['lang'] = array();
$allowed_tags_student['blockquote']['style'] = array();
$allowed_tags_student['blockquote']['title'] = array();
$allowed_tags_student['blockquote']['xml:lang'] = array();

// body
$allowed_tags_student_full_page['body'] = array();
$allowed_tags_student_full_page['body']['alink'] = array();
$allowed_tags_student_full_page['body']['background'] = array();
$allowed_tags_student_full_page['body']['bgcolor'] = array();
$allowed_tags_student_full_page['body']['link'] = array();
$allowed_tags_student_full_page['body']['text'] = array();
$allowed_tags_student_full_page['body']['vlink'] = array();
$allowed_tags_student_full_page['body']['class'] = array();
$allowed_tags_student_full_page['body']['dir'] = array();
$allowed_tags_student_full_page['body']['id'] = array();
$allowed_tags_student_full_page['body']['lang'] = array();
$allowed_tags_student_full_page['body']['style'] = array();
$allowed_tags_student_full_page['body']['title'] = array();
$allowed_tags_student_full_page['body']['xml:lang'] = array();

// br
$allowed_tags_student['br'] = array();
$allowed_tags_student['br']['class'] = array();
$allowed_tags_student['br']['id'] = array();
$allowed_tags_student['br']['style'] = array();
$allowed_tags_student['br']['title'] = array();

// caption
$allowed_tags_student['caption'] = array();
$allowed_tags_student['caption']['align'] = array();
$allowed_tags_student['caption']['class'] = array();
$allowed_tags_student['caption']['dir'] = array();
$allowed_tags_student['caption']['id'] = array();
$allowed_tags_student['caption']['lang'] = array();
$allowed_tags_student['caption']['style'] = array();
$allowed_tags_student['caption']['title'] = array();
$allowed_tags_student['caption']['xml:lang'] = array();

// center
$allowed_tags_student['center'] = array();
$allowed_tags_student['center']['class'] = array();
$allowed_tags_student['center']['dir'] = array();
$allowed_tags_student['center']['id'] = array();
$allowed_tags_student['center']['lang'] = array();
$allowed_tags_student['center']['style'] = array();
$allowed_tags_student['center']['title'] = array();

// cite
$allowed_tags_student['cite'] = array();
$allowed_tags_student['cite']['class'] = array();
$allowed_tags_student['cite']['dir'] = array();
$allowed_tags_student['cite']['id'] = array();
$allowed_tags_student['cite']['lang'] = array();
$allowed_tags_student['cite']['style'] = array();
$allowed_tags_student['cite']['title'] = array();
$allowed_tags_student['cite']['xml:lang'] = array();

// code
$allowed_tags_student['code'] = array();
$allowed_tags_student['code']['class'] = array();
$allowed_tags_student['code']['dir'] = array();
$allowed_tags_student['code']['id'] = array();
$allowed_tags_student['code']['lang'] = array();
$allowed_tags_student['code']['style'] = array();
$allowed_tags_student['code']['title'] = array();
$allowed_tags_student['code']['xml:lang'] = array();

// col
$allowed_tags_student['col'] = array();
$allowed_tags_student['col']['align'] = array();
$allowed_tags_student['col']['class'] = array();
$allowed_tags_student['col']['dir'] = array();
$allowed_tags_student['col']['id'] = array();
$allowed_tags_student['col']['lang'] = array();
$allowed_tags_student['col']['span'] = array();
$allowed_tags_student['col']['style'] = array();
$allowed_tags_student['col']['title'] = array();
$allowed_tags_student['col']['valign'] = array();
$allowed_tags_student['col']['width'] = array();
$allowed_tags_student['col']['xml:lang'] = array();

// colgroup
$allowed_tags_student['colgroup'] = array();
$allowed_tags_student['colgroup']['align'] = array();
$allowed_tags_student['colgroup']['class'] = array();
$allowed_tags_student['colgroup']['dir'] = array();
$allowed_tags_student['colgroup']['id'] = array();
$allowed_tags_student['colgroup']['lang'] = array();
$allowed_tags_student['colgroup']['span'] = array();
$allowed_tags_student['colgroup']['style'] = array();
$allowed_tags_student['colgroup']['title'] = array();
$allowed_tags_student['colgroup']['valign'] = array();
$allowed_tags_student['colgroup']['width'] = array();
$allowed_tags_student['colgroup']['xml:lang'] = array();

// dd
$allowed_tags_student['dd'] = array();
$allowed_tags_student['dd']['class'] = array();
$allowed_tags_student['dd']['dir'] = array();
$allowed_tags_student['dd']['id'] = array();
$allowed_tags_student['dd']['lang'] = array();
$allowed_tags_student['dd']['style'] = array();
$allowed_tags_student['dd']['title'] = array();
$allowed_tags_student['dd']['xml:lang'] = array();

// del
$allowed_tags_student['del'] = array();
$allowed_tags_student['del']['cite'] = array();
$allowed_tags_student['del']['class'] = array();
$allowed_tags_student['del']['dir'] = array();
$allowed_tags_student['del']['id'] = array();
$allowed_tags_student['del']['lang'] = array();
$allowed_tags_student['del']['style'] = array();
$allowed_tags_student['del']['title'] = array();
$allowed_tags_student['del']['xml:lang'] = array();

// div
$allowed_tags_student['div'] = array();
$allowed_tags_student['div']['align'] = array();
$allowed_tags_student['div']['class'] = array();
$allowed_tags_student['div']['id'] = array();
$allowed_tags_student['div']['style'] = array();

// dl
$allowed_tags_student['dl'] = array();

// dt
$allowed_tags_student['dt'] = array();

// em
$allowed_tags_student['em'] = array();

// embed
$allowed_tags_student['embed'] = array();
$allowed_tags_student['embed']['height'] = array();
$allowed_tags_student['embed']['width'] = array();
$allowed_tags_student['embed']['type'] = array();
//$allowed_tags_student['embed']['quality'] = array();
$allowed_tags_student['embed']['src'] = array();
$allowed_tags_student['embed']['flashvars'] = array();
$allowed_tags_student['embed']['allowscriptaccess'] = array();
//$allowed_tags_student['embed']['allowfullscreen'] = array();
//$allowed_tags_student['embed']['bgcolor'] = array();
//$allowed_tags_student['embed']['pluginspage'] = array();

// fieldset
/*
$allowed_tags_student['fieldset'] = array(); */

// font
$allowed_tags_student['font'] = array();
$allowed_tags_student['font']['color'] = array();
$allowed_tags_student['font']['face'] = array();
$allowed_tags_student['font']['size'] = array();
$allowed_tags_student['font']['style'] = array();

// form
/*
$allowed_tags_student['form'] = array();
$allowed_tags_student['form']['action'] = array();
$allowed_tags_student['form']['accept'] = array();
$allowed_tags_student['form']['accept-charset'] = array();
$allowed_tags_student['form']['enctype'] = array();
$allowed_tags_student['form']['method'] = array();
$allowed_tags_student['form']['name'] = array();
$allowed_tags_student['form']['target'] = array();*/

// frame
$allowed_tags_student_full_page['frame'] = array();
$allowed_tags_student_full_page['frame']['frameborder'] = array();
$allowed_tags_student_full_page['frame']['longsesc'] = array();
$allowed_tags_student_full_page['frame']['marginheight'] = array();
$allowed_tags_student_full_page['frame']['marginwidth'] = array();
$allowed_tags_student_full_page['frame']['name'] = array();
$allowed_tags_student_full_page['frame']['noresize'] = array();
$allowed_tags_student_full_page['frame']['scrolling'] = array();
$allowed_tags_student_full_page['frame']['src'] = array();

// frameset
$allowed_tags_student_full_page['frameset'] = array();
$allowed_tags_student_full_page['frameset']['cols'] = array();
$allowed_tags_student_full_page['frameset']['rows'] = array();

// head
$allowed_tags_student_full_page['head'] = array();
$allowed_tags_student_full_page['head']['profile'] = array();

// h1
$allowed_tags_student['h1'] = array();
$allowed_tags_student['h1']['align'] = array();
$allowed_tags_student['h1']['class'] = array();
$allowed_tags_student['h1']['id'] = array();

// h2
$allowed_tags_student['h2'] = array();
$allowed_tags_student['h2']['align'] = array();
$allowed_tags_student['h2']['class'] = array();
$allowed_tags_student['h2']['id'] = array();

// h3
$allowed_tags_student['h3'] = array();
$allowed_tags_student['h3']['align'] = array();
$allowed_tags_student['h3']['class'] = array();
$allowed_tags_student['h3']['id'] = array();

// h4
$allowed_tags_student['h4'] = array();
$allowed_tags_student['h4']['align'] = array();
$allowed_tags_student['h4']['class'] = array();
$allowed_tags_student['h4']['id'] = array();

// h5
$allowed_tags_student['h5'] = array();
$allowed_tags_student['h5']['align'] = array();
$allowed_tags_student['h5']['class'] = array();
$allowed_tags_student['h5']['id'] = array();

// h6
$allowed_tags_student['h6'] = array();
$allowed_tags_student['h6']['align'] = array();
$allowed_tags_student['h6']['class'] = array();
$allowed_tags_student['h6']['id'] = array();

// hr
$allowed_tags_student['hr'] = array();
$allowed_tags_student['hr']['align'] = array();
$allowed_tags_student['hr']['noshade'] = array();
$allowed_tags_student['hr']['size'] = array();
$allowed_tags_student['hr']['width'] = array();
$allowed_tags_student['hr']['class'] = array();
$allowed_tags_student['hr']['id'] = array();

// html
$allowed_tags_student_full_page['html'] = array();
$allowed_tags_student_full_page['html']['xmlns'] = array();

// i
$allowed_tags_student['i'] = array();

// iframe
/*
$allowed_tags_student['iframe'] = array();
$allowed_tags_student['iframe']['align'] = array();
$allowed_tags_student['iframe']['frameborder'] = array();
$allowed_tags_student['iframe']['height'] = array();
$allowed_tags_student['iframe']['londesc'] = array();
$allowed_tags_student['iframe']['marginheight'] = array();
$allowed_tags_student['iframe']['marginwidth'] = array();
$allowed_tags_student['iframe']['name'] = array();
$allowed_tags_student['iframe']['scrolling'] = array();
$allowed_tags_student['iframe']['src'] = array();
$allowed_tags_student['iframe']['width'] = array();*/

// img
$allowed_tags_student['img'] = array();
$allowed_tags_student['img']['alt'] = array();
$allowed_tags_student['img']['align'] = array();
$allowed_tags_student['img']['border'] = array();
$allowed_tags_student['img']['height'] = array();
$allowed_tags_student['img']['hspace'] = array();
//$allowed_tags_student['img']['ismap'] = array();
$allowed_tags_student['img']['longdesc'] = array();
$allowed_tags_student['img']['src'] = array();
//$allowed_tags_student['img']['usemap'] = array();
$allowed_tags_student['img']['vspace'] = array();
$allowed_tags_student['img']['width'] = array();

// ins
$allowed_tags_student['ins'] = array();
$allowed_tags_student['ins']['cite'] = array();
$allowed_tags_student['ins']['class'] = array();
$allowed_tags_student['ins']['dir'] = array();
$allowed_tags_student['ins']['id'] = array();
$allowed_tags_student['ins']['lang'] = array();
$allowed_tags_student['ins']['style'] = array();
$allowed_tags_student['ins']['title'] = array();
$allowed_tags_student['ins']['xml:lang'] = array();

// kbd
$allowed_tags_student['kbd'] = array();

// label
/*
$allowed_tags_student['label'] = array();
$allowed_tags_student['label']['for'] = array();
*/

// legend
/*
$allowed_tags_student['legend'] = array();
$allowed_tags_student['legend']['align'] = array();*/

// li
$allowed_tags_student['li'] = array();

// link
$allowed_tags_student_full_page['link'] = array();
$allowed_tags_student_full_page['link']['charset'] = array();
$allowed_tags_student_full_page['link']['href'] = array();
$allowed_tags_student_full_page['link']['hreflang'] = array();
$allowed_tags_student_full_page['link']['media'] = array();
$allowed_tags_student_full_page['link']['rel'] = array();
$allowed_tags_student_full_page['link']['rev'] = array();
$allowed_tags_student_full_page['link']['target'] = array();
$allowed_tags_student_full_page['link']['type'] = array();

// map
/*
$allowed_tags_student['map'] = array();
$allowed_tags_student['map']['id'] = array();
$allowed_tags_student['map']['name'] = array();*/

// menu
$allowed_tags_student['menu'] = array();

// meta
$allowed_tags_student_full_page['meta'] = array();
$allowed_tags_student_full_page['meta']['content'] = array();
$allowed_tags_student_full_page['meta']['http-equiv'] = array();
$allowed_tags_student_full_page['meta']['name'] = array();
$allowed_tags_student_full_page['meta']['scheme'] = array();

// noframes
$allowed_tags_student_full_page['noframes'] = array();

// object
$allowed_tags_student['object'] = array();
//$allowed_tags_student['object']['align'] = array();
//$allowed_tags_student['object']['archive'] = array();
//$allowed_tags_student['object']['border'] = array();
$allowed_tags_student['object']['classid'] = array();
$allowed_tags_student['object']['codebase'] = array();
//$allowed_tags_student['object']['codetype'] = array();
$allowed_tags_student['object']['data'] = array();
//$allowed_tags_student['object']['declare'] = array();
$allowed_tags_student['object']['height'] = array();
//$allowed_tags_student['object']['hspace'] = array();
//$allowed_tags_student['object']['name'] = array();
//$allowed_tags_student['object']['standby'] = array();
$allowed_tags_student['object']['type'] = array();
//$allowed_tags_student['object']['usemap'] = array();
//$allowed_tags_student['object']['vspace'] = array();
$allowed_tags_student['object']['width'] = array();

// ol
$allowed_tags_student['ol'] = array();
$allowed_tags_student['ol']['compact'] = array();
$allowed_tags_student['ol']['start'] = array();
$allowed_tags_student['ol']['type'] = array();

// optgroup
/*
$allowed_tags_student['optgroup'] = array();
$allowed_tags_student['optgroup']['label'] = array();
$allowed_tags_student['optgroup']['disabled'] = array();*/

// option
/*
$allowed_tags_student['option'] = array();
$allowed_tags_student['option']['disabled'] = array();
$allowed_tags_student['option']['label'] = array();
$allowed_tags_student['option']['selected'] = array();
$allowed_tags_student['option']['value'] = array();*/

// p
$allowed_tags_student['p'] = array();
$allowed_tags_student['p']['align'] = array();

// param
/*
$allowed_tags_student['param'] = array();
$allowed_tags_student['param']['name'] = array();
//$allowed_tags_student['param']['type'] = array();
$allowed_tags_student['param']['value'] = array();
//$allowed_tags_student['param']['valuetype'] = array();
*/

// pre
$allowed_tags_student['pre'] = array();
$allowed_tags_student['pre']['width'] = array();

// q
$allowed_tags_student['q'] = array();
$allowed_tags_student['q']['cite'] = array();

// s
$allowed_tags_student['s'] = array();

// span
$allowed_tags_student['span'] = array();
$allowed_tags_student['span']['style'] = array();

// strike
$allowed_tags_student['strike'] = array();

// strong
$allowed_tags_student['strong'] = array();

// style
$allowed_tags_student_full_page['style'] = array();
$allowed_tags_student_full_page['style']['type'] = array();
$allowed_tags_student_full_page['style']['media'] = array();

// sub
$allowed_tags_student['sub'] = array();

// sup
$allowed_tags_student['sup'] = array();

// table
$allowed_tags_student['table'] = array();
$allowed_tags_student['table']['align'] = array();
$allowed_tags_student['table']['bgcolor'] = array();
$allowed_tags_student['table']['border'] = array();
$allowed_tags_student['table']['cellpadding'] = array();
$allowed_tags_student['table']['cellspacing'] = array();
$allowed_tags_student['table']['frame'] = array();
$allowed_tags_student['table']['rules'] = array();
$allowed_tags_student['table']['summary'] = array();
$allowed_tags_student['table']['width'] = array();

// tbody
$allowed_tags_student['tbody'] = array();
$allowed_tags_student['tbody']['align'] = array();
//$allowed_tags_student['tbody']['char'] = array();
$allowed_tags_student['tbody']['charoff'] = array();
$allowed_tags_student['tbody']['valign'] = array();

// td
$allowed_tags_student['td'] = array();
$allowed_tags_student['td']['abbr'] = array();
$allowed_tags_student['td']['align'] = array();
//$allowed_tags_student['td']['axis'] = array();
$allowed_tags_student['td']['bgcolor'] = array();
//$allowed_tags_student['td']['char'] = array();
$allowed_tags_student['td']['charoff'] = array();
$allowed_tags_student['td']['colspan'] = array();
//$allowed_tags_student['td']['headers'] = array();
$allowed_tags_student['td']['height'] = array();
$allowed_tags_student['td']['nowrap'] = array();
$allowed_tags_student['td']['rowspan'] = array();
//$allowed_tags_student['td']['scope'] = array();
$allowed_tags_student['td']['valign'] = array();
$allowed_tags_student['td']['width'] = array();

// textarea
/*
$allowed_tags_student['textarea'] = array();
$allowed_tags_student['textarea']['cols'] = array();
$allowed_tags_student['textarea']['rows'] = array();
$allowed_tags_student['textarea']['disabled'] = array();
$allowed_tags_student['textarea']['name'] = array();
$allowed_tags_student['textarea']['readonly'] = array();*/

// tfoot
$allowed_tags_student['tfoot'] = array();
$allowed_tags_student['tfoot']['align'] = array();
//$allowed_tags_student['tfoot']['char'] = array();
$allowed_tags_student['tfoot']['charoff'] = array();
$allowed_tags_student['tfoot']['valign'] = array();

// th
$allowed_tags_student['th'] = array();
$allowed_tags_student['th']['abbr'] = array();
$allowed_tags_student['th']['align'] = array();
//$allowed_tags_student['th']['axis'] = array();
$allowed_tags_student['th']['bgcolor'] = array();
//$allowed_tags_student['th']['char'] = array();
$allowed_tags_student['th']['charoff'] = array();
$allowed_tags_student['th']['colspan'] = array();
//$allowed_tags_student['th']['headers'] = array();
$allowed_tags_student['th']['height'] = array();
$allowed_tags_student['th']['nowrap'] = array();
$allowed_tags_student['th']['rowspan'] = array();
//$allowed_tags_student['th']['scope'] = array();
$allowed_tags_student['th']['valign'] = array();
$allowed_tags_student['th']['width'] = array();

// thead
$allowed_tags_student['thead'] = array();
$allowed_tags_student['thead']['align'] = array();
//$allowed_tags_student['thead']['char'] = array();
$allowed_tags_student['thead']['charoff'] = array();
$allowed_tags_student['thead']['valign'] = array();

// title
$allowed_tags_student_full_page['title'] = array();

// tr
$allowed_tags_student['tr'] = array();
$allowed_tags_student['tr']['align'] = array();
$allowed_tags_student['tr']['bgcolor'] = array();
//$allowed_tags_student['tr']['char'] = array();
$allowed_tags_student['tr']['charoff'] = array();
$allowed_tags_student['tr']['valign'] = array();

// tt
$allowed_tags_student['tt'] = array();

// u
$allowed_tags_student['u'] = array();

// ul
$allowed_tags_student['ul'] = array();

// var
$allowed_tags_student['var'] = array();


// ALLOWED HTML FOR TEACHERS

// Allow all HTML allowed for students
$allowed_tags_teacher = $allowed_tags_student;

// noscript
$allowed_tags_teacher['noscript'] = array();

// script
$allowed_tags_teacher['script'] = array();
$allowed_tags_teacher['script']['type'] = array();

$allowed_tags_teacher['html'] = array();
$allowed_tags_teacher['html']['xmlns'] = array();

$allowed_tags_teacher['head'] = array();
$allowed_tags_teacher['head']['profile'] = array();

// body
$allowed_tags_teacher['body'] = array();
$allowed_tags_teacher['body']['alink'] = array();
$allowed_tags_teacher['body']['background'] = array();
$allowed_tags_teacher['body']['bgcolor'] = array();
$allowed_tags_teacher['body']['link'] = array();
$allowed_tags_teacher['body']['text'] = array();
$allowed_tags_teacher['body']['vlink'] = array();


// ALLOWED HTML FOR TEACHERS FOR HTMLPURIFIER
// TODO: This section is to be checked for removal.
/*
// noscript
$allowed_tags_teachers['noscript'] = array();

// script
$allowed_tags_teachers['script'] = array();
$allowed_tags_teachers['script']['type'] = array();

$allowed_tags_teachers['html'] = array();
$allowed_tags_teachers['html']['xmlns'] = array();

$allowed_tags_teachers['head'] = array();
$allowed_tags_teachers['head']['profile'] = array();

// body
$allowed_tags_teachers['body'] = array();
$allowed_tags_teachers['body']['alink'] = array();
$allowed_tags_teachers['body']['background'] = array();
$allowed_tags_teachers['body']['bgcolor'] = array();
$allowed_tags_teachers['body']['link'] = array();
$allowed_tags_teachers['body']['text'] = array();
$allowed_tags_teachers['body']['vlink'] = array();

$allowed_tags_teachers['span'] = array();
$allowed_tags_teachers['span']['style'] = array();
*/

$allowed_tags_teacher_full_page = $allowed_tags_student_full_page;


// ALLOWED HTML FOR ANONYMOUS USERS

$allowed_tags_anonymous = $allowed_tags_student;
$allowed_tags_anonymous_full_page = $allowed_tags_student_full_page;
// Add restrictions here.
unset($allowed_tags_anonymous['embed']);
unset($allowed_tags_anonymous['object']);


// HTMLPURIFIER-COMPATIBLE SETTINGS

function convert_kses_to_htmlpurifier($allowed_tags) {
    $allowed_html = array();
    foreach ($allowed_tags as $key1 => & $value1) {
        $result[0][] = $key1;
        if (count($value1) > 0) {
            $attr = array();
            foreach ($value1 as $key2 => & $value2) {
                $attr[] = $key2;
            }
            $allowed_html[] = $key1.'['.implode('|', $attr).']';
        } else {
            $allowed_html[] = $key1;
        }
    }
    return implode(',', $allowed_html);
}

global $allowed_html_student, $allowed_html_teacher, $allowed_html_anonymous;

// TODO: Support for full-page tags is needed for HTMLPurifier.
//$allowed_html_student = convert_kses_to_htmlpurifier(array_merge($allowed_tags_student, $allowed_tags_student_full_page));
//$allowed_html_teacher = convert_kses_to_htmlpurifier(array_merge($allowed_tags_teacher, $allowed_tags_teacher_full_page));
//$allowed_html_anonymous = convert_kses_to_htmlpurifier(array_merge($allowed_tags_anonymous, $allowed_tags_anonymous_full_page));
$allowed_html_student = convert_kses_to_htmlpurifier(array_merge($allowed_tags_student));
$allowed_html_teacher = convert_kses_to_htmlpurifier(array_merge($allowed_tags_teacher));
$allowed_html_anonymous = convert_kses_to_htmlpurifier(array_merge($allowed_tags_anonymous));

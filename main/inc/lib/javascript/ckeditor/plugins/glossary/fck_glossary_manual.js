/**
Makes possible to load glossary items from the Glossary Tool
This library will be loaded in:

document/showinframes.php
newscorm/lp_view.php
newscorm/scorm_api.php

*/
my_protocol = location.protocol;
my_pathname = location.pathname;
work_path = my_pathname.substr(0,my_pathname.indexOf('/courses/'));
var ajaxRequestUrl = my_protocol+"//"+location.host+work_path+"/main/glossary/glossary_ajax_request.php";

var imageSource = my_protocol+"//"+location.host+work_path+"/main/inc/lib/javascript/indicator.gif";
var indicatorImage ='<img src="' + imageSource + '" />';

$("body").on("click", ".glossary", function() {
    is_glossary_name = $(this).html();
    random_id = Math.round(Math.random()*100);
    div_show_id = "div_show_id";
    div_content_id = "div_content_id";

    $(this).append("<div id="+div_show_id+" ><div id="+div_content_id+">&nbsp;</div></div>");

    var $target = $(this);

    $("#"+div_show_id).dialog("destroy");
    $("#"+div_show_id).dialog({
        autoOpen: false,
        width: 600,
        height: 200,
        position:  { my: 'left top', at: 'right top', of: $target },
        close: function(){
            $("div#"+div_show_id).remove();
            $("div#"+div_content_id).remove();
        }
    });
    var indicator =
    $.ajax({
        contentType: "application/x-www-form-urlencoded",
        beforeSend: function(result) {
            $("div#"+div_content_id).html(indicatorImage);
        },
        type: "POST",
        url: ajaxRequestUrl,
        data: "glossary_name="+is_glossary_name,
        success: function(data) {
            $("div#"+div_content_id).html(data);
            $("#"+div_show_id).dialog("open");
        }
    });
});

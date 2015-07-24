<meta charset="{{ system_charset }}" />
<link href="https://chamilo.org/chamilo-lms/" rel="help" />
<link href="https://chamilo.org/the-association/" rel="author" />
<link href="https://chamilo.org/the-association/" rel="copyright" />
{{ prefetch }}
{{ favico }}
{{ browser_specific_head }}
<link rel="apple-touch-icon" href="{{ _p.web }}apple-touch-icon.png" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="Generator" content="{{ _s.software_name }} {{ _s.system_version|slice(0,1) }}" />
{#  Use the latest engine in ie8/ie9 or use google chrome engine if available  #}
{#  Improve usability in portal devices #}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ title_string }}</title>
{{ social_meta }}
{{ css_static_file_to_string }}
{{ js_file_to_string }}
<script>

// External plugins not part of the default Ckeditor package.
var plugins = [
    'asciimath',
    'asciisvg',
    'audio',
    //'ckeditor_wiris',
    'dialogui',
    'glossary',
    'leaflet',
    'mapping',
    'maximize',
    'mathjax',
    'oembed',
    'toolbar',
    'toolbarswitch',
    'video',
    'wikilink',
    'wordcount',
    'youtube'
];

plugins.forEach(function(plugin) {
    CKEDITOR.plugins.addExternal(plugin, '{{ _p.web_main ~ 'inc/lib/javascript/ckeditor/plugins/' }}' + plugin + '/');
});

/**
 * Function use to load templates in a div
**/
var showTemplates = function (ckeditorName) {
    var editorName = 'content';
    if (ckeditorName && ckeditorName.length > 0) {
        editorName = ckeditorName;
    }
    CKEDITOR.editorConfig(CKEDITOR.config);
    CKEDITOR.loadTemplates(CKEDITOR.config.templates_files, function (a){
        var templatesConfig = CKEDITOR.getTemplates("default");

        var $templatesUL = $("<ul>");

        $.each(templatesConfig.templates, function () {
            var template = this;
            var $templateLi = $("<li>");

            var templateHTML = "<img src=\"" + templatesConfig.imagesPath + template.image + "\" ><div>";
            templateHTML += "<b>" + template.title + "</b>";

            if (template.description) {
                templateHTML += "<div class=description>" + template.description + "</div>";
            }

            templateHTML += "</div>";

            $("<a>", {
                href: "#",
                html: templateHTML,
                click: function (e) {
                    e.preventDefault();
                    if (CKEDITOR.instances[editorName]) {
                        CKEDITOR.instances[editorName].setData(template.html, function () {
                            this.checkDirty();
                        });
                    }
                }
            }).appendTo($templateLi);

            $templatesUL.append($templateLi);
        });

        $templatesUL.appendTo("#frmModel");
    });
};

</script>
{{ extra_headers }}
<script>

function setCheckbox(value, table_id) {
    checkboxes = $("#"+table_id+" input:checkbox");
    $.each(checkboxes, function(index, checkbox) {
         checkbox.checked = value;
        if (value) {
            $(checkbox).parentsUntil("tr").parent().addClass("row_selected");
        } else {
            $(checkbox).parentsUntil("tr").parent().removeClass("row_selected");
        }
    });
    return false;
}

function action_click(element, table_id) {
    d = $("#"+table_id);
    if (!confirm('{{ "ConfirmYourChoice"|get_lang }}')) {
        return false;
    } else {
        var action =$(element).attr("data-action");
        $('#'+table_id+' input[name="action"] ').attr("value", action);
        d.submit();
        return false;
    }
}

/* Global chat variables */
var ajax_url        = '{{ _p.web_ajax }}chat.ajax.php';
var online_button   = '{{ online_button }}';
var offline_button  = '{{ offline_button }}';
var connect_lang    = '{{ "ChatConnected"|get_lang }}';
var disconnect_lang = '{{ "ChatDisconnected"|get_lang }}';

function get_url_params(q, attribute) {
    var vars;
    var hash;
    if (q != undefined) {
        q = q.split('&');
        for(var i = 0; i < q.length; i++){
            hash = q[i].split('=');
            if (hash[0] == attribute) {
                return hash[1];
            }
        }
    }
}
$(document).ready(function(){
    $("#open-view-list").click(function(){
        $("#student-list-work").fadeIn(300);
    });
    $("#closed-view-list").click(function(){
        $("#student-list-work").fadeOut(300);
    });
});
function check_brand() {
    if ($('.subnav').length) {
        if ($(window).width() >= 969) {
            $('.subnav .brand').hide();
        } else {
            $('.subnav .brand').show();
        }
    }
}

$(window).resize(function() {
    check_brand();
});

$(document).scroll(function() {

    // Top bar scroll effect
    if ($('body').width() > 959) {
        if ($('.subnav').length) {
            if (!$('.subnav').attr('data-top')) {
                // If already fixed, then do nothing
                if ($('.subnav').hasClass('subnav-fixed')) return;
                // Remember top position
                var offset = $('.subnav').offset();
                $('.subnav').attr('data-top', offset.top);
            }

            if ($('.subnav').attr('data-top') - $('.subnav').outerHeight() <= $(this).scrollTop()) {
                $('.subnav').addClass('subnav-fixed');
            } else {
                $('.subnav').removeClass('subnav-fixed');
            }
            //$('.subnav .brand').show();
        }
    } else {
        //$('.subnav .brand').hide();
    }

    //Exercise warning fixed at the top
    var fixed =  $("#exercise_clock_warning");
    if (fixed.length) {
        if (!fixed.attr('data-top')) {
            // If already fixed, then do nothing
            if (fixed.hasClass('subnav-fixed')) return;
            // Remember top position
            var offset = fixed.offset();
            fixed.attr('data-top', offset.top);
            fixed.css('width', '100%');
        }

        if (fixed.attr('data-top') - fixed.outerHeight() <= $(this).scrollTop()) {
            fixed.addClass('subnav-fixed');
            fixed.css('width', '100%');
        } else {
            fixed.removeClass('subnav-fixed');
            fixed.css('width', '200px');
        }
    }

    // Admin -> Settings toolbar.
    if ($('body').width() > 959) {
        if ($('.new_actions').length) {
            if (!$('.new_actions').attr('data-top')) {
                // If already fixed, then do nothing
                if ($('.new_actions').hasClass('new_actions-fixed')) return;
                // Remember top position
                var offset = $('.new_actions').offset();

                var more_top = 0;
                if ($('.subnav').hasClass('new_actions-fixed')) {
                    more_top = 50;
                }
                $('.new_actions').attr('data-top', offset.top + more_top);
            }

            if ($('.new_actions').attr('data-top') - $('.new_actions').outerHeight() <= $(this).scrollTop()) {
                $('.new_actions').addClass('new_actions-fixed');
            } else {
                $('.new_actions').removeClass('new_actions-fixed');
            }
        }
    }

    // Bottom actions.
    if ($('.bottom_actions').length) {
        if (!$('.bottom_actions').attr('data-top')) {
            // If already fixed, then do nothing
            if ($('.bottom_actions').hasClass('bottom_actions_fixed')) return;

            // Remember top position
            var offset = $('.bottom_actions').offset();
            $('.bottom_actions').attr('data-top', offset.top);
        }

        if ($('.bottom_actions').attr('data-top') > $('body').outerHeight()) {
            if ( ($('.bottom_actions').attr('data-top') - $('body').outerHeight() - $('.bottom_actions').outerHeight()) >= $(this).scrollTop()) {
                $('.bottom_actions').addClass('bottom_actions_fixed');
                $('.bottom_actions').css("width", "100%");
            } else {
                $('.bottom_actions').css("width", "");
                $('.bottom_actions').removeClass('bottom_actions_fixed');
            }
        } else {
            if ( ($('.bottom_actions').attr('data-top') -  $('.bottom_actions').outerHeight()) <= $(this).scrollTop()) {
                $('.bottom_actions').addClass('bottom_actions_fixed');
                $('.bottom_actions').css("width", "100%");
            } else {
                $('.bottom_actions').removeClass('bottom_actions_fixed');
                $('.bottom_actions').css("width", "");
            }
        }
    }
});

function showConfirmationPopup(obj, urlParam)
{
    if (urlParam) {
        url = urlParam
    } else {
        url = obj.href;
    }

    var dialog  = $("#dialog");
    if ($("#dialog").length == 0) {
        dialog  = $('<div id="dialog" style="display:none">{{ "ConfirmYourChoice" | get_lang }} </div>').appendTo('body');
    }

    var width_value = 350;
    var height_value = 150;
    var resizable_value = true;

    var new_param = get_url_params(url, 'width');
    if (new_param) {
        width_value = new_param;
    }

    var new_param = get_url_params(url, 'height')
    if (new_param) {
        height_value = new_param;
    }

    var new_param = get_url_params(url, 'resizable');
    if (new_param) {
        resizable_value = new_param;
    }

    // Show dialog
    dialog.dialog({
        modal       : true,
        width       : width_value,
        height      : height_value,
        resizable   : resizable_value,
        buttons: [
            {
                text: '{{ 'Yes' | get_lang }}',
                click: function() {
                    window.location = url;
                },
                icons:{
                    primary:'ui-icon-locked'
                }
            },
            {
                text: '{{ 'No' | get_lang }}',
                click: function() { $(this).dialog("close"); },
                icons:{
                    primary:'ui-icon-locked'
                }
            }
        ]
    });
    // prevent the browser to follow the link
    return false;
}

$(function() {

    check_brand();

    // Removes the yellow input in Chrome
    if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
        $(window).load(function(){
            $('input:-webkit-autofill').each(function(){
                var text = $(this).val();
                var name = $(this).attr('name');
                $(this).after(this.outerHTML).remove();
                $('input[name=' + name + ']').val(text);
            });
        });
    }

    // Fixes buttons to the new btn class.
    /* if (!$('#button').hasClass('btn')) {
        $("button").addClass('btn');
    } */

    // Dropdown effect.
    $('.dropdown-toggle').dropdown();

    // Responsive effect.
    $(".collapse").collapse();

    $(".accordion_jquery").accordion({
        autoHeight: false,
        active: false, // all items closed by default
        collapsible: true,
        header: ".accordion-heading"
    });

    // Global popup
    $('body').on('click', 'a.ajax', function(e) {
        e.preventDefault();

        var contentUrl = this.href,
            loadModalContent = $.get(contentUrl);

        $.when(loadModalContent).done(function(modalContent) {
            var modalDialog = $('#global-modal').find('.modal-dialog'),
                modalSize = get_url_params(contentUrl, 'modal_size'),
                modalWidth = get_url_params(contentUrl, 'width');

            modalDialog.removeClass('modal-lg modal-sm').css('width', '');

            if (modalSize) {
                switch (modalSize) {
                    case 'lg':
                        modalDialog.addClass('modal-lg');
                        break;
                    case 'sm':
                        modalDialog.addClass('modal-sm');
                        break;
                }
            } else if (modalWidth) {
                modalDialog.css('width', modalWidth + 'px');
            }

            $('#global-modal').find('.modal-body').html(modalContent);

            $('#global-modal').modal('show');
        });
    });

    $('a.expand-image').on('click', function(e) {
        e.preventDefault();

        var title = $(this).attr('title');

        var image = new Image();
        image.onload = function() {
            if (title) {
                $('#expand-image-modal').find('.modal-title').text(title);
            } else {
                $('#expand-image-modal').find('.modal-title').html('&nbsp;');
            }

            $('#expand-image-modal').find('.modal-body').html(image);
            $('#expand-image-modal').modal({
                show: true
            });
        };
        image.src = this.href;
    });

    // Global confirmation
    $('.popup-confirmation').on('click', function() {
        showConfirmationPopup(this);
        return false;
    });

    // old jquery.menu.js
    $('#navigation a').stop().animate({
        'marginLeft':'50px'
    },1000);

    $('#navigation > li').hover(
        function () {
            $('a',$(this)).stop().animate({
                'marginLeft':'1px'
            },200);
        },
        function () {
            $('a',$(this)).stop().animate({
                'marginLeft':'50px'
            },200);
        }
    );

    /* Make responsive image maps */
    $('map').imageMapResize();
});
</script>
{{ css_custom_file_to_string }}
{{ css_style_print }}
{# Extra header configured in admin section, only shown to non-admins #}
{{ header_extra_content }}

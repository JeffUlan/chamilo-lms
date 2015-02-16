/* For licensing terms, see /license.txt */
/**
 * JS library for the Chamilo buy-courses plugin
 * @package chamilo.plugin.buycourses
 */
$(document).ready(function () {
    $("input[name='price']").change(function () {
        $(this).parent().next().children().attr("style", "display:none");
        $(this).parent().next().children().next().attr("style", "display:''");
        $(this).parent().parent().addClass("fmod")
        $(this).parent().parent().children().each(function () {
            $(this).addClass("btop");
        });
    });

    $("input[name='price']").keyup(function () {
        $(this).parent().next().children().attr("style", "display:none");
        $(this).parent().next().children().next().attr("style", "display:''");
        $(this).parent().parent().addClass("fmod")
        $(this).parent().parent().children().each(function () {
            $(this).addClass("btop");
        });
    });

    $("input[name='visible']").change(function () {
        $(this).parent().next().next().children().attr("style", "display:none");
        $(this).parent().next().next().children().next().attr("style", "display:''");
        $(this).parent().parent().addClass("fmod")
        $(this).parent().parent().children().each(function () {
            $(this).addClass("btop");
        });
    });

    $(".save").click(function () {
        var visible = $(this).parent().parent().prev().prev().children().attr("checked");
        var price = $(this).parent().parent().prev().children().attr("value");
        var course_id = $(this).attr('id');
        var courseOrSession = $(this).parent().parent()[0].attributes[0].value;
        if (courseOrSession.indexOf("session") > -1) {
            courseOrSession = "session_id";
        } else {
            courseOrSession = "course_id";
        }
        var courseOrSessionObject = {tab: "save_mod", visible: visible, price: price};
        courseOrSessionObject[courseOrSession] = course_id;
        $.post("function.php", courseOrSessionObject,
            function (data) {
                if (data.status == "false") {
                    alert("Database Error");
                } else {
                    courseOrSession = courseOrSession.replace("_id", "");
                    $("#" + courseOrSession + data.course_id).children().attr("style", "display:''");
                    $("#" + courseOrSession + data.course_id).children().next().attr("style", "display:none");
                    $("#" + courseOrSession + data.course_id).parent().removeClass("fmod")
                    $("#" + courseOrSession + data.course_id).parent().children().each(function () {
                        $(this).removeClass("btop");
                    });
                }
            }, "json");
    });

    $('#sync').click(function (e) {
        $.post("function.php", {tab: "sync"},
            function (data) {
                if (data.status == "false") {
                    alert(data.contenido);
                } else {
                    alert(data.contenido);
                    location.reload();
                }
            }, "json");
        e.preventDefault();
        e.stopPropagation();
    });

    $(".filter").click(function (e) {
        var target = "#"+($(this).closest(".row").children().last()).attr("id");
        var filterFields = $(this).siblings("input");
        var filterFieldsData = { tab: $(this).attr("id") };
        $.each(filterFields, function() {
            // Get only the first class
            var className = $(this).attr("class").split(" ")[0];
            filterFieldsData[className] = $(this).val();
        });
        $.post("function.php", filterFieldsData,
            function (data) {
                if (data.status == "false") {
                    alert(data.content);
                    $(target).html('');
                } else {
                    $(target).html(data.content);
                }
                $(document).ready(acciones_ajax);
            }, "json");
        e.preventDefault();
        e.stopPropagation();
    });

    $("#save_currency").click(function (e) {
        var currency_type = $("#currency_type").attr("value");
        $.post("function.php", {tab: "save_currency", currency: currency_type},
            function (data) {
                alert(data.content);
            }, "json");

        e.preventDefault();
        e.stopPropagation();
    });

    $("#save_paypal").click(function (e) {
        var name = $("#username").attr("value");
        var clave = $("#password").attr("value");
        var firma = $("#signature").attr("value");
        if ($("#sandbox").attr("checked") == "checked") {
            var vsandbox = "YES";
        } else {
            var vsandbox = "NO";
        }
        $.post("function.php", {tab: "save_paypal", username: name, password: clave, signature: firma, sandbox: vsandbox},
            function (data) {
                alert(data.content);
            }, "json");

        e.preventDefault();
        e.stopPropagation();
    });

    $("#add_account").click(function (e) {
        var tname = $("#tname").attr("value");
        var taccount = $("#taccount").attr("value");
        var tswift = $("#tswift").attr("value");
        if (tname == '' || taccount == '') {
            alert("Complete los campos antes de insertar");
        } else {
            $.post("function.php", {tab: "add_account", name: tname, account: taccount, swift: tswift},
                function (data) {
                    location.reload();
                }, "json");
        }
        e.preventDefault();
        e.stopPropagation();
    });

    $(".delete_account").click(function (e) {
        var fieldName = $(this).parent().attr("id");
        var id = $("#id_" + fieldName).val();
        $.post("function.php", {tab: "delete_account", id: id},
            function (data) {
                location.reload();
            }, "json");

        e.preventDefault();
        e.stopPropagation();
    });

    $("#cancel_order").click(function (e) {
        $.post("function.php", {tab: "unset_variables"});
        window.location.replace("list.php");
    });

    $(".clear_order").click(function (e) {
        var vid = $(this).parent().attr("id");
        $.post("function.php", {tab: "clear_order", id: vid},
            function (data) {
                location.reload();
            }, "json");

        e.preventDefault();
        e.stopPropagation();
    });

    $(".confirm_order").click(function (e) {
        var vid = $(this).parent().attr("id");
        $.post("function.php", {tab: "confirm_order", id: vid},
            function (data) {
                location.reload();
            }, "json");

        e.preventDefault();
        e.stopPropagation();
    });

    $(".slt_tpv").change(function () {
        var vcod = $(this).attr("value");
        $.post("function.php", {tab: "activar_tpv", cod: vcod});
    });
});

function acciones_ajax() {
    $('.ajax').on('click', function () {
        var url = this.href;
        var dialog = $("#dialog");
        if ($("#dialog").length == 0) {
            dialog = $('<div id="dialog" style="display:none"></div>').appendTo('body');
        }
        width_value = 580;
        height_value = 450;
        resizable_value = true;
        new_param = get_url_params(url, 'width');
        if (new_param) {
            width_value = new_param;
        }
        new_param = get_url_params(url, 'height')
        if (new_param) {
            height_value = new_param;
        }
        new_param = get_url_params(url, 'resizable');
        if (new_param) {
            resizable_value = new_param;
        }
        // load remote content
        dialog.load(
            url,
            {},
            function (responseText, textStatus, XMLHttpRequest) {
                dialog.dialog({
                    modal: true,
                    width: width_value,
                    height: height_value,
                    resizable: resizable_value
                });
            });
        //prevent the browser to follow the link
        return false;
    });


}


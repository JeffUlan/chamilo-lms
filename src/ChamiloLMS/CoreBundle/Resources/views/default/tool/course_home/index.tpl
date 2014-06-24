{% extends "@template_style/layout/layout_1_col.tpl" %}
{% block content %}

<script>
    $(document).ready(function() {
        $(".make_visible_and_invisible").attr("href", "javascript:void(0);");
        $(".make_visible_and_invisible > img").click(function () {

            var make_visible = "visible.gif";
            var make_invisible = "invisible.gif";
            var path_name = $(this).attr("src");
            var list_path_name = path_name.split("/");
            var image_link = list_path_name[list_path_name.length - 1];
            var tool_id = $(this).attr("id");
            var tool_info = tool_id.split("_");
            var my_tool_id = tool_info[1];

            $.ajax({
                contentType: "application/x-www-form-urlencoded",
                beforeSend: function(data) {
                    $(".normal-message").show();
                    $("#id_confirmation_message").hide();
                },
                type: "GET",
                url: "{{ _p.web_ajax }}course_home.ajax.php?cidReq={{ course.code }}&a=set_visibility",
                data: "id=" + my_tool_id + "&sent_http_request=1",
                success: function(data) {
                    eval("var info=" + data);
                    var new_current_tool_image = info.image;
                    var new_current_view = "{{ _p.web_img}}" + info.view;
                    // Eyes
                    $("#" + tool_id).attr("src", new_current_view);

                    // Tool
                    $("#toolimage_" + my_tool_id).attr("src", new_current_tool_image);

                    // Class
                    $("#tooldesc_" + my_tool_id).attr("class", info.tclass);
                    $("#istooldesc_" + my_tool_id).attr("class", info.tclass);

                    if (image_link == "visible.gif") {
                        $("#" + tool_id).attr("alt", "{{ 'Activate' | trans }}");
                        $("#" + tool_id).attr("title", "{{ 'Activate' | trans }}");
                    } else {
                        $("#" + tool_id).attr("alt", "{{ 'Deactivate' | trans }}");
                        $("#" + tool_id).attr("title", "{{ 'Deactivate' | trans }}");
                    }
                    if (info.message == "is_active") {
                        message = "{{ 'ToolIsNowVisible' | trans }}";
                    } else {
                        message = "{{ 'ToolIsNowHidden' | trans }}";
                    }
                    $(".normal-message").hide();
                    $("#id_confirmation_message").html(message);
                    $("#id_confirmation_message").show();
                }
            });
        });
    });

    /* toogle for post-it in course home */
    $(function() {
        $(".thematic-postit-head").click(function() {
            $(".thematic-postit-center").slideToggle("fast");
        });
    });

</script>

<div class="courseadminview" style="border:0px; margin-top: 0px;padding:0px;">
    <div class="normal-message" id="id_normal_message" style="display:none">
        <img src="{{ _p.web_img }}loading1.gif" />
        {{ 'PleaseStandBy' | trans }}
    </div>
    <div class="confirmation-message" id="id_confirmation_message" style="display:none">
    </div>
</div>

{% if session_info %}
    <div class="courseadminview">
        <span class="viewcaption">{{ 'SessionData' | trans }}</span>
        <table class="course_activity_home">
            {{ session_info }}
        </table>
    </div>
{% endif %}

{{ lp_warning }}
{{ exercise_warning }}
{{ introduction_text }}
{{ edit_icons }}

<div id="course_tools">
    {{ icons }}
</div>

{% endblock %}

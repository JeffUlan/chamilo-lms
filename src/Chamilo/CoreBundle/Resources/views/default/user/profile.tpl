{% extends "@template_style/layout/layout_2_col.tpl" %}

{% block left_column %}

    <script>

        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                //updateTips( "Length of " + n + " must be between " + min + " and " + max + "." );
                return false;
            } else {
                return true;
            }
        }

        function send_message_to_user(user_id) {
            var subject = $( "#subject_id" );
            var content = $( "#content_id" );
            $("#send_message_form").show();
            $("#send_message_div").dialog({
                modal:true,
                height:350,
                buttons: {
                    "{{ 'Send' | trans }}": function() {
                        var bValid = true;
                        bValid = bValid && checkLength( subject, "subject", 1, 255 );
                        bValid = bValid && checkLength( content, "content", 1, 255 );

                        if ( bValid ) {
                            var url = "{{ _p.web_ajax_path}}message.ajax.php?a=send_message&user_id="+user_id;
                            var params = $("#send_message_form").serialize();
                            $.ajax({
                                url: url+"&"+params,
                                success:function(data) {
                                    $("#message_ajax_reponse").html(data);
                                    $("#message_ajax_reponse").show();
                                    $("#send_message_div").dialog({ buttons:{}});
                                    $("#send_message_form").hide();
                                    $("#send_message_div").dialog("close");
                                    $("#subject_id").val("");
                                    $("#content_id").val("");
                                }
                            });
                        }
                    }
                },
                close: function() {
                }
            });
            $("#send_message_div").dialog("open");
            //prevent the browser to follow the link
        }

        function send_invitation_to_user(user_id) {
            var content = $( "#content_invitation_id" );
            $("#send_invitation_form").show();
            $("#send_invitation_div").dialog({
                modal:true,
                buttons: {
                    "{{ 'SendInvitation' | trans }}": function() {
                        var bValid = true;
                        bValid = bValid && checkLength( content, "content", 1, 255 );
                        if (bValid) {
                            var url = "{{ _p.web_ajax_path}}message.ajax.php?a=send_invitation&user_id="+user_id;
                            var params = $("#send_invitation_form").serialize();
                            $.ajax({
                                url: url+"&"+params,
                                success:function(data) {
                                    $("#message_ajax_reponse").html(data);
                                    $("#message_ajax_reponse").show();
                                    $("#send_invitation_div").dialog({ buttons:{}});
                                    $("#send_invitation_form").hide();
                                    $("#send_invitation_div").dialog("close");
                                    $("#content_invitation_id").val("");
                                }
                            });
                        }
                    }
                },
                close: function() {
                }
            });
            $("#send_invitation_div").dialog("open");
            //prevent the browser to follow the link
        }

        $(document).ready(function (){
            $("input#id_btn_send_invitation").bind("click", function(){
                if (confirm("'.trans('SendMessageInvitation', '').'")) {
                    $("#form_register_friend").submit();
                }
            });

            $("#send_message_div").dialog({
                autoOpen: false,
                modal    : false,
                width    : 550,
                height    : 300
            });

            $("#send_invitation_div").dialog({
                autoOpen: false,
                modal    : false,
                width    : 550,
                height    : 300
            });

        });
    </script>

    <div class="well social-background-content">
        <img src="{{ user.avatar }}"/>
    </div>

    <div class="well sidebar-nav">
        <ul class="nav nav-pills nav-stacked">
            <li>
                <a href="javascript:void(0);" onclick="javascript:send_message_to_user('{{ user.user_id }}');">
                    {{ 'SendMessage' | trans }}
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" onclick="javascript:send_invitation_to_user('{{ user.user_id }}');">
                    {{ 'SendInvitation' | trans }}
                </a>
            </li>
        </ul>
    </div>

{% endblock %}

{% block right_column %}
    <span id ="message_ajax_reponse"></span>
    <div class="well_border">
        <h3>{{ user.complete_name }} @{{ user.username }} </h3>
    </div>
    {{ form_send_message }}
    {{ form_send_invitation }}
{% endblock %}

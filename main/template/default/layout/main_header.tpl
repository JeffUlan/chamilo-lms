<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="{{ document_language }}" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html lang="{{ document_language }}" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html lang="{{ document_language }}" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="{{ document_language }}" class="no-js"> <!--<![endif]-->
<head>
{% block head %}
{% include template ~ "/layout/head.tpl" %}
{% endblock %}
</head>
<body dir="{{ text_direction }}" class="{{ section_name }} {{ login_class }}">
<noscript>{{ "NoJavascript"|get_lang }}</noscript>

<!-- Display the Chamilo Uses Cookies Warning Validation if needed -->
{% if displayCookieUsageWarning == true %}
    <!-- If toolbar is displayed, we have to display this block bellow it -->
    {% if toolBarDisplayed == true %}
        <div class="displayUnderToolbar" >&nbsp;</div>
    {% endif %}
    <form onSubmit="$(this).toggle('slow')" action="" method="post">
        <input value=1 type="hidden" name="acceptCookies"/>
        <div class="cookieUsageValidation">
            {{ "YouAcceptCookies" | get_lang }}
            <span style="margin-left:20px;" onclick="$(this).next().toggle('slow'); $(this).toggle('slow')">
                ({{"More" | get_lang }})
            </span>
            <div style="display:none; margin:20px 0;">
                {{ "HelpCookieUsageValidation" | get_lang}}
            </div>
            <span style="margin-left:20px;" onclick="$(this).parent().parent().submit()">
                ({{"Accept" | get_lang }})
            </span>
        </div>
    </form>
{% endif %}

{% if show_header == true %}
    <div class="skip">
        <ul>
            <li><a href="#menu">{{ "WCAGGoMenu"|get_lang }}</a></li>
            <li><a href="#content" accesskey="2">{{ "WCAGGoContent"|get_lang }}</a></li>
        </ul>
    </div>
{% endif %}
    <div id="page" class="container"> <!-- page section -->
        {# Bug and help notifications #}
        {% block help_notifications %}
        <ul id="navigation" class="notification-panel">
            {{ help_content }}
            {{ bug_notification_link }}
        </ul>
        {% endblock %}

        {# topbar #}
        {% block topbar %}
        {% include template ~ "/layout/topbar.tpl" %}
        {% endblock %}
            <header>
                <div class="container">
                    {% if plugin_header_main %}
                    <div class="row">
                        <div class="col-lg-12">
                            {{ plugin_header_main }}
                        </div>
                    </div>
                    {% endif %}
                    <div class="row">
                        <div class="col-lg-3">
                            {{ logo }}
                        </div>
                        <div class="col-lg-9">
                            <div class="col-4">
                                {% if plugin_header_left is not null %}
                                <div id="plugin_header_left">
                                    {{ plugin_header_left }}
                                </div>
                                {% endif %}
                            </div>
                            <div class="col-4">
                                {% if plugin_header_center is not null %}
                                <div id="plugin_header_center">
                                    {{ plugin_header_center }}
                                </div>
                                {% endif %}
                            </div>
                            <div class="col-4">
                                {% if plugin_header_right is not null %}
                                <div id="plugin_header_right">
                                    {{ plugin_header_right }}
                                </div>
                                {% endif %}
                                <div class="section-notifications">
                                    <ul id="notifications" class="nav nav-pills pull-right">
                                        {{ notification_menu }}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <section id="menu-bar">
                {# menu #}
                {% block menu %}
                {% include template ~ "/layout/menu.tpl" %}
                {% endblock %}
            </section>
            <section id="breadcrumb-bar">
                {# breadcrumb #}
                {% block breadcrumb %}
                {{ breadcrumb }}
                {% endblock %}
            </section>


        
        <div id="top_main_content">

        {# course navigation links/shortcuts need to be activated by the admin #}
        {% include template ~ "/layout/course_navigation.tpl" %}
{% for row in session %}
    <div class="panel panel-default">
        {% if not row.show_simple_session_info %}
            {% set collapsable = '' %}
            <div class="panel-heading">
                {% if row.course_list_session_style == 1 %}
                    {# Classic #}
                    <a href="{{ _p.web_main ~ 'session/index.php?session_id=' ~ row.id }}">
                        <img id="session_img_{{ row.id }}" src="{{ "window_list.png"|icon(32) }}" width="32" height="32"
                             alt="{{ row.title }}" title="{{ row.title }}"/>
                        {{ row.title }}
                    </a>
                {% elseif row.course_list_session_style == 2 %}
                    {# No link #}
                    <img id="session_img_{{ row.id }}" src="{{ "window_list.png"|icon(32) }}" width="32" height="32"
                         alt="{{ row.title }}" title="{{ row.title }}"/>
                    {{ row.title }}
                {% elseif row.course_list_session_style == 3 %}
                    {# Foldable #}
                    <a role="button" data-toggle="collapse" data-parent="#page-content" href="#collapse_{{ row.id }}"
                       aria-expanded="false">
                        <img id="session_img_{{ row.id }}" src="{{ "window_list.png"|icon(32) }}" width="32" height="32"
                             alt="{{ row.title }}" title="{{ row.title }}"/>
                        {{ row.title }}
                    </a>
                    {% set collapsable = 'collapse' %}
                {% endif %}

                {% if row.show_actions %}
                    <div class="pull-right">
                        <a href="{{ _p.web_main ~ "session/resume_session.php?id_session=" ~ row.id }}">
                            <img src="{{ "edit.png"|icon(22) }}" width="22" height="22" alt="{{ "Edit"|get_lang }}"
                                 title="{{ "Edit"|get_lang }}"/>
                        </a>
                    </div>
                {% endif %}
            </div>
        {% endif %}

        <div class="session panel-body {{ collapsable }}" id="collapse_{{ row.id }}">
            {% if row.show_simple_session_info %}
                <div class="row">
                    <div class="col-md-7">
                        {% if row.description != '' %}
                            {{ row.description }}
                        {% endif %}
                        <div class="info-session">
                            {# Classic #}
                            <span>
                                <a href="{{ _p.web_main ~ 'session/index.php?session_id=' ~ row.id }}">
                                <img id="session_img_{{ row.id }}" src="{{ "window_list.png"|icon(32) }}" width="32" height="32"
                                     alt="{{ row.title }}" title="{{ row.title }}"/>
                                {{ row.title }}
                                </a>
                            </span>
                            {% if row.coach_name  != '' %}
                                <span>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    {{ row.coach_name }}
                                </span>
                            {% endif %}
                        <span>
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            {{ row.date }}
                        </span>
                        </div>
                        {% if row.coaches %}
                            <h5 class="teacher-name">{{ "teacher.png"|icon(16) ~ row.coaches }}</h5>
                        {% endif %}
                    </div>

                    {% if row.show_actions %}
                        <div class="col-md-5 text-right">
                            <a href="{{ _p.web_main ~ "session/resume_session.php?id_session=" ~ row.id }}">
                                <img src="{{ "edit.png"|icon(22) }}" width="22" height="22" alt="{{ "Edit"|get_lang }}"
                                     title="{{ "Edit"|get_lang }}">
                            </a>
                        </div>
                    {% endif %}
                </div>
            {% else %}
                <div class="row">
                    <div class="col-md-12">
                        {% if row.description != '' %}
                            {{ row.description }}
                        {% endif %}
                        <div class="info-session">
                            {% if row.coach_name  != '' %}
                                <span><i class="fa fa-user" aria-hidden="true"></i>
                                    {{ row.coach_name }}
                                </span>
                            {% endif %}
                            <span>
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                {{ row.date }}
                            </span>
                        </div>
                        <div class="sessions-items">
                        {% for item in row.courses %}
                            <div class="courses">
                                <div class="row">
                                    <div class="col-md-2">
                                        {% if item.link %}
                                            <a href="{{ item.link }}" class="thumbnail">
                                                <img class="img-responsive" src="{{ item.icon }}">
                                            </a>
                                        {% else %}
                                            {{ 'blackboard.png' | img(48, item.title ) }}
                                        {% endif %}
                                    </div>
                                    <div class="col-md-10">
                                        <h4>{{ item.title }}</h4>
                                        <div class="list-teachers">
                                            {% if item.coaches|length > 0 %}
                                                <img src="{{ 'teacher.png'|icon(16) }}" width="16" height="16">&nbsp;
                                                {% for coach in item.coaches %}
                                                    {{ loop.index > 1 ? ' | ' }}
                                                    <a href="{{ _p.web_ajax ~ 'user_manager.ajax.php?' ~ {'a': 'get_user_popup', 'user_id': coach.user_id}|url_encode() }}"
                                                       data-title="{{ coach.full_name }}" class="ajax">
                                                        {{ coach.firstname }},
                                                        {{ coach.lastname }}
                                                    </a>
                                                {% endfor %}
                                            {% endif %}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                        </div>
                    </div>
                </div>
            {% endif %}
        </div>
    </div>
{% endfor %}
<div class="panel panel-default">
    {% if not session.show_simple_session_info %}
        <div class="panel-heading">
            {% if session.show_link_to_session %}
                <a href="{{ _p.web_main ~ 'session/index.php?session_id=' ~ session.id }}">
                    <img id="session_img_{{ session.id }}" src="{{ "window_list.png"|icon(32) }}" alt="{{ session.title }}" title="{{ session.title }}">
                    {{ session.title }}
                </a>
            {% else %}
                <img id="session_img_{{ session.id }}" src="{{ "window_list.png"|icon(32) }}" alt="{{ session.title }}" title="{{ session.title }}">
                {{ session.title }}
            {% endif %}
            {% if session.show_actions %}
                <div class="pull-right">
                    <a href="{{ _p.web_main ~ "session/resume_session.php?id_session=" ~ session.id }}">
                        <img src="{{ "edit.png"|icon(22) }}" alt="{{ "Edit"|get_lang }}" title="{{ "Edit"|get_lang }}">
                    </a>
                </div>
            {% endif %}
        </div>
    {% endif %}

    <div class="sessions panel-body">
        {% if session.show_simple_session_info %}
            <div class="row">
                <div class="col-md-7">
                    <h3>
                        {{ session.title ~ session.notifications }}
                    </h3>

                    {% if session.show_description %}
                        <div>
                            {{ session.description }}
                        </div>
                    {% endif %}

                    {% if session.subtitle %}
                        <small>{{ session.subtitle }}</small>
                    {% endif %}

                    {% if session.teachers %}
                        <h5>{{ "teacher.png"|icon(16) ~ session.teachers }}</h5>
                    {% endif %}

                    {% if session.coaches %}
                        <h5>{{ "teacher.png"|icon(16) ~ session.coaches }}</h5>
                    {% endif %}
                </div>

                {% if session.show_actions %}
                    <div class="col-md-5 text-right">
                        <a href="{{ _p.web_main ~ "session/resume_session.php?id_session=" ~ session.id }}">
                            <img src="{{ "edit.png"|icon(22) }}" alt="{{ "Edit"|get_lang }}" title="{{ "Edit"|get_lang }}">
                        </a>
                    </div>
                {% endif %}
            </div>
        {% else %}
            <div class="row">
                <div class="col-md-12">
                    {% if session.subtitle %}
                        <p class="subtitle-session">
                            <i class="fa fa-clock-o"></i> <em>{{ session.subtitle }}</em>
                        </p>
                    {% endif %}
                    {% if session.show_description %}
                        <div class="description-session">
                            {{ session.description }}
                        </div>
                    {% endif %}
                    <div class="sessions-items">
                        {% for item in session.courses %}
                            <div class="row">
                                <div class="col-md-2">
                                    {% if item.link %}
                                        <a href="{{ item.link }}" class="thumbnail">{{ item.icon }}</a>
                                    {% else %}
                                        {{ item.icon }}
                                    {% endif %}
                                </div>
                                <div class="col-md-10">
                                    {{ item.title }}
                                    {{ item.coaches }}
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
</div>

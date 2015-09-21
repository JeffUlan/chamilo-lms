{% extends template ~ "/layout/layout_1_col.tpl" %}

{% block content %}
<div class="row">
    <div class="col-md-3">
        {{ social_avatar_block }}
        {{ social_extra_info_block }}
        <div class="social-network-menu">
            {{ social_menu_block }}
        </div>
    </div>
    <div id="wallMessages" class="col-md-6">
        {{ social_wall_block }}
        {{ social_post_wall_block }}
        {{ social_auto_extend_link }}
    </div>
    <div class="col-md-3">
        <div class="chat-friends">
                <div class="panel-group" id="blocklistFriends" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#blocklistFriends" href="#listFriends" aria-expanded="true" aria-controls="listFriends">
                                    {{ "SocialFriend" | get_lang }}
                                </a>
                            </h4>
                        </div>
                    <div id="listFriends" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            {{ social_friend_block }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ social_skill_block }}
        {{ social_group_info_block }}
        <!-- Block course list -->
         {% if social_course_block != null %}
         <div class="panel-group" id="course-block" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#course-block" href="#courseList" aria-expanded="true" aria-controls="courseList">
                                    {{ "MyCourses" | get_lang }}
                                </a>
                            </h4>
                        </div>
                    <div id="courseList" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <ul class="list-group">
                                {{ social_course_block }}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
         {% endif %}
        <!-- Block session list -->
        {% if sessionList != null %}
        <div class="panel-group" id="session-block" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#session-block" href="#sessionList" aria-expanded="true" aria-controls="sessionList">
                           {{ "MySessions" | get_lang }}
                        </a>
                    </h4>
                </div>
                <div id="sessionList" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <ul class="list-group">
                            {% for session in sessionList %}
                            <li id="session_{{ session.id }}" class="list-group-item" style="min-height:65px;">
                                <img class="img-session" src="{{ session.image }}"/>
                                <span class="title">{{ session.name }}</span>
                            </li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
            </div>
         </div>
         {% endif %}

        {{ social_rss_block }}
        {{ social_right_information }}
    </div>
</div>
    {% if form_modals is defined %}
        {{ form_modals }}
    {% endif %}
{% endblock %}

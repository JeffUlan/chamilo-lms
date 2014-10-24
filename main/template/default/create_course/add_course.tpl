{% if just_created == 1%}
{{ just_created_link }}
<h3>{{ 'JustCreated'|get_lang }} {{ course_title }}</h3>
<hr />
{% endif %}

<h3>{{ 'ThingsToDo'|get_lang }}</h3>
<br />

<div id="course_thing_to_do" class="row">
    <div class="span3">
        <div class="thumbnail">
            <img src="{{ _p.web_img }}icons/64/info.png"/>
            <div class="caption">
                <a href="{{ _p.web_main }}course_description/?cidReq={{ course_id }}" class="btn">
                    {{'AddCourseDescription'|get_lang}}
                </a>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="thumbnail">
            <img src="{{ _p.web_img }}icons/64/folder_document.png"/>
            <div class="caption">
                <a href="{{ _p.web_main }}document/document.php?cidReq={{ course_id }}" class="btn">
                    {{'UploadADocument'|get_lang}}
                </a>
            </div>
        </div>
    </div>
    <div class="span3">
        <div class="thumbnail">
            <img src="{{ _p.web_img }}icons/64/forum.png"/>
            <div class="caption">
                <a href="{{ _p.web_main }}forum/index.php?cidReq={{ course_id }}" class="btn">
                    {{ 'AddForum'|get_lang }}
                </a>
            </div>
        </div>
    </div>
    {% if ("allow_user_course_subscription_by_course_admin" | get_setting) == 'true' or _u.is_admin == 1 %}
    <div class="span3">
        <div class="thumbnail">
        <img src="{{ _p.web_img }}icons/64/user.png"/>
            <div class="caption">
            <a href="{{ _p.web_main }}user/subscribe_user.php?cidReq={{ course_id }}" class="btn">
                {{ 'SubscribeUserToCourse'|get_lang }}
            </a>
            </div>
        </div>
    </div>
    {% endif %}


</div>

<div class="clear"></div>

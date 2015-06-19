<div id="about-session">
{% for course_data in courses %}
    {% if courses|length > 1 %}
 <div class="row">
    <div class="col-xs-12">
        <h2 class="text-uppercase">{{ course_data.course.getTitle }}</h2>
    </div>
 </div>
    {% endif %}

    <div class="row">
        {% if course_data.video %}
            <div class="col-sm-6 col-md-7">
                <div class="embed-responsive embed-responsive-16by9">
                    {{ course_data.video }}
                </div>
            </div>
        {% endif %}

        <div class="{{ course_data.video ? 'col-sm-6 col-md-5' : 'col-sm-12' }}">
            <div class="description-course">
                {{ course_data.description.getContent }}
            </div>
            {% if course_data.tags %}
                <div class="tags-course">
                    <i class="fa fa-tags"></i>
                       {% for tag in course_data.tags %}
                       <a href="#">{{ tag.getTag }}</a>
                       {% endfor %}
                </div>
            {% endif %}
                <div class="subscribe">
                    <a href="#" class="btn btn-success btn-lg btn-block"><i class="fa fa-book"></i> {{ "Subscribe"|get_lang }}</a>
                </div>
        </div>
    </div>



    <div class="row info-course">
        <div class="col-xs-12 col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>{{ "CourseInformation"|get_lang }}</h4>
                </div>
                <div class="panel-body">
                    {% if course_data.objectives %}
                    <div class="objective-course">
                        <h4 class="title-info"><i class="fa fa-book"></i> {{ "Objectives"|get_lang }}</h4>
                        <div class="content-info">
                            {{ course_data.objectives.getContent }}
                        </div>

                    </div>
                    {% endif %}
                    {% if course_data.topics %}
                    <div class="topics">
                        <h4 class="title-info"><i class="fa fa-book"></i> {{ "Topics"|get_lang }}</h4>
                        <div class="content-info">
                            {{ course_data.topics.getContent }}
                        </div>

                    </div>
                    {% endif %}
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-5">
            {% if course_data.coaches %}
            <div class="panel panel-default teachers">
                <div class="panel-heading">
                    <h4>{{ "Coaches"|get_lang }}</h4>
                </div>
                <div class="panel-body">
                    {% for coach in course_data.coaches %}
                    <div class="row">
                        <div class="col-xs-7 col-md-7">
                            <h4>{{ coach.complete_name }}</h4>
                            {% if coach.officer_position %}
                            <p>{{ coach.officer_position }}</p>
                            {% endif %}

                            {% if coach.work_or_study_place %}
                            <p>{{ coach.work_or_study_place }}</p>
                            {% endif %}
                        </div>
                        <div class="col-xs-5 col-md-5">
                            <div class="text-center">
                                <img class="img-circle" src="{{ coach.image }}" alt="{{ coach.complete_name }}">
                            </div>
                        </div>
                    </div>
                    {% endfor %}
                </div>
            </div>
            {% endif %}
            <div class="panel panel-default social-share">
                <div class="panel-heading">{{ "ShareWithYourFriends"|get_lang }}</div>
                <div class="panel-body">
                    <div class="icons-social text-center">
                        <a href="#" class="btn bnt-link btn-lg">
                            <i class="fa fa-facebook fa-2x"></i>
                        </a>
                        <a href="#" class="btn bnt-link btn-lg">
                            <i class="fa fa-twitter fa-2x"></i>
                        </a>
                        <a href="#" class="btn bnt-link btn-lg">
                            <i class="fa fa-linkedin fa-2x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="text-center">
                <a href="#" class="btn btn-success btn-lg btn-block"><i class="fa fa-book"></i> {{ "Subscribe"|get_lang }}</a>
            </div>
        </div>
    </div>
{% endfor %}
</div>
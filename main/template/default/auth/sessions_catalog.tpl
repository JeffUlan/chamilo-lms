{% extends template ~ "/layout/main.tpl" %}

{% block body %}
    <script type="text/javascript">
        $().ready(function() {
            $('#date').datepicker({
                dateFormat: 'yy-mm-dd'
            });

            $('.accordion').click(function(e) {
                e.preventDefault();
                var tempTarget = e.target.toString().split('#');
                tempTarget = '#' + tempTarget[1];
                // use the target of the link as the ID of the element to find
                var target = $(tempTarget);
                var targetContent = target.find('.accordion-inner');

                if (targetContent.is(':empty')) {
                    var idParts = tempTarget.split('-');

                    var sessionId = parseInt(idParts[1], 10);

                    $.ajax('{{ web_session_courses_ajax_url }}', {
                        data: {
                            a: 'display_sessions_courses',
                            session: sessionId
                        },
                        dataType: 'json',
                        success: function(response) {
                            var coursesUL = '';

                            $.each(response, function(index, course) {
                                coursesUL += '<li><img src="{{ _p.web }}main/img/check.png"/> <strong>' + course.name + '</strong>';

                                if (course.coachName != '') {
                                    coursesUL += ' (' + course.coachName + ')';
                                }

                                coursesUL += '</li>';
                            });

                            targetContent.html('<ul class="items-session">' + coursesUL + '</ul>');
                            target.css({
                                height: targetContent.outerHeight()
                            }).addClass('in');
                        }
                    });
                } else {
                    target.addClass('in');
                }
            });
        });
    </script>

    <div class="col-md-3">
        {% if showCourses %}
            <div class="well">
                {% if not hiddenLinks %}
                    <form class="form-search" method="post" action="{{ courseUrl }}">
                        <fieldset>
                            <input type="hidden" name="sec_token" value="{{ searchToken }}">
                            <input type="hidden" name="search_course" value="1" />
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-append">
                                        <input class="span2" type="text" name="search_term" />
                                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> {{ 'Search' | get_lang }}</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                {% endif %}

                {% if coursesCategoriesList is not empty %}
                    <a class="btn" href="{{ api_get_self }}?action=display_random_courses">{{ 'RandomPick' | get_lang }}</a>
                {% endif %}
            </div>

            {% if coursesCategoriesList is not empty %}
                <div class="well sidebar-nav">
                    <h4>{{ 'CourseCategories' | get_lang }}</h4>
                    <ul class="nav nav-list">
                        {{ coursesCategoriesList }}
                    </ul>
                </div>
            {% endif %}
        {% endif %}

        {% if showSessions %}
            <div class="well sidebar-nav">
                <h4>{{ 'Sessions' | get_lang }}</h4>
                <ul class="nav nav-list">
                    <li>{{ 'SearchSessions' | get_lang }}</li>
                    <li>
                        <form class="form-search" method="post" action="{{ api_get_self }}?action=display_sessions">
                            <div class="input-append">
                                <input type="date" name="date" id="date" class="span2 search-session" value="{{ searchDate }}" readonly>
                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> {{ 'Search' | get_lang }}</button>
                            </div>
                        </form>
                    </li>
                </ul>
            </div>
        {% endif %}
    </div>
    <div class="col-md-9">
        {% for session in sessions_blocks %}
            <div class="well well-small session-group" id="session-{{ session.id }}">
                <div class="row">
                    <div class="col-md-9">
                        <div class="row padding-clear">
                            <div class="col-md-2">
                                <span class="thumbnail">
                                    {{ session.icon }}
                                </span>
                            </div>
                            <div class="col-md-10 border-info">
                                <h3>{{ session.name }}</h3>
                                <div class="tutor">
                                    <img src="{{ 'teacher.png' | icon(22) }}" width="16"> {{ 'GeneralCoach' | get_lang }} {{ session.coach_name }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="accordion" id="session-{{ session.id }}-accordion">
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#session-{{ session.id }}-accordion" href="#session-{{ session.id }}-courses">
                                            {{ 'CourseList' | get_lang }}
                                        </a>
                                    </div>
                                    <div id="session-{{ session.id }}-courses" class="accordion-body collapse in">
                                        <div class="accordion-inner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        {% if session.showDescription %}
                            <div class="buttom-subscribed">
                                <a class="ajax btn btn-large btn-info" href="{{ _p.web_ajax }}session.ajax.php?a=get_description&session={{ session.id }}">{{ 'Description' | get_lang }}</a>
                            </div>
                        {% endif %}

                        <div class="buttom-subscribed">
                            {% if session.is_subscribed %}
                                {{ already_subscribed_label }}
                            {% else %}
                                {{ session.subscribe_button }}
                            {% endif %}
                        </div>
                        <div class="time"><img src="{{ 'agenda.png' | icon(22) }}"> {{ session.date }}</div>
                    </div>
                </div>
            </div>
        {% endfor %}
        {{ cataloguePagination }}
    </div>

{% endblock %}

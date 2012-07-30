{% if hot_courses is not null and hot_courses is not empty %}

<script type="text/javascript">
$(document).ready( function() {
    $('.star-rating li a').live('click', function(event) {
        var id = $(this).parents('ul').attr('id');
        $('#vote_label2_' + id).html("{{'Loading'|get_lang}}");
        $.ajax({
            url: $(this).attr('data-link'),
            success: function(data) {
                $("#rating_wrapper_"+id).html(data);
                if(data == 'added') {
                    //$('#vote_label2_' + id).html("{{'Saved'|get_lang}}");
                }
                if(data == 'updated') {
                    //$('#vote_label2_' + id).html("{{'Saved'|get_lang}}");
                }
            }
        });
    });

});
</script>

    <section id="hot_courses">
        <div class="row">
            <div class="span9">
                {% if _u.is_admin %}
                <span class="pull-right">
                    <a href="{{_p.web_main}}admin/settings.php?search_field=show_hot_courses&submit_button=&_qf__search_settings=&category=search_setting"><img src="{{_p.web_img}}icons/32/visible.png" alt="{{ "Hide" | get_lang}}" title="{{ "Hide" | get_lang}}" /></a>
                </span>
                {% endif %}
                {{"HottestCourses"|display_page_header}}
            </div>
            {% for hot_course in hot_courses %}
                
                {% if hot_course.extra_info.title %}
                <div class="span9">
                    <div class="well_border">
                        <div class="row">
                            <div class="span2">
                                <div class="thumbnail">
                                    <img src="{{ hot_course.extra_info.course_image }}" />
                                    {# html_image file=$hot_course.extra_info.course_image #}
                                </div>
                            </div>

                            <div class="span6">
                                <div class="categories-course-description">
                                    <h3>{{ hot_course.extra_info.title}}</h3>
                                    <h5>{{ hot_course.extra_info.teachers }}</h5>

                                    {{ hot_course.extra_info.rating_html }}
                                </div>
                                <p>                                
                                    {{ hot_course.extra_info.go_to_course_button }}
                                    {{ hot_course.extra_info.description_button }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                {% endif %}
            {% endfor %}
        </div>
    </section>
{% endif %}

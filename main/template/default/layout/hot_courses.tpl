{if !(empty($hot_courses)) }
    
    <script type="text/javascript">
    $(document).ready( function() {
        $('.star-rating li a').live('click', function(event) {        
            var id = $(this).parents('ul').attr('id');        
            $('#vote_label2_' + id).html("{'Loading'|get_lang}");           
            $.ajax({
                url: $(this).attr('data-link'),
                success: function(data) {
                    $("#rating_wrapper_"+id).html(data);
                    if(data == 'added') {                                                                        
                        //$('#vote_label2_' + id).html("{'Saved'|get_lang}");
                    }
                    if(data == 'updated') {
                        //$('#vote_label2_' + id).html("{'Saved'|get_lang}");
                    }
                }
            });        
        });

    });
    </script>
    <section id="hot_courses">
        <div class="row">    
            <div class="span9">
                <div class="page-header">
                    <h3>{"HottestCourses"|get_lang}</h3>
                </div>
            </div>
        {foreach $hot_courses as $hot_course}										
            <div class="span9">
                <div class="categories-block-course ">            
                <div class="categories-content-course">

                    <div class="categories-course-picture">
                        <img src="{$hot_course.extra_info.course_image}" />
                        {* html_image file=$hot_course.extra_info.course_image *}
                    </div>

                    <div class="categories-course-description">
                        <div class="course-block-title">{$hot_course.extra_info.name|truncate:60}</div>							
                        {$hot_course.extra_info.rating_html}					
                    </div>			
                </div>

                <div class="categories-course-links">
                    
                    {* World *}
                    {if ($hot_course.extra_info.visibility == 3)}
                        <div class="course-link-desc right">
                            <a class="btn btn-primary" title="" href="{$_p.web_course}{$hot_course.extra_info.path}/index.php">
                                {"GoToCourse"|get_lang}
                            </a>
                        </div>
                    {/if}
                    
                    {* Description *}
                    <div class="course-link-desc right">
                    {if ($hot_course.extra_info.visibility == 3)} 
                        <a class="ajax btn" title="" href="{$_p.web_ajax}course_home.ajax.php?a=show_course_information&code={$hot_course.course_code}">
                            {"Description"|get_lang}
                        </a>
                    {/if}								
                    </div>

              			
                </div>
                </div>
            </div>            
        {/foreach}
        </div>
    </section>
{/if}
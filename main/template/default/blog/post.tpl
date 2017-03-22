<div class="row">
    <div class="col-md-3">
        <div class="sidebar">
            <div id="calendar-blog" class="panel panel-default">
                <div class="panel-heading">
                    {{ 'Calendar'|get_lang }}
                </div>
                <div class="panel-body">
                    {{ calendar }}
                </div>
            </div>
            <div id="search-blog" class="panel panel-default">
                <div class="panel-heading">
                    {{ 'Search'|get_lang }}
                </div>
                <div class="panel-body">
                    <form action="blog.php" method="get" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="hidden" name="blog_id" value="{{ id_blog }}"/>
                            <input type="hidden" name="action" value="view_search_result"/>
                            <input type="text" class="form-control" size="20" name="q" value="{{ search }}"/>
                        </div>
                        <button class="btn btn-default btn-block" type="submit">
                            <em class="fa fa-search"></em> {{ 'Search'|get_lang }}
                        </button>
                    </form>
                </div>
            </div>
            <div id="task-blog" class="panel panel-default">
                <div class="panel-heading">
                    {{ 'MyTasks'|get_lang }}
                </div>
                <div class="panel-body">
                    {{ task }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="blog" id="post-{{ post.id_post }}">
            <div class="panel panel-default" id="blog-header">
                <div class="panel-heading">
                    <div id="post-action" class="text-right">
                        <div class="btn-group btn-group-sm" role="group" aria-label="{{ 'Actions'|get_lang }}">
                            {{ post.actions }}
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <article>
                        <header>
                            <div class="title">
                                <h1 title="{{ post.title }}">{{ post.title }}</h1>
                            </div>
                            <ul class="info-post list-inline">
                                <li class="date">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i> {{ post.post_date }}
                                </li>
                                <li class="comments">
                                    <i class="fa fa-comment-o"
                                       aria-hidden="true"></i> {{ 'XComments'|get_lang|format(post.n_comments) }}
                                </li>
                                <li class="autor">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    <a href="{{ _p.web }}main/social/profile.php?u={{ post.id_author }}">{{ post.author }}</a>
                                </li>
                                <li class="score">
                                    <i class="fa fa-star" aria-hidden="true"></i> {{ post.score_ranking }}
                                </li>
                            </ul>
                        </header>
                        <div class="content-post">
                            {{ post.content }}
                        </div>
                        {% if post.files %}
                            <aside class="well well-sm files">
                                <i class="fa fa-paperclip" aria-hidden="true"></i>
                                <a href="download.php?file={{ post.files.path }}">{{ post.files.filename }}</a>
                            </aside>
                        {% endif %}

                        {{ post.frm_rating ?: '' }}
                    </article>
                    <div class="comments-post">
                        <h3 class="title">{{ 'XComments'|get_lang|format(post.n_comments) }}</h3>
                        <div id="list-comments" class="media-list">
                            {% for item in post.comments %}
                                <div class="media">
                                    <div class="media-left">
                                        <a href="{{ _p.web }}main/social/profile.php?u={{ item.id_author }}">
                                            <img class="media-object thumbnail avatar"
                                                 src="{{ item.info_user.dir }}{{ item.info_user.file }}"
                                                 alt="{{ item.name_author }}">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <div class="pull-right">
                                            {{ item.actions }}
                                        </div>
                                        <h4 class="media-heading" id="comment-{{ item.iid }}-title">{{ item.title }}</h4>
                                        <ul class="info-post list-inline">
                                            <li class="date">
                                                <i class="fa fa-clock-o"></i> {{ item.comment_date }}
                                            </li>
                                            <li class="autor">
                                                <i class="fa fa-user"></i>
                                                <a href="{{ _p.web }}main/social/profile.php?u={{ item.id_author }}">
                                                    {{ item.name_author }}
                                                </a>
                                            </li>
                                            <li class="score">
                                                <i class="fa fa-star" aria-hidden="true"></i> {{ item.score_ranking }}
                                            </li>
                                        </ul>
                                        <div id="comment-{{ item.iid }}-content">
                                            {{ item.content }}
                                        </div>
                                        {% if item.files %}
                                            <aside class="well well-sm files">
                                                <i class="fa fa-paperclip" aria-hidden="true"></i> <a
                                                        href="download.php?file={{ item.files.path }}">{{ item.files.filename }}</a>
                                                <p>{{ item.files.comment }}</p>
                                            </aside>
                                        {% endif %}

                                        <div class="ranking">
                                            {{ item.form_ranking }}
                                        </div>

                                        {% for item2 in item.comments %}
                                            <div class="media {{ item2.iid }}">
                                                <div class="media-left">
                                                    <a href="{{ _p.web }}main/social/profile.php?u={{ item2.id_author }}">
                                                        <img class="media-object thumbnail avatar"
                                                             src="{{ item2.info_user.dir }}{{ item2.info_user.file }}"
                                                             alt="{{ item2.name_author }}">
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <div class="pull-right">
                                                        {{ item2.actions }}
                                                    </div>
                                                    <h4 class="media-heading" id="comment-{{ item2.iid }}-title">{{ item2.title }}</h4>
                                                    <ul class="info-post list-inline">
                                                        <li class="date">
                                                            <i class="fa fa-clock-o"></i> {{ item2.comment_date }}
                                                        </li>
                                                        <li class="autor">
                                                            <i class="fa fa-user"></i>
                                                            <a href="{{ _p.web }}main/social/profile.php?u={{ item2.id_author }}">
                                                                {{ item2.name_author }}
                                                            </a>
                                                        </li>
                                                        <li class="score">
                                                            <i class="fa fa-star" aria-hidden="true"></i> {{ item2.score_ranking }}
                                                        </li>
                                                    </ul>
                                                    <div id="comment-{{ item2.iid }}-content">
                                                        {{ item2.content }}
                                                    </div>
                                                    {% if item2.files %}
                                                        <aside class="well well-sm files">
                                                            <i class="fa fa-paperclip" aria-hidden="true"></i> <a
                                                                    href="download.php?file={{ item2.files.path }}">{{ item2.files.filename }}</a>
                                                            <p>{{ item2.files.comment }}</p>
                                                        </aside>
                                                    {% endif %}

                                                    <div class="ranking">
                                                        {{ item2.form_ranking }}
                                                    </div>
                                                </div>
                                            </div>
                                        {% endfor %}
                                    </div>
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                    <div class="form-post">
                        {{ post.form_html }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).on('ready', function () {
        $('.btn-reply-to').on('click', function (e) {
            e.preventDefault();

            var id = $(this).data('id') || 0;

            if (!id) {
                return;
            }

            var $frm = $('form#add_post'),
                title = $('#comment-' + id + '-title'),
                content = $('#comment-' + id + '-content');

            $frm.find('[name="comment_parent_id"]').val(id);
            $frm.find('[name="title"]').val('Re: ' + title.text().trim());
            CKEDITOR.instances.comment.setData('<blockquote>' + content.text().trim() + '</blockquote><br>');
        });
    });
</script>

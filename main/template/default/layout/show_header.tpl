{% include app.template_style ~ "/layout/main_header.tpl" %}
{#
    show_header and show_footer templates are only called when using the Display::display_header and Display::display_footer
    for backward compatibility we suppose that the default layout is one column which means using a div with class span12
#}

{% if app.template.show_header == true %}
        {% if plugin_content_top is not null %}
            <div id="plugin_content_top" class="span12">
                {{ plugin_content_top }}
            </div>
        {% endif %}
        <div class="span12">
            {% include app.template_style ~ "/layout/page_body.tpl" %}
            {% block main_content_section_block %}<section id="main_content">{% endblock main_content_section_block %}
{% endif %}

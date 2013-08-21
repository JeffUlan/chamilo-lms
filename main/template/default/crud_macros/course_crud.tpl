{% macro list(items, links, course) %}
    <a href="{{ url(links.create_link, {'course': course.code }) }}">
        {{ 'Add' |trans }}
    </a>
    <table class="table">
    {% for item in items %}
        <tr>
            <td>
                <a href="{{ url(links.read_link, {'course': course.code, id: item.id }) }}">
                {{ item.name }}
                </a>
            </td>
            <td>
                <a class="btn" href="{{ url(links.update_link, {'course': course.code, id: item.id }) }}"> {{ 'Edit' |trans }}</a>
                <a class="btn" href="{{ url(links.delete_link, {'course': course.code, id: item.id }) }}"> {{ 'Delete' |trans }}</a>
            </td>
        </tr>
    {% endfor %}
    </table>
{% endmacro %}

{% macro add(form, links, course) %}
    <a href="{{ url(links.list_link, {'course': course.code }) }}">
        {{ 'List' |trans }}
    </a>
    <hr />
    <form action="{{ url(links.create_link, {'course': course.code }) }}" method="post" {{ form_enctype(form) }}>
        {{ form_widget(form) }}
    </form>
{% endmacro %}

{% macro edit(form, links, course) %}
    <a href="{{ url(links.list_link, {'course': course.code}) }}">
        {{ 'List' |trans }}
    </a>
    <form action="{{ url(links.update_link, {'course': course.code, id : item.id }) }}" method = "post" {{ form_enctype(form) }}>
        {{ form_widget(form) }}
    </form>
{% endmacro %}

{% macro read(item, links, course) %}
    <a href="{{ url(links.list_link, {'course': course.code}) }}">
        {{ 'List' |trans }}
    </a>
    <a href="{{ url(links.update_link, {'course': course.code, id : item.id }) }}">
        {{ 'Edit' |trans }}
    </a>
    <h2>{{ item.id }}</h2>
    <p>{{ item.name }}</p>
{% endmacro %}


{% extends template ~ "/layout/layout_1_col.tpl" %}

{% block content %}
    {{ form }}
    <table class="table promotions">
        <thead class="title">
            <tr>
                <th>{{ 'Name' | get_lang }}</th>
                <th>{{ 'Course' | get_lang }}</th>
                <th>{{ 'Actions' | get_lang }} </th>
            </tr>
        </thead>

    {% for item in gradebook_list %}
        <tr>
            <td>
                {{ item.name }}
            </td>
            <td>
                {{ item.courseCode }}
            </td>
            <td>
                <a href="{{ current_url }}&action=edit&id={{ item.id }}">
                    <img src="{{ 'edit.png'|icon(22) }}" />
                </a>
            </td>
        </tr>
    {% endfor %}
    </table>

   {{ gradebook_list }}
{% endblock %}

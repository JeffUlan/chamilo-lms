{{ form }}

<table class="data_table">
    <tr>
        <th>{{ 'Name' | get_lang }}</th>
        <th>{{ 'ShortName' | get_lang }}</th>

        <th>{{ 'Profile' | get_lang }}</th>
        <th>{{ 'Actions' | get_lang }}</th>
    </tr>
    {% for item in list %}
        <tr>
            <td>{{ item.name }}</td>
            <td>{{ item.shortName }}</td>
            <td> {{ item.profile }}</td>
            <td>
                <a href="{{ _p.web_main }}admin/skill_level.php?action=edit&id={{ item.id }}">
                    <img src="{{ 'edit.png'|icon(22) }}">
                </a>

                <a href="{{ _p.web_main }}admin/skill_level.php?action=delete&id={{ item.id }}">
                    <img src="{{ 'delete.png'|icon(22) }}">
                </a>
            </td>
        </tr>
    {% endfor %}
</table>
<header class="page-header">
    <h1>{{ "ManageSkills" | get_lang }}</h1>
</header>

<div class="table table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>{{ "Name" | get_lang }}</th>
                <th>{{ "ShortCode" | get_lang }}</th>
                <th>{{ "Description" | get_lang }}</th>
                <th>{{ "Options" | get_lang }}</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th width="200">{{ "Name" | get_lang }}</th>
                <th class="text-center">{{ "ShortName" | get_lang }}</th>
                <th width="300">{{ "Description" | get_lang }}</th>
                <th class="text-right">{{ "Options" | get_lang }}</th>
            </tr>
        </tfoot>
        <tbody>
            {% for skill in skills %}
                <tr>
                    <td width="200">{{ skill.name }}</td>
                    <td class="text-center">{{ skill.short_code }}</td>
                    <td width="300">{{ skill.description }}</td>
                    <td class="text-right">
                        <a href="{{ _p.web_main }}admin/skill_edit.php?id={{ skill.id }}" class="btn btn-default btn-sm">
                            <i class="fa fa-edit"></i> {{ "Edit" | get_lang }}
                        </a>
                        <a href="{{ _p.web_main }}admin/skill_create.php?parent={{ skill.id }}" class="btn btn-default btn-sm">
                            <i class="fa fa-plus"></i> {{ "CreateChildSkill" | get_lang }}
                        </a>
                        <a href="{{ _p.web_main }}admin/skill_badge_create.php?id={{ skill.id }}" class="btn btn-default btn-sm">
                            <i class="fa fa-shield"></i> {{ "CreateBadge" | get_lang }}
                        </a>
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>

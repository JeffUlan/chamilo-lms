<script>
    $(document).on('ready', function () {
        $('select#session').on('change', function () {
            var sessionId = parseInt(this.value, 10),
                    $selectCourse = $('select#course');

            $selectCourse.empty();

            $.get('{{ _p.web_main }}inc/ajax/course.ajax.php', {
                a: 'display_sessions_courses',
                session: sessionId
            }, function (courseList) {
                var $option = null;

                $('<option>', {
                    value: 0,
                    text: "{{ 'Select' | get_lang }}"
                }).appendTo($selectCourse);

                if (courseList.length > 0) {
                    $.each(courseList, function (index, course) {
                        $('<option>', {
                            value: course.id,
                            text: course.name
                        }).appendTo($selectCourse);
                    });
                }
            }, 'json');
        });
    });
</script>

{{ form }}

{% if errorMessage is defined %}
    <div class="alert alert-error">{{ errorMessage }}</div>
{% endif %}

{% if not certificateStudents is empty %}
    <div class="actions">
        <a href="{{ exportAllLink }}" class="btn btn-info">
            <i class="fa fa-check"></i> {{ 'ExportAllCertificatesToPDF' | get_lang }}
        </a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ 'Student' | get_lang }}</th>
                <th>{{ 'Sesion' | get_lang }}</th>
                <th>{{ 'Course' | get_lang }}</th>
                <th>{{ 'Date' | get_lang }}</th>
                <th>{{ 'Certificate' | get_lang }}</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>{{ 'Student' | get_lang }}</th>
                <th>{{ 'Sesion' | get_lang }}</th>
                <th>{{ 'Course' | get_lang }}</th>
                <th>{{ 'Date' | get_lang }}</th>
                <th>{{ 'Certificate' | get_lang }}</th>
            </tr>
        </tfoot>
        <tbody>
            {% for student in certificateStudents %}
                <tr>
                    <td>{{ student.fullName }}</td>
                    <td>{{ student.sessionName }}</td>
                    <td>{{ student.courseName }}</td>
                    <td>
                        {% for certificate in student.certificates %}
                            <p>{{ certificate.createdAt }}</p>
                        {% endfor %}
                    </td>
                    <td>
                        {% for certificate in student.certificates %}
                            <a href="{{ _p.web }}certificates/index.php?id={{ certificate.id }}" class="btn btn-default"><i class="fa fa-floppy-o"></i> {{ 'Certificate' | get_lang }}</a>
                        {% endfor %}
                    </td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
{% else %}
    <p class="alert alert-info">{{ 'NoResults' | get_lang }}</p>
{% endif %}

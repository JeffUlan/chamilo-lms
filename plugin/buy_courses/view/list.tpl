<script type='text/javascript' src="../js/funciones.js"></script>
<link rel="stylesheet" type="text/css" href="../resources/plugin.css"/>

<div class="row">
    <div class="span3">
        <div id="course_category_well" class="well">
            <ul class="nav nav-list">
                <li class="nav-header"><h4>{{ 'Filtro_buscar'|get_lang }}:</h4></li>
                <li class="nav-header">{{ 'Curso'|get_lang }}:</li>
                <li><input type="text" id="course_name" style="width:95%"/></li>
                <li class="nav-header">{{ 'Price_Minimum'|get_lang }}: <input type="text" id="price_min" class="span1"/>
                </li>
                <li class="nav-header">{{ 'Price_Maximum'|get_lang }}: <input type="text" id="price_max" class="span1"/>
                </li>
                <li class="nav-header">{{ 'Mostrar_disponibles'|get_lang }}: &nbsp;<input type="checkbox"
                                                                                          id="mostrar_disponibles"
                                                                                          value="SI"/></li>
                <li class="nav-header">{{ 'Categorias'|get_lang }}:</li>
                <li><select id="categoria_cursos">
                        <option value="" selected="selected"></option>
                        {% for categoria in categorias %}
                        <option value="{{ categoria.code }}">{{ categoria.name }}</option>
                        {% endfor %}
                        </select>
                        </li>
                        <br />
                        <li class="ta-center"><input type="button" class="btn btn-primary" value="Buscar cursos" id="confirmar_filtro" /></li>
                        </ul>
                        </div>
                        </div>
                        <div class="span9" id="resultado_cursos">
                        {% if rmensaje == "SI" %}
                        <div class="{{ estilo }}">{{ mensaje }}
        </div>
        {% endif %}
        {% for curso in cursos %}
        <div class="well_border span8">
        <div class="row">
        <div class="span">
        <div class="thumbnail">
        <a class="ajax" rel="gb_page_center[778]" title="" href="{{ server }}plugin/buy_courses/function/ajax.php?code={
        { curso.code }}">
        <img alt="" src="{{ server }}{{ curso.imagen_curso }}">
        </a>
    </div>
</div>
<div class="span4">
    <div class="categories-course-description">
        <h3>{{ curso.title }}</h3>
        <h5>Profesor: {{ curso.profesor }}</h5>
    </div>
    {% if curso.matriculado == "SI" %}
    <span class="label label-info">{{ 'bc_registrado'|get_lang }}</span>
    {% endif %}
    {% if curso.matriculado == "TMP" %}
    <span class="label label-warning">{{ 'bc_tmp_registrado'|get_lang }}</span>
    {% endif %}
    </div>
    <div class="span right">
    <div class="sprice right">{{ curso.price }} {{ moneda }}
</div>
<div class="cleared"></div>
<div class="btn-toolbar right">
    <a class="ajax btn btn-primary" title=""
       href="{{ server }}plugin/buy_courses/function/ajax.php?code={{ curso.code }}">{{ 'Description'|get_lang }}</a>
    {% if curso.matriculado == "NO" %}
    <a class="btn btn-success" title="" href="{{ server }}plugin/buy_courses/process.php?code={{ curso.id }}">{{
    'Buy'|get_lang }}</a>
{% endif %}
</div>
</div>
</div>
</div>
{% endfor %}
</div>








</div>
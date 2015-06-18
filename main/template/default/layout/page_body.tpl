{#  Actions  #}
{% if actions != '' %}
    <div class="actions">
        {{ actions }}
    </div>
{% endif %}

{{ flash_messages }}

{#  Page header #}
{% if header != '' %}
    <div class="page-header">
        <h2>{{ header }}</h2>
    </div>
{% endif %}

{#  Show messages #}
{% if message != '' %}
    <section id="messages">
        {{ message}}
    </section>
{% endif %}

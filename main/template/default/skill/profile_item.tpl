{% if profiles is not null %}
          <h5 class="title-skill">{{ "SkillProfiles"|get_lang }}</h5>
        <div class="items_save">
            <ul class="holder_simple">
            {%for profile in profiles %}
            <li class="bit-box load_profile" rel="{{ profile.id }}" >
                <button class="close">&times;</button>
                <a href="#">{{ profile.name }}</a>
            </li>        
            {% endfor %}
            </ul>
        </div>
{% endif %}

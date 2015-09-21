{{ form }}

<script>
    $('form[name="start_video_chat"]').on('submit', function(e) {
        e.preventDefault();

        var createChatRoom = $.post(
            '{{ _p.web_ajax }}chat.ajax.php',
            {
                room_name: $(this).find('input[name="chat_room_name"]').val(),
                to: $(this).find('input[name="to"]').val(),
                action: 'create_room'
            },
            null,
            'json'
        );

        $.when(createChatRoom).done(function(response) {
            if (!response.url) {
                return;
            }

            window.location.href = response.url;
        });
    });
</script>

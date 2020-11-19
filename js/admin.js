$(function () {

    $('form.save_credentials')
            .submit(function () {
                var $form = $(this);
                var $save = $('button.save', $form);
                var $status = $('button.status', $form);

                $form.ajaxError(function (e, jqxhr, settings, exception) {
                    alert('Failed to save.');
                });

                $.post($form.attr('action'), $form.serialize(), function (response, status) {
                    $save.prop('disabled', true);
                    $status.prop('disabled', false);
                    $('span', $save).text('Saved!');
                }, 'json');

                return false;
            })
            .find('input[type=text]').keyup(function (event) {
        // skip enter
        if (event.which == 13)
            return;

        var $form = $(this).closest('form');
        var $token = $('button.token', $form);
        var $save = $('button.save', $form);

        // change something? enable save button
        $save.prop('disabled', false).find('span').text('Save');

        if ($('input[name=client_key]', $form).val() && $('input[name=client_secret]', $form).val()) {
            $token.prop('disabled', false);
        }
        else {
            $token.prop('disabled', true);
        }
    });

    // Disable all save buttons, stop caching state
    $('form.save_credentials button.save').prop('disabled', true);

    // Disable all token buttons, if credentials are not there
    $('div.provider').each(function () {
        $provider = $(this);

        // If they have stuff then show
        if ($('input[name=client_key]', $provider).val() && $('input[name=client_secret]', $provider).val()) {
            $('button.token', $provider).prop('disabled', false);
            $('button.status', $provider).prop('disabled', false).removeProp('disabled');
            return;
        }

        // Otherwise disable that token button!
        $('button.token', $provider).prop('disabled', true);
        $('button.status', $provider).prop('disabled', true);
    });

    $('button.clear').click(function () {
        var provider = $(this).closest('div.provider').data('provider');
        $.post(SITE_URL + 'admin/social/remove_credentials', {provider: provider}, function () {
            window.location.href = window.location.href;
        });
    });

    $('button.status').click(function () {

        var $provider = $(this).closest('div.provider'),
                provider = $provider.data('provider'),
                status = this.value;

        $.post(SITE_URL + 'admin/social/save_status/' + provider, {status: status, csrf_hash_name: app.csrf_hash}, function () {
            if (parseInt(status) === 1) {
                $('button[name=enable]', $provider).hide();
                $('button[name=disable]', $provider).removeClass('hidden').show();
            }
            else {
                $('button[name=enable]', $provider).removeClass('hidden').show();
                $('button[name=disable]', $provider).hide();
            }
        });
    });

    $('button.token').click(function () {

        var provider = $(this).closest('div.provider').data('provider');
        var url = SITE_URL + 'admin/social/token_redirect/' + provider;

        auth_window = window.open(url, 'provider-auth', 'width=600,height=500');

        auth_window.onunload = function () {
            window.location.href = window.location.href;
        }
    });


    $('div.tokens dd span').live('click', function () {
        $(this).parent().html($('<input/>').val($(this).text()).css('width', '100%').prop('readonly', true));
    });

    $('div.tokens dd input').live('blur', function () {
        $(this).parent().html($('<span/>').text($(this).val()));
    });

});
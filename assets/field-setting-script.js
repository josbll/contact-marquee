jQuery(document).ready(function($) {

    
//funciones de FieldSettingView 

 const fieldNames = [
        'show_title',
        'show_subtitle',
        'show_phone',
        'show_email',
        'show_whatsapp',
        'show_instagram',
        'show_facebook',
        'show_tiktok',
        'show_telegram',
        'show_linkedin'
    ];

    const $marqueeSelect = $('select[name="marquee_name"]');

    function resetCheckboxes(state = true) {
        fieldNames.forEach(name => {
            $(`input[name="${name}"]`).prop('checked', state);
        });
    }

    $marqueeSelect.on('change', function () {
        const marqueeName = $(this).val();

        if (!marqueeName) {
            resetCheckboxes(true);
            return;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'imp_get_field_settings',
                marquee_name: marqueeName
            },
            success: function (response) {
       
                if (!response.success || !response.data) {
                    resetCheckboxes(true);
                    return;
                }

                fieldNames.forEach(name => {
                    const value = response.data[name];
                    $(`input[name="${name}"]`).prop('checked', value == 1);
                });
            },
            error: function () {
                resetCheckboxes(true);
            }
        });
    });


});
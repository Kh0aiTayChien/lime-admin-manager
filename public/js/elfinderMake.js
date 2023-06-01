let host = window.location.origin;
let csrfToken = $('meta[name="csrf-token"]').attr('content')

$(document).ready(function () {
    $('#elfinder').elfinder({
        lang: 'vi',
        height: '80%',
        customData: {
            _token: csrfToken
        },
        url: host+"/elfinder/connector", // connector URL
        soundPath: host+"/packages/barryvdh/elfinder/sounds"
    });
})

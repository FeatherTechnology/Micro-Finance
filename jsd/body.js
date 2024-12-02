$(document).ready(function () {

    $.post('api/base_api/getSessionData.php', function (response) {
        if (response != null) {

            $('#search_screens').click(function () {
                let search_input = $('#search_input_').val();
                if (search_input != '') {
                    $.post('api/base_api/getScreens.php', { search_input }, function (response) {
                        let append = '';
                        $.each(response, function (index, val) {
                            if (val.display_name != undefined) {
                                append += "<li class='dropdown-contents'><a href='" + val.module_name + "'>" + val.display_name + "</a></li>";
                            }
                        })
                        $('#search_ul').empty().append(append);
                    }, 'json');
                }
            })

            getPageHeaderName();

            getBodyContentPage();
        } else {
            const current_page = localStorage.getItem('currentPage');

            if (current_page != 'index.php' && current_page != ''){
                window.location.href = 'index.php';
            }
        }
    }, 'json');
})

function getPageHeaderName() {
    $.post('api/base_api/getPageHeaderName.php', { current_page: localStorage.getItem('currentPage') }, function (response) {
        if (response.length != 0) {
            $('#pageHeaderName').text(' - ' + response.sub_menu);
        }
    }, 'json')
}

function getBodyContentPage() {
    const current_page = localStorage.getItem('currentPage');
    $.post('api/base_api/getBodyContentPage.php', { current_page: current_page }, function (response) {
        $('#main-container').html(response);
        if ($('#main-container .error')) {//removed the page header name when 404 called
            $('#pageHeaderName').text("");
        }
    })
}
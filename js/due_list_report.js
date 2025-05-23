$(document).ready(function () {

    $("#due_type").change(function () {
        if ($(this).val() == "2") {
            $("#week_days").show();
        } else {
            $("#week_days").hide();
            $("#week_days").val("");
        }
    });


    $('#due_list_report_btn').click(function () {
        let search_date = $('#search_date').val();
        let due_type = $('#due_type').val();
        let week_days = $('#week_days').val();

        if (search_date !== '' && due_type !== '' && (due_type != 2 || week_days !== '')) {

            $('#due_list_report_table').DataTable().destroy();
            $('#due_list_report_table').DataTable({
                "order": [
                    [0, "desc"]
                ],
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': 'api/report_files/get_due_list_report.php',
                    'data': function (data) {
                        var search = $('input[type=search]').val();
                        data.search = search;
                        data.search_date = $('#search_date').val();
                        data.due_type = $('#due_type').val();
                        data.week_days = $('#week_days').val();
                    }
                },
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'excel',
                    title: "Closed Report List"
                },
                {
                    extend: 'colvis',
                    collectionLayout: 'fixed four-column',
                }
                ],
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            });
        }
        else {
            swalError('Please Fill Dates!', 'Both Date and Due Method are Required.');

        }



    })

});
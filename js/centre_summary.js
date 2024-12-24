$(document).ready(function () {
    $('input[name=customer_data_type]').click(function () {
        let centreType = $(this).val();
        if (centreType == 'cen_current') {
            $('.centre_loan_current').show();
            $('.centre_loan_closed').hide();
        } else if (centreType == 'cen_closed') {
            $('.centre_loan_closed').show();
            $('.centre_loan_current').hide();
        }
    })


    $(document).on('click', '#centre_current', function () {
        getCentreCurrentTable();
    })
    $(document).on('click', '#centre_closed', function () {
        getCentreClosedTable();
    })
});


//////////////////////////////////////Document End
$(function () {
    getCentreCurrentTable();
});

function getCentreCurrentTable() {
    serverSideTable('#centre_current_table', '', 'api/centre_summary_files/centre_current_list.php');
}
function getCentreClosedTable() {
    serverSideTable('#centre_close_table', '', 'api/centre_summary_files/centre_closed_list.php');
}
$(document).ready(function () {
    $('#from_date').change(function () {
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        if(from_date > to_date){
            $('#to_date').val('');
        }
        $('#to_date').attr('min', from_date);
    });

    $('#loan_issue_report_btn').click(function (event) {
        event.preventDefault();
        let from_date=$('#from_date').val();
        let to_date=$('#to_date').val();
        let data = {
            'from_date' : $('#from_date').val(),
            'to_date' : $('#to_date').val()
        };

        if(from_date!='' && to_date!=''){
            serverSideTable('#loan_issue_report_table', data, 'api/report_files/get_loan_issue_report.php');
        }
        else{
            swalError('Please Fill Dates!', 'Both From and To dates are required.');

        }
       


    });

});

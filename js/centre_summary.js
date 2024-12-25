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
    $(document).on('click', '#back_btn', function () {
        getCentreCurrentTable();
        $('#back_btn').hide();
        $('.centre_loan_current').show()
        $('#curr_closed').show()
        $('.ledger_view_chart_model').hide();
    })
    $(document).on('click', '#close_back_btn', function () {
        getCentreCurrentTable();
        $('#close_back_btn').hide();
        $('.centre_loan_closed').show()
        $('#curr_closed').show()
        $('.ledger_view_chart_model').hide();
    })
});
/////////////////// Current Ledger View//////////////////
$(document).on('click', '.ledgerViewBtn', function (event) {
    event.preventDefault();
    $('.ledger_view_chart_model').show();
    $('#back_btn').show();
    $('.centre_loan_current').hide()
    $('#curr_closed').hide()
    let loan_id= $(this).attr('value');
    getLedgerViewChart(loan_id);
});
//////////////////////////Ledger View/////////////////////////////
$(document).on('click', '.closeledgerViewBtn', function (event) {
    event.preventDefault();
    $('.ledger_view_chart_model').show();
    $('#close_back_btn').show();
    $('.centre_loan_closed').hide()
    $('#curr_closed').hide()
    let loan_id= $(this).attr('value');
    getLedgerViewChart(loan_id);
});
//////////////////////////Ledger View/////////////////////////////
//////////////////////////////////////////Due Chart start//////////////////////////////
$(document).on('click','.due-chart', function(){
    var cus_mapping_id = $(this).attr('data-id');
    let loan_id= $('#loan_id').val()
    $('#due_chart_model').modal('show');
    dueChartList(cus_mapping_id,loan_id); // To show Due Chart List.
    setTimeout(()=>{
        $('.print_due_coll').click(function(){
            var id = $(this).attr('value');
            Swal.fire({
                title: 'Print',
                text: 'Do you want to print this collection?',
                imageUrl: 'img/printer.png',
                imageWidth: 300,
                imageHeight: 210,
                imageAlt: 'Custom image',
                showCancelButton: true,
                confirmButtonColor: '#009688',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url:'api/collection_files/print_collection.php',
                        data:{'coll_id':id},
                        type:'post',
                        cache:false,
                        success:function(html){
                            $('#printcollection').html(html)
                            // Get the content of the div element
                            var content = $("#printcollection").html();
                        }
                    })
                }
            })
        })
    },1000)
});
//////////////////////////Due Chart End/////////////////////////////////////////

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
function getLedgerViewChart(loan_id){
    $.post('api/collection_files/ledger_view_data.php', {loan_id:loan_id}, function(response){
        $('#ledger_view_table_div').empty();
        $('#ledger_view_table_div').html(response);

    });
}
function dueChartList(cus_mapping_id,loan_id){
    $.ajax({
        url: 'api/collection_files/get_due_chart_list.php',
        data: {'cus_mapping_id':cus_mapping_id},
        type:'post',
        cache: false,
        success: function(response){
            $('#due_chart_table_div').empty();
            $('#due_chart_table_div').html(response);
        }
    }).then(function(){
      
        $.post('api/collection_files/get_due_method_name.php', { cus_mapping_id: cus_mapping_id}, function(response) {
            $('#dueChartTitle').text('Due Chart ( ' + response['due_month'] + ' - ' + response['loan_type'] + ' ) - Customer ID: ' 
            + response['cus_id'] + ' - Customer Name: ' + response['cus_name'] + ' - Loan ID: ' + response['loan_id'] 
            + ' - Centre ID: ' + response['centre_id'] + ' - Centre Name: ' + response['centre_name']);
        }, 'json');   
    })

}
function closeChartsModal() {
    $('#due_chart_model').modal('hide');
}
$(function () {
  getclosedTable();
});
// document start
$(document).ready(function () {
    // click view button
    $(document).on('click', '.view', function () {
        let id = $(this).attr('value');
        let loan_id = $(this).attr('loan_id');
        $("#closed_content").show();
        $(".closed_customer_table_content").show();
        $(".centre_closed_content").show();
        $(".closed_table_content").hide();
        getCentreDetails(id);
        getCustomerList(loan_id);
    });
// click move  to move the loan to noc
    $(document).on('click', '.move-noc', function () {
        let id = $(this).attr('value');
        let loan_sts = 10;
     moveToNext(id, loan_sts);
       
    });
// click back button
    $(document).on('click', '#back_btn', function () {
        $("#closed_content").hide();
        $(".closed_customer_table_content").hide();
        $(".centre_closed_content").hide();
        $(".closed_table_content").show();
        getclosedTable();


    });
// submit centre close
    $(document).on('click', '#submit_centre_closed', function () {
        if ($("#sub_status").prop("disabled") && $("#centre_remarks").prop("readonly")) {
            submitCustomer();
        } else {
            let loan_id=$("#loan_id").val();
            console.log("loan id"+loan_id);
            let sub_status=$("#sub_status").val();
            let centre_id=$("#centre_id").val();
            let remarks=$("#centre_remarks").val();
            let allValid = true;
            $('#closed_customer_table tbody tr').each(function() {
                let selectBox = $(this).find('.sub_status'); 
                let selectedValue = selectBox.val(); 
                console.log('Row Select Value:', selectedValue); 
                if (selectedValue === "") {
                    allValid = false; 
                    selectBox.addClass('is-invalid');
                } 
            })
            if(allValid && sub_status!=''){
                submitCustomer();
                submitLoanSubStatus(loan_id,sub_status,remarks,centre_id)
            }
            else{
                $('#sub_status').css('border', '1px solid rgb(255, 56, 56)');
            }
            
        }
    });

    $(document).on('click', '.ledgerViewBtn', function (event) {
        event.preventDefault();
        $('.ledger_view_chart_model').show();
        $('#back_to_coll_list').show();
        $('.closed_table_content').hide();
        let loan_id= $(this).attr('value');
        getLedgerViewChart(loan_id);
    });
    $(document).on('click', '#back_to_coll_list', function (event) {
        event.preventDefault();
        $('.closed_table_content').show();
        getclosedTable();
        $('#back_to_coll_list').hide();
        $('.ledger_view_chart_model').hide();
    });
    $(document).on('click','.due-chart', function(){
        var cus_mapping_id = $(this).attr('data-id');
        let loan_id= $('#loan_id').val()
        $('#due_chart_model').modal('show');
        var tbody = $('#due_chart_table_div tbody');
        tbody.empty(); // Clear existing rows
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
    $(document).on('click','.cus_due_chart', function(){
        var cus_mapping_id = $(this).attr('value');
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
    $(document).on('click','.cus_penalty_chart', function(e){
        e.preventDefault(); // Prevent default anchor behavior
        var cus_mapping_id = $(this).attr('value'); // Capture data-id from the clicked element
        $('#penalty_model').modal('show'); // Show the modal
        penaltyChartList(cus_mapping_id); 
    });
    $(document).on('click', '.cus_fine_chart', function (e) {
        e.preventDefault(); // Prevent default anchor behavior
        var cus_mapping_id = $(this).attr('value'); // Capture data-id from the clicked element
        $('#fine_model').modal('show'); // Show the modal
        fineChartList(cus_mapping_id); // Call the function and pass the cus_mapping_id
    });


});
function closeChartsModal() {
    $('#due_chart_model').modal('hide');
    $('#penalty_model').modal('hide');
    $('#fine_model').modal('hide');
}

function dueChartList(cus_mapping_id, loan_id) {
    $.ajax({
        url: 'api/collection_files/get_due_chart_list.php',
        data: { 'cus_mapping_id': cus_mapping_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#due_chart_table_div').empty();
            $('#due_chart_table_div').html(response);
        }
    }).then(function () {
        $.post('api/collection_files/get_due_method_name.php', { loan_id: loan_id, cus_mapping_id: cus_mapping_id }, function (response) {
            $('#dueChartTitle').text('Due Chart ( ' + response['due_month'] + ' - ' + response['loan_type'] + ' ) - Customer ID: '
                + response['cus_id'] + ' - Customer Name: ' + response['cus_name'] + ' - Loan ID: ' + response['loan_id']
                + ' - Centre ID: ' + response['centre_id'] + ' - Centre Name: ' + response['centre_name']);
        }, 'json');
    })

}
//Penalty chart
function penaltyChartList(cus_mapping_id){
    $.ajax({
        url: 'api/collection_files/get_penalty_chart_list.php',
        data: {'cus_mapping_id':cus_mapping_id},
        type:'post',
        cache: false,
        success: function(response){
            $('#penalty_chart_table_div').empty()
            $('#penalty_chart_table_div').html(response)
        }
    });//Ajax End.
}
//Fine Chart List
function fineChartList(cus_mapping_id){
    $.ajax({
        url: 'api/collection_files/get_fine_chart_list.php',
        data: {'cus_mapping_id':cus_mapping_id},
        type:'post',
        cache: false,
        success: function(response){
            $('#fine_chart_table_div').empty()
            $('#fine_chart_table_div').html(response)
        }
    });//Ajax End.
}

function getLedgerViewChart(loan_id){
    $.post('api/collection_files/ledger_view_data.php', {loan_id:loan_id}, function(response){
        $('#ledger_view_table_div').empty();
        $('#ledger_view_table_div').html(response);

    });
}




// function start
function getclosedTable() {
  serverSideTable("#closed_table", "", "api/closed_files/closed_list.php");
}

function getCentreDetails(id){
        $.post('api/closed_files/get_centre_details.php', { id }, function (response) {
            if (response.length > 0) {
                $('#loan_id').val(response[0].loan_id);
                $('#centre_id').val(response[0].centre_id);
                $('#centre_no').val(response[0].centre_no);
                $('#centre_name').val(response[0].centre_name);
                $('#mobile').val(response[0].mobile1);
                $('#area').val(response[0].areaname);
                $('#branch').val(response[0].branch_name);
                $('#loan_date').val(response[0].due_start);
                $('#closed_date').val(response[0].due_end);
                $('#loan_amount').val(response[0].loan_amount);
            }
        }, 'json');
    

}

function getCustomerList(loan_id){
    $.ajax({
        url: 'api/closed_files/get_customer_list.php',
        type: 'POST',
        dataType: 'json',
        data: {
            loan_id: loan_id,
        },
        success: function (response) {
            var tbody = $('#closed_customer_table tbody');
            tbody.empty();
            var hasRows = false;
            $.each(response, function (index, item) {
             // Check if status is 'Closed'
             if (item.status === 'Closed') {
                sub_status = `
                    <select class="form-control sub_status">
                        <option value="" >Select Sub Status</option>
                        <option value="1"${item.closed_sub_status == 1 ? 'selected' : ''} >Consider</option>
                        <option value="2" ${item.closed_sub_status == 2 ? 'selected' : ''}>Blocked</option>
                    </select>
                `;
                textarea = `
                    <textarea class="form-control textarea" placeholder="Enter remarks">${item.closed_remarks ? item.closed_remarks : ''}</textarea>
                `;
            } else {
                sub_status = `
                    <select class="form-control sub_status" disabled>
                        <option value="" >Select Sub Status</option>
                        <option value="1" >Consider</option>
                        <option value="2">Blocked</option>
                    </select>
                `;
                textarea = `
                    <textarea class="form-control textarea" placeholder="Enter remarks" readonly></textarea>
                `;
            }
                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td class="hidden-id">' + item.id + '</td>' +
                    '<td class="cus_id">' + item.cus_id + '</td>' +
                    '<td>' + item.first_name + '</td>' +
                    '<td class="status-column">' + item.status + '</td>' +
                    '<td >' + sub_status + '</td>' +
                    '<td >' + textarea + '</td>' + 
                    '<td>' + item.chart + '</td>' + 
                    '</tr>';
                tbody.append(row);
                hasRows = true;
                $('.hidden-id').css('display', 'none');
            });

            setDropdownScripts(); 
            let allClosed = true; 
            $('#closed_customer_table tbody tr').each(function() {
                // Get the text content of the specific <td> by class
                let status = $(this).find(`td.status-column`).text().trim();
                console.log('Column content:'+ status); // Debugging output
            
                if (status != 'Closed') { // Check if it's "Closed"
                    allClosed = false; 
                }
                
                if (allClosed) {
                    $('#sub_status').prop('disabled', false);
                    $('#centre_remarks').prop('readonly', false);
                }
                
            });
        }
    })
}

function submitLoanSubStatus(loan_id,sub_status,remarks,centre_id){
    $.ajax({
        url: 'api/closed_files/submit_loan_status.php',
        type: 'POST',
        dataType: 'json',
        data: {
            loan_id: loan_id,sub_status:sub_status,remarks:remarks,centre_id:centre_id
        },
        success: function (response) {
            if(response==="0"){
                swalSuccess('Success', 'Loan Closed and Moved to NOC');
                $("#closed_content").hide();
        $(".closed_customer_table_content").hide();
        $(".centre_closed_content").hide();
        $(".closed_table_content").show();
        getclosedTable();
            }
            else if(response==="1"){
                swalSuccess('Success', 'Loan Status Inserted ');
            }
            else if(response==="2"){
                swalError('Warning', 'Query Error');
            }
        }
    });
}

function submitCustomer(){
    var dataToSave = [];
    $('#closed_customer_table tbody tr').each(function () {
        var row = $(this);
        var cus_id = row.find('.hidden-id').text();
        var sub_status = row.find('.sub_status').val(); 
        var remarks = row.find('.textarea').val();
        if (sub_status !="") {
            dataToSave.push({
                cus_id: cus_id,
                sub_status: sub_status,
                remarks: remarks
            });
        }
    });
    if (dataToSave.length > 0) {
        $.ajax({
            url: 'api/closed_files/update_customer_status.php',
            type: 'POST',
            dataType: 'json',
            data: { customers: dataToSave },  
            success: function (response) {
                if (response.success) {
                    alert('Customer Sub Status Updated');
                } else {
                    alert('Failed to save data: ' + response.message);
                }
            },
            error: function () {
                alert('An error occurred while saving data.');
            }
        });
    } else {
        alert('Please Entre Sub Status');
    }

}

function moveToNext(loan_id, loan_sts) {
    $.post('api/common_files/move_to_next.php', { loan_id, loan_sts }, function (response) {
        if (response == '0') {
            let alertName = 'Closed Successfully';
            swalSuccess('Success', alertName);
            getclosedTable()
        } else {
            swalError('Alert', 'Failed To Move');
        }
    }, 'json');
}

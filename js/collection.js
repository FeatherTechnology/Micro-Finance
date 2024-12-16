$(document).ready(function () {
    $(document).on('click', '#back_to_coll_list', function (event) {
        event.preventDefault();
        $('#pageHeaderName').text(` - Collection`);
        $('#collection_list').show();
        getCollectionListTable();
        $('#back_to_coll_list ,#coll_main_container').hide();

    });

    ///////////////////////////////////////////////Pay Due Start////////////////////////////////////
    $(document).on('click', '.pay-due', function (event) {
        event.preventDefault();

        // Hide and show the appropriate sections
        $('#collection_list').hide();
        $('#back_to_coll_list ,#coll_main_container').show();

        let dataValue = $(this).data('value');
        let dataParts = dataValue.split('_');
        let loan_id = dataParts[0];
        let centre_id = dataParts[1];
        let centre_name = dataParts[2];
        $('#pageHeaderName').text(` - Collection - Loan ID: ${loan_id}, Centre ID: ${centre_id}, Centre Name: ${centre_name}`);
    });
/////////////////////////////////////////////////Pay Due End////////////////////////////////////////////////
//////////////////////////////////////////////Fine Start ////////////////////////////////////////////
$(document).on('click', '.pay-fine', function(e) {
    let loan_id = $(this).attr('value');
    $('#fine_loan_id').val(loan_id);
    $('#fine_form_modal').modal('show');
    collectDate();
    getCustomerList(loan_id)
    getFineFormTable(loan_id)

});
$('#fine_form_submit').click(function(event){
    event.preventDefault();
    let fine_loan_id = $('#fine_loan_id').val();
    let cus_id = $('#add_customer').val();
    let fine_date = $('#fine_date').val();
    let fine_purpose = $('#fine_purpose').val();
    let fine_Amnt = $('#fine_Amnt').val();
    var data = [ 'fine_loan_id','add_customer','fine_date','fine_purpose','fine_Amnt']
    
    var isValid = true;
    data.forEach(function (entry) {
        var fieldIsValid = validateField($('#'+entry).val(), entry);
        if (!fieldIsValid) {
            isValid = false;
        }
    });

    if(isValid){
        $.post('api/collection_files/submit_fine_form.php', {fine_loan_id, cus_id, fine_date, fine_purpose, fine_Amnt}, function(response){
            if(response == '1'){
                swalSuccess('Success', 'Fine Added Successfully.');
                getFineFormTable(fine_loan_id);
            }else{
                swalError('Error', 'Failed to Add Fine');
            }
        },'json');
    }
})



//////////////////////////////////////////////Fine End//////////////////////////////////////////////

});


$(function () {
    getCollectionListTable();
});

function getCollectionListTable() {
    serverSideTable('#collection_list_table', '', 'api/collection_files/collection_list.php');
}
function closeFineChartModal(){
    $('#fine_form_modal').modal('hide');
    let cus_id = $('#cus_id').val();
    //OnLoadFunctions(cus_id);
}
function getCustomerList(loan_id) {
    $.post('api/collection_files/get_customer_list.php', { loan_id :loan_id }, function (response) {
        let cusOptn = '';
        cusOptn = '<option value="">Select Customer Name</option>';
        response.forEach(val => {
            cusOptn += '<option value="' + val.id + '">' + val.first_name + ' - ' + val.mobile1 + ' - ' + val.areaname + '</option>';
        });
        $('#add_customer').empty().append(cusOptn);
    }, 'json');
}
function collectDate() {
    var today = new Date();
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0
    var year = today.getFullYear();

    var currentDate = day + '-' + month + '-' + year;
    $('#fine_date').val(currentDate);
}
function getFineFormTable(loan_id){
    $.post('api/collection_files/fine_form_list.php', {loan_id}, function(response){
        let fineColumn =[
            'sno',
            'first_name',
            'coll_date',
            'coll_purpose',
            'coll_charge'
        ];
        appendDataToTable('#fine_form_table', response, fineColumn);
        setdtable('#fine_form_table');

        $('#fine_purpose').val('');
        $('#fine_Amnt').val('');
        $('#add_customer').val('');
        $('#fine_purpose').css('border', '1px solid #cecece');
        $('#fine_Amnt').css('border', '1px solid #cecece');
        $('#add_customer').css('border', '1px solid #cecece');
    },'json');
}

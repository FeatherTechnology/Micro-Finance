$(document).ready(function () {
    $(document).on('click', '.addcompanyBtn, .backBtn', function () {
        swapTableAndCreation();
    });
    $('#document').change(function () {
        let document = $('#document')[0];
        let img = $('#imgshow');
        img.attr('src', URL.createObjectURL(document.files[0]));
        checkInputFileSize(this, 200, img)
    })
    $('#state').change(function () {
        getDistrictList($(this).val());
    });

    $('#district').change(function () {
        getTalukList($(this).val());
    });
    // submit Document creation
    $(document).on('click','#submit_document',function(e){
        e.preventDefault();
        let document_name = $('#doc_name').val();
        let document = $('#document')[0].files[0];
        let per_pic = $('#per_pic').val();
        var isValid = true;
        if(document_name===""){
            isValid = false;
            $('#doc_name').css('border', '1px solid #ff0000');
        }
        else{
            $('#doc_name').css('border', '1px solid #cecece');

        }
        if (document === undefined && per_pic === '' ) {
            let isUploadValid = validateField('', 'document');
            let isHiddenValid = validateField('', 'per_pic');
            if (!isUploadValid || !isHiddenValid ) {
                isValid = false;
            }
            else {
                $('#document').css('border', '1px solid #cecece');
                $('#per_pic').css('border', '1px solid #cecece');
            }
        }
        else {
            $('#document').css('border', '1px solid #cecece');
            $('#per_pic').css('border', '1px solid #cecece');
        }
        if (isValid) {
            var formData = new FormData(); 
            formData.append('document_name', document_name);
            formData.append('document', document);
            formData.append('per_pic', per_pic);
       
        
        $.ajax({
            url: 'api/company_creation_files/submit_company_document.php', 
            type: 'POST',
            data: formData,
            processData: false,  
            contentType: false,
            dataType: 'json',
            cache: false,  
            success: function(response) {
                if(response===1){
                    getDocNeedTable();
                    $('#doc_name').val(""); 
                    $('#document').val("");
                    $('#per_pic').val("");
                }
                else{
                    alert("error");
                }
                
            },
        });
    }


    });
    // delet document from list 
    $(document).on('click', '.docNeedDeleteBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/company_creation_files/delet_document.php', { id }, function (response) {
            if (response == '0') {
                getDocNeedTable();
            } 
        }, 'json');
    });

  

    $('#submit_company_creation').click(function () {
        event.preventDefault();
        //Validation
        let company_name = $('#company_name').val(); let address = $('#address').val(); let state = $('#state').val(); let district = $('#district').val(); let taluk = $('#taluk').val(); let place = $('#place').val(); let pincode = $('#pincode').val(); let website = $('#website').val(); let mailid = $('#mailid').val(); let mobile = $('#mobile').val(); let whatsapp = $('#whatsapp').val(); let landline_code = $('#landline_code').val(); let landline = $('#landline').val(); let companyid = $('#companyid').val();
        let pan =$("#pan").val(); let tan =$("#tan").val(); let tin =$("#tin").val(); let cin =$("#cin").val(); let License_No =$("#License_No").val(); let gst =$("#gst").val();
        var data = ['company_name', 'address', 'state','place', 'district', 'taluk', 'pincode']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            /////////////////////////// submit page AJAX /////////////////////////////////////
            $.post('api/company_creation_files/submit_company_creation.php', { company_name, address, state, district, taluk, place, pincode, website, mailid, mobile, whatsapp, landline_code, landline, companyid,pan,tan,tin,cin,License_No,gst }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Company Added Successfully!');
                } else {
                    swalSuccess('Success', 'Company Updated Successfully!')
                }

                $('#company_creation').trigger('reset');
                getCompanyTable();
                swapTableAndCreation();//to change to div to table content.

            });
            /////////////////////////// submit page AJAX END/////////////////////////////////////
        }
    });

    ///////////////////////////////////// EDIT Screen START   /////////////////////////////////////
    $(document).on('click', '.companyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/company_creation_files/get_company_creation_data.php', { id }, function (response) {
            $('#companyid').val(id);
            $('#company_name').val(response[0].company_name);
            $('#address').val(response[0].address);
            $('#state').val(response[0].state);

            getDistrictList(response[0].state)
            getTalukList(response[0].district)

            $('#place').val(response[0].place);
            $('#pincode').val(response[0].pincode);
            $('#website').val(response[0].website);
            $('#mailid').val(response[0].mailid);
            $('#mobile').val(response[0].mobile);
            $('#whatsapp').val(response[0].whatsapp);
            $('#landline_code').val(response[0].landline_code);
            $('#landline').val(response[0].landline);
            $('#pan').val(response[0].pan);
            $('#tan').val(response[0].tan);
            $('#tin').val(response[0].tin);
            $('#cin').val(response[0].cin);
            $('#License_No').val(response[0].License_No);
            $('#gst').val(response[0].gst);

            setTimeout(() => {
                $('#district').val(response[0].district);
                $('#taluk').val(response[0].taluk);
            }, 1000);

            swapTableAndCreation();//to change to div to table content.
        }, 'json');
    })
    ///////////////////////////////////// EDIT Screen END  /////////////////////////////////////

    $('#mobile, #whatsapp').change(function () {       
        checkMobileNo($(this).val(), $(this).attr('id'));
    });

    $('#landline').change(function () {       
        checkLandlineFormat($(this).val(), $(this).attr('id'));
    });
    
    $('#mailid').on('change', function () {
        validateEmail($(this).val(), $(this).attr('id'));
    });

});//Document END.

//OnLoad/////
$(function () {
    
    getCompanyTable();
    getDocNeedTable();
    getStateList();
});

function getDocNeedTable() {
    $.post('api/company_creation_files/document_list.php', function (response) {
        let loanCategoryColumn = [
            "sno",
            "document_name",
            "upload",
            "action"
        ]
        appendDataToTable('#documents_table', response, loanCategoryColumn);
        // setdtable('#documents_table');
    }, 'json');
}

function getCompanyTable() {
    $.post('api/company_creation_files/company_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'company_name',
            'place',
            'district_name',
            'mobile',
            'action'
        ];
        appendDataToTable('#company_creation_table', response, columnMapping);
        setdtable('#company_creation_table');

        if (response.length > 0) {
            $('#add_company').hide();
        } else {
            $('#add_company').show();
        }
    }, 'json')
}

function swapTableAndCreation() {

    if ($('.company_table_content').is(':visible')) {
        $('.company_table_content').hide();
        $('.addcompanyBtn').hide();
        $('#company_creation_content').show();
        $('.backBtn').show();
    } else {
        $('.company_table_content').show();
        $('#company_creation_content').hide();
        $('.backBtn').hide();
    }
}

function getStateList() {
    $.post('api/common_files/get_state_list.php', function (response) {
        let appendStateOption = '';
        appendStateOption += "<option value=''>Select State</option>";
        $.each(response, function (index, val) {
            appendStateOption += "<option value='" + val.id + "'>" + val.state_name + "</option>";
        });
        $('#state').empty().append(appendStateOption);
    }, 'json');
}

function getDistrictList(state_id) {
    $.post('api/common_files/get_district_list.php', { state_id }, function (response) {
        let appendDistrictOption = '';
        appendDistrictOption += "<option value=''>Select District</option>";
        $.each(response, function (index, val) {
            appendDistrictOption += "<option value='" + val.id + "'>" + val.district_name + "</option>";
        });
        $('#district').empty().append(appendDistrictOption);
    }, 'json');
}

function getTalukList(district_id) {
    $.post('api/common_files/get_taluk_list.php', { district_id }, function (response) {
        let appendTalukOption = '';
        appendTalukOption += "<option value=''>Select Taluk</option>";
        $.each(response, function (index, val) {
            appendTalukOption += "<option value='" + val.id + "'>" + val.taluk_name + "</option>";
        });
        $('#taluk').empty().append(appendTalukOption);
    }, 'json');
}
$('button[type="reset"],  .backBtn').click(function () {
    event.preventDefault();
    $('#company_creation input').css('border', '1px solid #cecece');
    $('#company_creation select').css('border', '1px solid #cecece');
});
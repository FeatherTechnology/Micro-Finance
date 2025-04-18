$(document).ready(function () {
    $('#bk_submit').click(function (event) {
        event.preventDefault(); // Prevent the default form submission
        $(this).attr('disabled', true);
        let upload_files =$('#upload_files').val();
        let fileValidation = $('#upload_btn').val();
        let isValid = true;
        // Validate the upload_btn field
        if (!validateField(upload_files, 'upload_files')) {
            isValid = false;
        }
        if (!validateField(fileValidation, 'upload_btn')) {
            isValid = false;
        }
        // If the validation passes, proceed with uploading the Excel file to the database
        if (isValid) {
            if (upload_files == 1){
                uploadGCExcelToDB();
            }
            else if(upload_files == 2){
                uploadCenExcelToDB()
           }
            else if(upload_files == 3){
                 uploadCCExcelToDB()
            }
            else {
                uploadAuctionExcelToDB()
           }
           
        }
    });
});
function uploadGCExcelToDB() {
    let excelFile = $('#upload_btn')[0].files[0];
    let formData = new FormData();
    formData.append('excelFile', excelFile);

    $.ajax({
        url: 'api/bulk_upload_files/customer_centre_upload.php',
        data: formData,
        type: 'POST',
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.includes('Bulk Upload Completed')) {
                swalSuccess('Success', 'Uploaded Successfully');
                $('#upload_btn').val('');
            } else if (response.includes('Insertion completed till Serial No')) {
                Swal.fire({
                    html: response,
                    icon: 'error',
                    showConfirmButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                });
            } else if (response.includes('File is not in Excel Format')) {
                swalError('Alert', 'The uploaded file is not in the correct format.');
            }
        }, 
       
        complete: function () {
            $('#bk_submit').attr('disabled', false); 
            $('#upload_btn').val('');
        }
       
    });
}
function uploadCenExcelToDB() {
    let excelFile = $('#upload_btn')[0].files[0];
    let formData = new FormData();
    formData.append('excelFile', excelFile);

    $.ajax({
        url: 'api/bulk_upload_files/centre_creation_upload.php',
        data: formData,
        type: 'POST',
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.includes('Bulk Upload Completed')) {
                swalSuccess('Success', 'Uploaded Successfully');
                $('#upload_btn').val('');
            } else if (response.includes('Insertion completed till Serial No')) {
                Swal.fire({
                    html: response,
                    icon: 'error',
                    showConfirmButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                });
            } else if (response.includes('File is not in Excel Format')) {
                swalError('Alert', 'The uploaded file is not in the correct format.');
            }
        },
        complete: function () {
            $('#bk_submit').attr('disabled', false); 
            $('#upload_btn').val('');
        }
    });
}
function uploadCCExcelToDB() {
    let excelFile = $('#upload_btn')[0].files[0];
    let formData = new FormData();
    formData.append('excelFile', excelFile);

    $.ajax({
        url: 'api/bulk_upload_files/loan_entry_upload.php',
        data: formData,
        type: 'POST',
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.includes('Bulk Upload Completed')) {
                swalSuccess('Success', 'Uploaded Successfully');
                $('#upload_btn').val('');
            } else if (response.includes('Insertion completed till Serial No')) {
                Swal.fire({
                    html: response,
                    icon: 'error',
                    showConfirmButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                });
            } else if (response.includes('File is not in Excel Format')) {
                swalError('Alert', 'The uploaded file is not in the correct format.');
            }
        },
        complete: function () {
            $('#bk_submit').attr('disabled', false); 
            $('#upload_btn').val('');
        }
    });
}
function uploadAuctionExcelToDB() {
    let excelFile = $('#upload_btn')[0].files[0];
    let formData = new FormData();
    formData.append('excelFile', excelFile);

    $.ajax({
        url: 'api/bulk_upload_files/loan_issue_upload.php',
        data: formData,
        type: 'POST',
        cache: false,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.includes('Bulk Upload Completed')) {
                swalSuccess('Success', 'Uploaded Successfully');
                $('#upload_btn').val('');
            } else if (response.includes('Insertion completed till Serial No')) {
                Swal.fire({
                    html: response,
                    icon: 'error',
                    showConfirmButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                });
            } else if (response.includes('File is not in Excel Format')) {
                swalError('Alert', 'The uploaded file is not in the correct format.');
            }
        },
        complete: function () {
            $('#bk_submit').attr('disabled', false); 
            $('#upload_btn').val('');
        }
    });
}

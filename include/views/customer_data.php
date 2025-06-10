<div id="customer_data_content">
    <div class="card customer_table_content">
        <div class="card-header">
            <div class="card-title">Customer List</div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <table id="customer_list_table" class="table custom-table">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                            <th>Mobile</th>
                            <th>Area</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="back_btn" style="display:none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <br>
    <div class="radio-container" style="display: none;">

        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="customer_info" name="customer_data" class="selector-item_radio" value="customer_info" checked>
                <label for="customer_info" class="selector-item_label">Customer Info</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="centre_summary" name="customer_data" class="selector-item_radio" value="centre_summary">
                <label for="centre_summary" class="selector-item_label">Centre Summary</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="documentation" name="customer_data" class="selector-item_radio" value="documentation">
                <label for="documentation" class="selector-item_label">Documentation </label>
            </div>
        </div>
    </div>
    <br>

    <!--Customer List Start-->
    <div class="customer_info_table_content" style="display:none">
        <form id="customer_creation" name="customer_creation">
            <input type="hidden" id="customer_profile_id">
            <div class="row gutters">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Customer Info</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_id"> Customer ID</label>
                                                <input type="text" class="form-control" id="cus_id" name="cus_id" placeholder="Enter Customer ID" tabindex="1" readonly>
                                                <input type="hidden" id="cus_id_upd" name="cus_id_upd">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="aadhar_number">Aadhar No</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" name="aadhar_number" id="aadhar_number" tabindex="2" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                                <input type="hidden" id="addaadhar_id" value='0'>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_data"> Customer Data</label>
                                                <input type="text" class="form-control" id="cus_data" name="cus_data" disabled tabindex="3">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 cus_status_div" style="display:none;">
                                            <div class="form-group">
                                                <label for="cus_status"> Customer Status</label>
                                                <input type="text" class="form-control" id="cus_status" name="cus_status" disabled tabindex="4">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="first_name">First Name</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control " id="first_name" name="first_name" placeholder="Enter First name" tabindex="5">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="last_name">Last Name</label>
                                                <input type="last_name" class="form-control" id="last_name" name="last_name" placeholder="Enter Last name" tabindex="6">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="dob"> DOB</label>
                                                <input type="date" class="form-control" id="dob" name="dob" placeholder="Enter Date Of Birth" tabindex="7">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="age"> Age</label>
                                                <input type="number" class="form-control" id="age" name="age" readonly placeholder="Age" tabindex="8">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="area">Area</label><span class="text-danger">*</span>
                                                <input type="hidden" id="area_edit">
                                                <select type="text" class="form-control" id="area" name="area" tabindex="9">
                                                    <option value="">Select Area</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="mobile1"> Mobile Number 1</label><span class="text-danger">*</span>
                                                <input type="number" class="form-control" id="mobile1" name="mobile1" placeholder="Enter Mobile Number 1" onKeyPress="if(this.value.length==10) return false;" tabindex="10">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="mobile2"> Mobile Number 2</label>
                                                <input type="number" class="form-control" id="mobile2" name="mobile2" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile Number 2" tabindex="11">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label>Choose Mobile Number for WhatsApp:</label><br>
                                                <label>
                                                    <input type="radio" name="mobile_whatsapp" value="mobile1" id="mobile1_radio">
                                                    Mobile Number 1
                                                </label><br>
                                                <label>
                                                    <input type="radio" name="mobile_whatsapp" value="mobile2" id="mobile2_radio">
                                                    Mobile Number 2
                                                </label>
                                                <input type="hidden" id="selected_mobile_radio">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="whatsapp"> WhatsApp Number </label>
                                                <input type="number" class="form-control" id="whatsapp" name="whatsapp" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter WhatsApp Number" tabindex="10">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="occupation">Occupation</label>
                                                <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Enter Occupation" tabindex="11">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="occ_detail">Occupation Detail</label>
                                                <input type="text" class="form-control" id="occ_detail" name="occ_detail" placeholder="Enter Occupation Detail" tabindex="12">
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="address"> Address </label>
                                                <textarea class="form-control" name="address" id="address" placeholder="Enter Address" tabindex="13"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="native_address"> Native Address </label>
                                                <textarea class="form-control" name="native_address" id="native_address" placeholder="Enter Native Address" tabindex="14"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="multiple_loan">Muliple Loan Entry</label><span class="text-danger">*</span>
                                                <select type="text" class="form-control" id="multiple_loan" name="multiple_loan" tabindex="15">
                                                    <option value="">Select Multiple Loan Entry</option>
                                                    <option value="1">Enable</option>
                                                    <option value="0">Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="row">
                                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-12">
                                                <div class="form-group">
                                                    <label for="latlong">Location</label>
                                                    <input type="text" class="form-control" name="latlong" id="latlong" placeholder="Enter Latitude Longitude">
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12 ">
                                                <div class="form-group">
                                                    <label style="visibility:hidden">Location</label>
                                                    <button class="btn btn-primary" id="getlatlong" name="getlatlong" style="padding: 5px 35px;"><span class="icon-my_location"></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="pic"> Photo</label><br>
                                                <img id='imgshow' class="img_show" src='img\avatar.png' />
                                                <input type="file" class="form-control  personal_info_disble" id="pic" name="pic" tabindex="16">
                                                <input type="hidden" class="personal_info_disble" id="per_pic">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Family Info
                                <button type="button" class="btn btn-primary" id="add_group" name="add_group" data-toggle="modal" data-target="#add_fam_info_modal" onclick="getFamilyTable()" style="padding: 5px 35px; float: right;" tabindex='17'><span class="icon-add"></span></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <table id="fam_info_table" class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.NO</th>
                                                    <th>Name</th>
                                                    <th>Relationship</th>
                                                    <th>Age</th>
                                                    <th>Occupation</th>
                                                    <th>Aadhar No</th>
                                                    <th>Mobile No</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                <div class="card">
                    <div class="card-header"> KYC info
                        <button type="button" class="btn btn-primary" id="kyc_add" name="kyc_add" data-toggle="modal" data-target=".addkyc" style="padding: 5px 35px; float: right; " tabindex='50'><span class="icon-add"></span></button>
                    </div>
                    <span class="text-danger" style='display:none' id='kyc_infoCheck'>Please Fill KYC Info </span>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group table-responsive" id="kycListTable">
                                    <table class="table custom-table modalTable_list">
                                        <thead>
                                            <tr>
                                                <th width="50"> S.No </th>
                                                <th> Lable </th>
                                                <th> Details </th>
                                                <th> Upload </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
                <div class="col-md-12 ">
                    <div class="text-right">

                        <button type="submit" name="submit_cus_creation" id="submit_cus_creation" class="btn btn-primary" value="Submit" tabindex="18"><span class="icon-check"></span>&nbsp;Submit</button>
                        <button type="reset" class="btn btn-outline-secondary" tabindex="19">Clear</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <!--customer List End-->
    <!--centre List Start-->
    <br>
    <br>
    <div class="card centre_table_content" style="display: none;">
        <br>
        <br>

        <div class="radio-container" id="curr_closed">
            <div class="selector">
                <div class="selector-item">
                    <input type="radio" id="centre_current" name="customer_data_type" class="selector-item_radio" value="cen_current" checked>
                    <label for="centre_current" class="selector-item_label">Current</label>
                </div>
                <div class="selector-item">
                    <input type="radio" id="centre_closed" name="customer_data_type" class="selector-item_radio" value="cen_closed">
                    <label for="centre_closed" class="selector-item_label">Closed</label>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="col-12">
            <div class="centre_loan_current">

                <div class="card-header">
                    <div class="card-title">Current Centre List </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table id="centre_current_table" class="table custom-table">
                                <thead>
                                    <tr>
                                        <th width="50">S.No.</th>
                                        <th>Centre ID</th>
                                        <th>Centre Number</th>
                                        <th>Centre Name</th>
                                        <th>Loan ID</th>
                                        <th>Loan Amount</th>
                                        <th>Centre Status</th>
                                        <th>Collection Status</th>
                                        <th>Customer Status</th>
                                        <th>Charts</th>
                                    </tr>
                                </thead>
                                <tbody> </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--------------Centre table end-------------------------->
        <div class="col-12">
            <div class="centre_loan_closed" style="display:none">
                <div class="card-header">
                    <div class="card-title">Closed Centre List </div>

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table id="centre_close_table" class=" table custom-table">
                                <thead>
                                    <th width="50">S.No.</th>
                                    <th>Centre ID</th>
                                    <th>Centre Number</th>
                                    <th>Centre Name</th>
                                    <th>Loan ID</th>
                                    <th>Loan Amount</th>
                                    <th>Centre Status</th>
                                    <th>Collection Status</th>
                                    <th>Customer Status</th>
                                    <th>Charts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Existing List End-->
    <!--document List Start-->
    <div class="card document_table_content" style="display: none;">
        <div class="card-header">
            <div class="card-title">Document List </div>
        </div>
        <div class="card-body">
            <div class="col-12">
                <table id="document_list_table" class="table custom-table">
                    <thead>
                        <tr>
                            <th width="20">S.NO</th>
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                            <th>Loan ID</th>
                            <th>Centre ID</th>
                            <th>Centre Name</th>
                            <th>Centre Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    <form id="documentation_form" name="documentation_form" style="display:none">
        <input type="hidden" id="loan_id">
        <div class="row gutters">
            <div class="col-12">
                <div class="text-right">
                    <button type="button" class="btn btn-primary" id="cancel_btn" >&nbsp; Cancel</button>
                </div>
                <br>

                <!--- -------------------------------------- Document Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Document Info
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_id"> Document ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="doc_id" name="doc_id" tabindex="1" value="D" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="customer_name"> Customer Name</label><span class="text-danger">*</span>
                                    <input type="hidden" id="customer_name_edit">
                                    <select class="form-control" id="customer_name" name="customer_name" tabindex="2">
                                        <option value="">Select Customer Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_name"> Document Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="doc_name" name="doc_name" tabindex="3" placeholder="Enter Document Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_type">Document Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="doc_type" id="doc_type" tabindex="4">
                                        <option value="">Select Document Type</option>
                                        <option value="1">Original</option>
                                        <option value="2">Xerox</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_count">Count</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="doc_count" name="doc_count" tabindex="5" placeholder="Enter Count">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark" tabindex="6"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_upload">Upload</label>
                                    <input type="file" class="form-control" name="doc_upload" id="doc_upload" tabindex="7">
                                    <input type="hidden" name="doc_upload_edit" id="doc_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_doc_info" id="submit_doc_info" class="btn btn-primary" tabindex="8" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="document_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Document ID</th>
                                                <th>Customer ID</th>
                                                <th>Customer Name</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Count</th>
                                                <th>Remark</th>
                                                <th>Upload</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Document Info END ------------------------------- -->
            </div>
        </div>
    </form>

    <!--document List End-->





</div>
<!--Family Info Modal-->
<div class="modal fade" id="add_fam_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Family Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getFamilyInfoTable();" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="family_form">
                        <div class="row">
                            <input type="hidden" name="family_id" id='family_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_name">Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="fam_name" id="fam_name" tabindex="1" placeholder="Enter Name">
                                    <input type="hidden" id="addfam_name_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_relationship">Relationship</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="fam_relationship" name="fam_relationship" tabindex="1">
                                        <option value=""> Select Relationship </option>
                                        <option value="Father"> Father </option>
                                        <option value="Mother"> Mother </option>
                                        <option value="Spouse"> Spouse </option>
                                        <option value="Son"> Son </option>
                                        <option value="Daughter"> Daughter </option>
                                        <option value="Brother"> Brother </option>
                                        <option value="Sister"> Sister </option>
                                        <option value="Other"> Other </option>
                                    </select>
                                    <input type="hidden" id="addrelationship_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_age">Age</label>
                                    <input type="number" class="form-control" name="fam_age" id="fam_age" tabindex="1" placeholder="Enter Age">
                                    <input type="hidden" id="addage_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_occupation">Occupation</label>
                                    <input class="form-control" name="fam_occupation" id="fam_occupation" tabindex="1" placeholder="Enter Occupation">
                                    <input type="hidden" id="addoccupation_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_aadhar">Aadhar No</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="fam_aadhar" id="fam_aadhar" tabindex="1" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                    <input type="hidden" id="addaadhar_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_mobile">Mobile No</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="fam_mobile" id="fam_mobile" onKeyPress="if(this.value.length==10) return false;" tabindex="1" placeholder="Enter Mobile Number">
                                    <input type="hidden" id="addmobile_id" value='0'>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_family" id="submit_family" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_fam_form" class="btn btn-outline-secondary" tabindex="">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="family_creation_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="10">S.No.</th>
                                    <th>Name</th>
                                    <th>Relationship</th>
                                    <th>Age</th>
                                    <th>Occupation</th>
                                    <th>Aadhar No</th>
                                    <th>Mobile No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getFamilyInfoTable()">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="printcollection" style="display: none"></div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade bd-example-modal-lg" id="due_chart_model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document" style="max-width: 70% !important">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="dueChartTitle">Due Chart</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid" id="due_chart_table_div">
                    <table class="table custom-table">
                        <thead>
                            <th>Due No.</th>
                            <th>Month</th>
                            <th>Date</th>
                            <th>Due Amount</th>
                            <th>Pending</th>
                            <th>Payable</th>
                            <th>Collection Date</th>
                            <th>Collection Amount</th>
                            <th>Balance Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal END ////////////////////////////////////////////////////////////////////// -->
 <!-- Add KYC info Modal  START -->
<div class="modal fade addkyc" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Add KYC Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="resetkycinfo()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="kyc_lable"> Label </label> <span class="required">&nbsp;*</span>
                            <input type="text" class="form-control" id="kyc_lable" name="kyc_lable" placeholder="Enter Lable" tabindex='1'>
                            <span class="text-danger" id="lableCheck" style="display:none"> Enter Lable </span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="kyc_details"> Details </label> <span class="required">&nbsp;*</span>
                            <input type="text" class="form-control" id="kyc_details" name="kyc_details" placeholder="Enter Details" tabindex='2'>
                            <span class="text-danger" id="detailCheck" style="display:none"> Enter Details </span>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="upload"> Upload </label><span class="required">&nbsp;*</span>
                            <input type="hidden" id="upload_files">
                            <input type="file" class="form-control" id="upload" name="upload" accept=".pdf,.jpg,.png,.jpeg" tabindex='1'>
                            <span class="text-danger" id="proofUploadCheck" style="display:none"> Upload </span>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-4 col-12">
                        <input type="hidden" name="kycID" id="kycID">
                        <button type="button" name="kycInfoBtn" id="kycInfoBtn" class="btn btn-primary" style="margin-top: 19px;" tabindex='1'>Submit</button>
                    </div>

                </div>
                </br>

                <div id="kycTable">
                    <table class="table custom-table modalTable">
                        <thead>
                            <tr>
                                <th width="50"> S.No </th>
                                <th> Label </th>
                                <th> Details </th>
                                <th> Uploads </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetkycinfo()" tabindex='1'>Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////// Savings Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="Savings_chart_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Savings Chart</h5>
                <button type="button" class="close savings_chart_back" data-dismiss="modal" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body overflow-x-cls" id="savings_chart_table_div">
                <table class="table custom-table">
                    <thead>
                        <th>S No.</th>
                        <th>Date</th>
                        <th>Credit / Debit</th>
                        <th>Savings</th>
                        <th>Total Savings</th>
                    </thead>
                </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary savings_chart_back" data-dismiss="modal" tabindex="2">Back</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Savings Chart Modal END ////////////////////////////////////////////////////////////////////// -->
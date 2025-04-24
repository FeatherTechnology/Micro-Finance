<div class="text-right">
    <button type="button" class="btn btn-primary " id="add_centre"><span class="fa fa-plus"></span>&nbsp; Add Centre</button>
    <button type="button" class="btn btn-primary" id="back_btn" style="display:none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
</div>
<br>
<div class="card centre_table_content">
    <div class="card-body">
        <div class="col-12">

            <table id="centre_create" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Centre ID</th>
                        <th>Centre No</th>
                        <th>Centre Name</th>
                        <th>Mobile No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="centre_creation_content" style="display:none;">
    <form id="centre_creation" name="centre_creation">
        <input type="hidden" id="centre_create_id">
        <div class="row gutters">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Centre Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="centre_id"> Centre ID</label>
                                            <input type="text" class="form-control" id="centre_id" name="centre_id" placeholder="Enter Centre ID" tabindex="1" readonly>
                                            <input type="hidden" id="centre_id_upd" name="centre_id_upd">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="centre_no">Centre No</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control " id="centre_no" name="centre_no" placeholder="Enter Centre Number" tabindex="2">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="centre_name">Centre Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="centre_name" name="centre_name" placeholder="Enter Centre Name" tabindex="3">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile1"> Mobile Number 1</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="mobile1" name="mobile1" placeholder="Enter Mobile Number 1" onKeyPress="if(this.value.length==10) return false;" tabindex="4">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile2"> Mobile Number 2</label>
                                            <input type="number" class="form-control" id="mobile2" name="mobile2" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile Number 2" tabindex="5">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="area">Area</label><span class="text-danger">*</span>
                                            <input type="hidden" id="area_edit">
                                            <select type="text" class="form-control" id="area" name="area" tabindex="6">
                                                <option value="">Select Area</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="branch">Branch</label><span class="text-danger">*</span>
                                            <input type="hidden" id="branch_edit">
                                            <select type="text" class="form-control" id="branch" name="branch" tabindex="7">
                                                <option value="">Select Branch</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-group">
                                            <label for="latlong">Location</label>
                                            <input type="text" class="form-control" name="latlong" id="latlong" placeholder="Enter Latitude Longitude">
                                        </div>
                                    </div>
                                    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 col-12">
                                        <div class="form-group">
                                            <label style="visibility:hidden">Location</label>
                                            <button class="btn btn-primary" id="getlatlong" name="getlatlong" style="padding: 5px 35px;"><span class="icon-my_location"></span></button>
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
                                            <input type="file" class="form-control" id="pic" name="pic" tabindex="8">
                                            <input type="hidden" id="per_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Reference Info
                            <button type="button" class="btn btn-primary" id="add_rep" name="add_rep" data-toggle="modal" data-target="#add_rep_info_modal" onclick="getRepresentTable()" style="padding: 5px 35px; float: right;" tabindex='9'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="rep_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Name</th>
                                                <th>Aadhar No</th>
                                                <th>Occupation</th>
                                                <th>Mobile No</th>
                                                <th>Designation</th>
                                                <th>Remark</th>
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
            <div class="col-md-12 ">
                <div class="text-right">

                    <button type="submit" name="submit_centre_creation" id="submit_centre_creation" class="btn btn-primary" value="Submit" tabindex="10"><span class="icon-check"></span>&nbsp;Submit</button>
                    <button type="reset" class="btn btn-outline-secondary" tabindex="11">Clear</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!--Family Info Modal-->
<div class="modal fade" id="add_rep_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Representative Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getRepresentInfoTable();" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="represent_form">
                        <div class="row">
                            <input type="hidden" name="represent_id" id='represent_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="rep_name">Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="rep_name" id="rep_name" tabindex="1" placeholder="Enter Name">
                                    <input type="hidden" id="addrep_name_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="rep_aadhar">Aadhar No</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="rep_aadhar" id="rep_aadhar" tabindex="1" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                    <input type="hidden" id="addaadhar_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="rep_occupation">Occupation</label>
                                    <input class="form-control" name="rep_occupation" id="rep_occupation" tabindex="1" placeholder="Enter Occupation">
                                    <input type="hidden" id="addoccupation_id" value='0'>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="rep_mobile">Mobile No</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="rep_mobile" id="rep_mobile" onKeyPress="if(this.value.length==10) return false;" tabindex="1" placeholder="Enter Mobile Number">
                                    <input type="hidden" id="addmobile_id" value='0'>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="designation">Designation</label>
                                    <input class="form-control" name="designation" id="designation" tabindex="1" placeholder="Enter Designation">
                                    <input type="hidden" id="adddes_name_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark" tabindex="1"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_rep" id="submit_rep" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="represent_creation_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.NO</th>
                                    <th>Name</th>
                                    <th>Aadhar No</th>
                                    <th>Occupation</th>
                                    <th>Mobile No</th>
                                    <th>Designation</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getRepresentInfoTable()">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Family Modal End-->
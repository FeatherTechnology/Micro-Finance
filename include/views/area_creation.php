<!-- Area Name Dropdown: If Area name Disabled it show in Area "List" but in "Dropdown" -->
<div class="row gutters">
    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary add_area_btn"><span class="icon-add"></span> Add Area Creation</button>
            <button class="btn btn-primary back_to_area_btn" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div></br>
        <!----------------------------- CARD START  AREA CREATION TABLE ------------------------------>
        <div class="card wow area_table_content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="area_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Area</th>
                                    <th>Branch</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END  AREA CREATION TABLE ------------------------------>


        <!----------------------------- CARD START  AREA CREATION FORM ------------------------------>
        <div id="area_creation_content" style="display: none;">
            <form id="area_creation" name="area_creation" method="post" enctype="multipart/form-data">
                <input type="hidden" id="area_creation_id" value="0">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">General Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="branch_name" name="branch_name" tabindex="1">
                                                <option value="">Select Branch Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="area_name">Area Name</label><span class="text-danger">*</span>
                                            <input type="hidden" id="area_name2">
                                            <select class="form-control" id="area_name" name="area_name" tabindex="2" multiple>
                                                <option value="">Select Area Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-2" style="margin-top: 18px;">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary modalBtnCss" id="area_modal_btn" onclick="getAreaNameTable()"  tabindex="3"><span class="icon-add"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3 text-right">
                            <button name="submit_area_creation" id="submit_area_creation" class="btn btn-primary" tabindex="4"><span class="icon-check"></span>&nbsp;Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" tabindex="5">Clear</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!----------------------------- CARD END  AREA CREATION FORM------------------------------>

    </div>
</div>


<!-- /////////////////////////////////////////////////////////////////// Area Name Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="add_area_name_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Area Name</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getAreaNameDropdown()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" id='area_name_div'>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="addarea_name">Area Name</label><span class="text-danger">*</span>
                                <input class="form-control" name="addarea_name" id="addarea_name" tabindex="2" placeholder="Enter Area Name">
                                <input type="hidden" id="addarea_name_id" value='0'>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="area_status">Status</label><span class="text-danger">*</span>
                                <select class="form-control" id="area_status" name="area_status" tabindex="3">
                                    <option value="">Select Status</option>
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <button name="submit_addarea" id="submit_addarea" class="btn btn-primary" tabindex="4" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="card">
                    <div class="card-body"> -->
                <div class="row">
                    <div class="col-12">
                        <table id="area_name_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Area Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
                <!-- </div>
                </div> -->

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="5" onclick="getAreaNameDropdown()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Area Name Modal END ////////////////////////////////////////////////////////////////////// -->
<div class="row gutters">
    <div class="col-12">
        <!----------------------------- CARD START  SEARCH FORM ------------------------------>
        <div id="search">
            <form id="search_form" name="search_form" method="post" enctype="multipart/form-data">

                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Search</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="aadhar_no">Aadhar Number</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="aadhar_no" name="aadhar_no" placeholder="Enter Aadhar Number" tabindex="1" maxlength="12">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cust_name">Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cust_name" name="cust_name" placeholder="Enter Customer Name" tabindex="2">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="centre_no">Centre Number</label><span class="text-danger">*</span>
                                            <input class="form-control" id="centre_no" name="centre_no" placeholder="Enter Centre Number" tabindex="3">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="mobile">Mobile Number</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile Number" tabindex="4" onKeyPress="if(this.value.length==10) return false;">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="centre_name">Centre Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="centre_name" name="centre_name" placeholder="Enter Centre Name" tabindex="5">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="area">Area</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="area" name="cus_mobile" placeholder="Enter Area" tabindex="6">
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3 text-right">
                                        <button name="submit_search" id="submit_search" class="btn btn-primary" tabindex="7"><span class="icon-check"></span>&nbsp;Search</button>
                                        <button type="reset" class="btn btn-outline-secondary" tabindex="8">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>





        <div id="search_content" style="display: none;">
            <br>
            <div class="radio-container">
                <div class="selector">
                    <div class="selector-item">
                        <input type="radio" id="customer_details" name="search_type" class="selector-item_radio" value="customer_details" checked>
                        <label for="customer_details" class="selector-item_label">Customer List</label>
                    </div>
                    <div class="selector-item">
                        <input type="radio" id="centre_details" name="search_type" class="selector-item_radio" value="centre_details">
                        <label for="centre_details" class="selector-item_label">Centre List</label>
                    </div>
                </div>
            </div>


            <br>


            <div class="card" id="custome_list_content" style="display:none">
                <div class="card-header">
                    <h5 class="card-title">Customer List</h5>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <table id="customer_list" class="table custom-table">
                            <thead>
                                <th width="20">S No.</th>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                                <th>Centre Name</th>
                                <th>Area</th>
                                <th>Branch</th>
                                <th>Mobile Number</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            <div class="card" id="centre_list_content" style="display:none">
                <div class="card-header">
                    <h5 class="card-title">Centre List</h5>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <table id="centre_list" class="table custom-table">
                            <thead>
                                <th width="20">S No.</th>
                                <th>Centre ID</th>
                                <th>Centre Number</th>
                                <th>Centre Name</th>
                                <th>Loan ID</th>
                                <th>Area</th>
                                <th>Branch</th>
                                <th>Mobile Number</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="card" id="customer_loan_content" style="display:none">
        <div class="card-header">
                    <h5 class="card-title">Customer Status&nbsp;<button type="button" id="back_to_search" style="float:right" class="btn btn-primary"><span class="icon-arrow-left"></span>&nbsp;Back</button></h5>
                </div>
            <div class="col-12">
                <table id="customer_loan_list" class="table custom-table">
                    <thead>
                        <th width="20">S No.</th>
                        <th>Loan Date</th>
                        <th>Loan ID</th>
                        <th>Loan Category</th>
                        <th>Loan Amount</th>
                        <th>Status</th>
                        <th>Chart</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" id="customer_centre_content" style="display:none">
        <div class="card-header">
                    <h5 class="card-title">Customer List&nbsp;<button type="button" id="back_to_centre_list" style="float:right" class="btn btn-primary"><span class="icon-arrow-left"></span>&nbsp;Back</button></h5>
                </div>
            <div class="col-12">
                <table id="customer_centre_list" class="table custom-table">
                    <thead>
                        <th width="20">S No.</th>
                        <th>Customer ID</th>
                        <th>Customer Name</th>
                        <th>Aadhar Number</th>
                        <th>Mobile</th>
                        <th>Area</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
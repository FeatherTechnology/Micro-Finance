<!-- closed List Start -->
<div class="card closed_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="closed_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Loan ID</th>
                        <th>Centre ID</th>
                        <th>Centre No</th>
                        <th>Centre Name</th>
                        <th>Area</th>
                        <th>Branch</th>
                        <th>Mobile</th>
                        <th>Loan Date</th>
                        <th>Closed Date</th>
                        <th>Loan Amount</th>
                        <th>Status</th>
                        <th>Sub Status</th>
                        <th>Chart</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<br>
<!-- centre infor content  -->
<div id="closed_content" style="display: none;">
    <div class="text-right mb-3">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <form id="closed_form" name="closed_form">
        <div class="row gutters">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Centre Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_id"> Centre ID</label>
                                    <input type="hidden" id="loan_id" name="loan_id">
                                    <input type="text" class="form-control" id="centre_id" name="centre_id" tabindex="1" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_no"> Centre Number</label>
                                    <input type="text" class="form-control" id="centre_no" name="centre_no" tabindex="2" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="centre_name"> Centre Name</label>
                                    <input type="text" class="form-control" id="centre_name" name="centre_name" tabindex="3" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" id="area" name="area" tabindex="4" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="branch">Branch</label>
                                    <input type="text" class="form-control" id="branch" name="branch" tabindex="5" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile" tabindex="6" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_date">Loan Date</label>
                                    <input type="text" class="form-control" id="loan_date" name="loan_date" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="closed_date">Closed Date</label>
                                    <input type="text" class="form-control" id="closed_date" name="closed_date" tabindex="8" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amount">Loan Amount</label>
                                    <input type="text" class="form-control" id="loan_amount" name="loan_amount" tabindex="9" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- closed  customer List Start -->
<div class="card closed_customer_table_content" style="display: none;">
    <div class="card-header">
        <div class="card-title">Customer List</div>
    </div>
    <div class="card-body">
        <div class="col-12">
            <table id="closed_customer_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Customer ID</th>
                        <th>Customer Name</th>
                        <th>Status</th>
                        <th>Sub Status</th>
                        <th>Remarks</th>
                        <th>Chart</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>
</div>
<!-- submit centre closed  -->
<div class="card centre_closed_content" style="display: none;">
    <div class="card-header">
        <div class="card-title">Centre Close</div>
    </div>
    <div class="row gutters">
        <div class="col-12">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="sub_status"> Sub Status</label><span class="text-danger">*</span>
                            <select class="form-control" id="sub_status" name="sub_status" tabindex="10" disabled>
                                <option value="">Select Sub Status</option>
                                <option value="1">Consider</option>
                                <option value="2">Blocked</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="centre_remarks">Centre Remarks</label>
                            <textarea class="form-control" name="centre_remarks" id="centre_remarks" tabindex="11" readonly></textarea>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
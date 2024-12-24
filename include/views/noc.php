<!----------------------------- CARD START NOC TABLE------------------------------>
<div class="card wow" id="noc_list">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table id="noc_table" class="table custom-table">
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
                            <th>Status</th>
                            <th>Sub Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<br>
<div id="noc_content" style="display: none;">
    <div class="text-right mb-3">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <form id="noc_form" name="noc_form">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="card" id="customer_list" style="display: none;">
    <div class="card-header">
        <h5 class="card-title">Document List</h5>
    </div>
    <div class="card-body">
        <table class="table custom-table" id="document_list_table">
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
                    <th>Date of NOC</th>
                    <th>Handover Person</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<div class="card" id="noc_date" style="display: none;">
    <div class="card-header">
        <h5 class="card-title"></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                    <label for="date_of_noc">Date of NOC</label><span class="required">*</span>
                    <input type="date" class="form-control" id="date_of_noc" name="date_of_noc" tabindex="1" readonly>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="form-group">
                    <label for="noc_member">Member</label><span class="required">*</span>
                    <select name="noc_member" id="noc_member" class="form-control" tabindex="2">
                        <option value="">Select Member Name</option>
                    </select>
                </div>
            </div>
            <div class="col-12 mt-3 text-right">
                <button name="submit_noc" id="submit_noc" class="btn btn-primary" tabindex="4"><span class="icon-check"></span>&nbsp;Submit</button>
            </div>
        </div>
    </div>
</div>
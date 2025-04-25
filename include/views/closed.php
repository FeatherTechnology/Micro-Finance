<!-- closed List Start -->
<div class="card closed_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="closed_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Loan ID</th>
                        <th>Date / Day</th>
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
<div class="col-12 text-right" style="margin-bottom:10px">
    <button class="btn btn-primary" id="back_to_coll_list" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
</div>
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
                    <div class="col-12 mt-3 text-right">
                        <button name="submit_centre_closed" id="submit_centre_closed" class="btn btn-primary" tabindex="12"><span class="icon-check"></span>&nbsp;Submit</button>
                    </div>
                </div>
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
<!----------------------------- CARD Start- Ledger View Chart ------------------------------>
<div class="card ledger_view_chart_model" style="display: none;">
    <div class="card-header">
        <div class="card-title">Ledger View</div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12" id="ledger_view_table_div" style="overflow: auto;">

            </div>
        </div>
    </div>
</div>
<!-----------------------------CARD END - Ledger View Chart --------------------------------->
<div class="modal fade" id="savings_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Savings Chart</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- <div class="modal-body">
                <div class="container-fluid "> -->
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
                    <!-- </div>
                </div> -->

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="fine_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Fine Chart</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body overflow-x-cls" id="fine_chart_table_div">
                <table class="table custom-table">
                    <thead>
                        <th>S No.</th>
                        <th>Date</th>
                        <th>Fine</th>
                        <th>Purpose</th>
                        <th>Paid Date</th>
                        <th>Paid Amount</th>
                        <th>Balance Amount</th>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
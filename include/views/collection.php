<div class="row gutters">
    <div class="col-12">
        <div class="card" id="collection_list">
            <div class="card-header">
                <h5 class="card-title">Collection List</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="collection_list_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Loan ID</th>
                                    <th>Date / Day</th>
                                    <th>Centre ID</th>
                                    <th>Centre No</th>
                                    <th>Centre Name</th>
                                    <th>Mobile</th>
                                    <th>Area</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th>Chart</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 text-right" style="margin-bottom:10px">
            <button class="btn btn-primary" id="back_to_coll_list" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div>

        <div id="coll_main_container" style="display: none;">
            <!-- Row start -->
            <!-- /////////////////////////////////////////////////// Collection Info  START ///////////////////////////////////////// -->
            <div class="card coll_details">
                <div class="card-header">
                    <h5 class="card-title">Collection Info</h5>
                </div>

                <input type="hidden" name="loan_category_id" id="loan_category_id">
                <input type="hidden" name="loan_id" id="loan_id">
                <input type="hidden" name="status" id="status">
                <input type="hidden" name="sub_status" id="sub_status">

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Amount</label>&nbsp;<span class="text-danger totspan">*</span>
                                        <input type="text" class="form-control" readonly id="tot_amt" name="tot_amt" value='' tabindex='1'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Paid Amount</label>&nbsp;<span class="text-danger paidspan">*</span>
                                        <input type="text" class="form-control" readonly id="paid_amt" name="paid_amt" value='' tabindex='2'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Balance Amount</label>&nbsp;<span class="text-danger balspan">*</span>
                                        <input type="text" class="form-control" readonly id="bal_amt" name="bal_amt" value='' tabindex='3'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Due Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" readonly id="due_amt" name="due_amt" value='' tabindex='4'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Pending Amount</label>&nbsp;<span class="text-danger pendingspan">*</span>
                                        <input type="text" class="form-control" readonly id="pending_amt" name="pending_amt" value='' tabindex='5'>
                                        <input type="hidden" class="form-control" readonly id="pend_amt" name="pend_amt">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Payable Amount</label>&nbsp;<span class="text-danger payablespan">*</span>
                                        <input type="text" class="form-control" readonly id="payable_amt" name="payable_amt" value='' tabindex='6'>
                                        <input type="hidden" class="form-control" readonly id="payableAmount" name="payableAmount">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="fine_charge" name="fine_charge" value='' tabindex='7'>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Customer List</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table id="customer_list_table" class=" table custom-table">
                                <thead>
                                    <!-- First row: Spanning headers for sections -->
                                    <tr>
                                        <th colspan="8">Collection Info</th>
                                        <th colspan="7">Collection Track</th>
                                    </tr>
                                    <!-- Second row: Column headers -->
                                    <tr>
                                        <th width="50">S.No.</th>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Due Amount</th>
                                        <th>Pending</th>
                                        <th>Payable</th>
                                        <th>Fine</th>
                                        <th>Collection Date</th>
                                        <th>Collection Due Amount</th>
                                        <th>Collection Savings</th>
                                        <th>Collection Fine</th>
                                        <th>Total Collection</th>
                                        <th>Charts</th>
                                        <th>Action</th>
                                        <th style="display: none;">aadhar</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /////////////////////////////////////////////////// Collection Info END ///////////////////////////////////////// -->

            <!-- Submit Button Start -->
            <div class="col-md-12 coll_details">
                <div class="text-right">
                    <button type="submit" name="submit_collection" id="submit_collection" class="btn btn-primary" value="Submit" tabindex='8'><span class="icon-check"></span>&nbsp;Submit</button>
                </div>
            </div>
            <!-- Submit Button End -->

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
<!-- /////////////////////////////////////////////////////////////////// Fine Add Modal START ////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="fine_form_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Add Fine</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeFineChartModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="coll_date "> Date </label> <span class="required">&nbsp;*</span>
                            <input type="hidden" class="form-control" id="fine_loan_id" name="fine_loan_id">
                            <input type="text" class="form-control" id="fine_date" name="fine_date" readonly tabindex='1'>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="add_customer">Customer Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="add_customer" name="add_customer" tabindex="2">
                                <option value="">Select Customer Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="coll_purpose"> Purpose </label> <span class="required">&nbsp;*</span>
                            <input type="text" class="form-control" id="fine_purpose" name="fine_purpose" placeholder="Enter Purpose" onkeydown="return /[a-z ]/i.test(event.key)" tabindex='2'>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="coll_amnt"> Amount </label> <span class="required">&nbsp;*</span>
                            <input type="number" class="form-control" id="fine_Amnt" name="fine_Amnt" placeholder="Enter Amount" tabindex='3'>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-4 col-12">
                        <button type="button" tabindex="4" name="fine_form_submit" id="fine_form_submit" class="btn btn-primary" style="margin-top: 19px;">Submit</button>
                    </div>
                </div>
                </br>
                <div>
                    <table id="fine_form_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th width="15%"> S.No </th>
                                <th>Customer Name</th>
                                <th> Date </th>
                                <th> Purpose </th>
                                <th> Amount </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeFineChartModal()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Fine Add Modal END /////////////////////////////////////////////////////////////-->
<!-- ////////////////////////////////////////////////////////////////// Savings Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="Savings_chart_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Savings Chart</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
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
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Savings Chart Modal END ////////////////////////////////////////////////////////////////////// -->
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
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal END ////////////////////////////////////////////////////////////////////// -->
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
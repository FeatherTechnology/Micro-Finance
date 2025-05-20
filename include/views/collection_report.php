<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <input type="button" id='collection_report_btn' name='collection_report_btn' class="toggle-button" style="background-color: #381f42;color:white" value='Search'>
        </div> <br/>
        <!-- Collection report Start -->
        <div class="card">
            <div class="card-body overflow-x-cls">
                <div class="col-12">
                    <table id="collection_report_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Day</th>
                                <th>Loan ID</th>
                                <th>Loan Date</th>
                                <th>Centre ID</th>
                                <th>Centre Number</th>
                                <th>Centre Name</th>
                                <th>Branch</th>
                                <th>Mobile</th>
                                <th>Loan category</th>
                                <th>User Type</th>
                                <th>User</th>
                                <th>Receipt Date</th>
                                <th>Due Amount</th>
                                <th>Principal Amount</th>
                                <th>Interest Amount</th>
                                <th>Fine</th>
                                <th>Total Paid</th>
                                <th>Status</th>
                                <th>Sub Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="13"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!--Collection report End-->
    </div>
</div>
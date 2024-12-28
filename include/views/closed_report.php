<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <input type="button" id='closed_report_btn' name='closed_report_btn' class="toggle-button" style="background-color: #381f42;color:white" value='Search'>
        </div> <br />
        <!-- Closed report Start -->
        <div class="card">
            <div class="card-body">
                <div class="col-12">
                    <table id="closed_report_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Day</th>
                                <th>Loan ID</th>
                                <th>Loan Date</th>
                                <th>Centre Id</th>
                                <th>Centre Number</th>
                                <th>Centre Name</th>
                                <th>Branch</th>
                                <th>Mobile</th>
                                <th>Loan Category</th>
                                <th>Loan Amount</th>
                                <th>Maturity Date</th>
                                <th>Closed Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10"></td>
                                <td></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!--Closed report End-->
    </div>
</div>
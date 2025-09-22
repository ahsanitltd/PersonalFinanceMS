<div class="modal fade" id="form-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="card-body">
                    <form id="form" method="POST">
                        <div class="row">
                            <div class="col-sm-12 mt-1 ">
                                <label for="name">Partner name</label>
                                <select class="form-control select2-ajax" name="investment_partner_id"
                                    data-url="{{ route('api-investment-partner-data.index') }}"
                                    data-columns='{"id":"id", "text":"name"}' data-placeholder="Select partner">
                                    <option value="">Select partner</option>
                                </select>
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="agreed_amount">Agreed amount</label>
                                <input type="text" class="form-control" placeholder="Enter amount" id="agreed_amount"
                                    name="agreed_amount">
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="amount_invested">Invested amount</label>
                                <input type="text" class="form-control" placeholder="Enter amount"
                                    id="amount_invested" name="amount_invested">
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="your_due">your_due</label>
                                <input type="text" class="form-control" placeholder="Enter amount" id="your_due"
                                    name="your_due">
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="partner_due">Partner due</label>
                                <input type="text" class="form-control" placeholder="Enter amount" id="partner_due"
                                    name="partner_due">
                            </div>

                            <div class="col-sm-6 mt-1 ">
                                <label>Profit type</label>
                                <select class="form-control select2-ajax" name="profit_type" data-url=""
                                    data-placeholder="Select One">
                                    <option value="">Select One</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="profit_value">Profit value</label>
                                <input type="text" class="form-control" placeholder="Enter amount" id="profit_value"
                                    name="profit_value">
                            </div>

                            <div class="col-sm-12 mt-1 ">
                                <label class="d-block" for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="w-100" style="resize: auto;"></textarea>
                            </div>
                            <div class="col-sm-12 my-4">
                                <!-- Add hidden url input for create/Update form -->
                                <input type="hidden" id="url" value="">

                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-danger"
                                        data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-outline-primary" id="formSubmitBtn">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

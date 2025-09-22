<div class="modal fade" id="form_log_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="logCloseModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="card-body">
                    <form id="log_form" method="POST">
                        <div class="row">
                            <input type="hidden" name="investment_id" value="">
                            
                            <div class="col-sm-6 mt-1 ">
                                <label for="name">Payment Type</label>
                                <select class="form-control select2-ajax" name="type" data-placeholder="Select type">
                                    <option value="">Select partner</option>
                                    <option value="investment">Investment</option>
                                    <option value="due_payment">Due payment</option>
                                    <option value="profit">Profit</option>
                                    <option value="loss">Loss</option>
                                </select>
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="name">Paid By</label>
                                <select class="form-control select2-ajax" name="paid_by"
                                    data-url="{{ route('api-investment-partner-data.index') }}"
                                    data-columns='{"id":"id", "text":"name"}' data-placeholder="Select partner">
                                    <option value="">Select partner</option>
                                </select>
                            </div>
                            <div class="col-sm-6 mt-1 ">
                                <label for="Amount">Amount</label>
                                <input type="text" class="form-control" placeholder="Enter amount" id="agreed_amount"
                                    name="amount">
                            </div>

                            <div class="col-sm-12 mt-1 ">
                                <label class="d-block" for="notes">Notes</label>
                                <textarea name="note" id="notes" class="w-100" style="resize: auto;"></textarea>
                            </div>
                            <div class="col-sm-12 my-4">
                                <!-- Add hidden url input for create/Update form -->
                                <input type="hidden" id="logUrl" value="">

                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="logCloseModal()">Close</button>
                                    <button type="button" class="btn btn-outline-primary" id="logFormSubmitBtn">Save
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

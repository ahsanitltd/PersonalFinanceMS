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
                            <!-- Company Select -->
                            <div class="col-sm-6 mt-1 ">
                                <label>Company</label>
                                <select class="form-control select2-ajax" name="company_id"
                                    data-url="{{ route('api-company-data.index') }}"
                                    data-columns='{"id":"id", "text":"name"}' data-placeholder="Select a Company">
                                    <option value="">Select company</option>
                                </select>
                            </div>

                            <!-- Amount -->
                            <div class="col-sm-6 mt-1">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" placeholder="Enter amount" id="amount"
                                    name="amount" step="0.01">
                            </div>

                            <!-- Currency -->
                            <div class="col-sm-6 mt-1">
                                <label for="currency">Currency</label>
                                <input type="text" class="form-control" placeholder="e.g. BDT" id="currency"
                                    name="currency" value="BDT" readonly>
                            </div>

                            <!-- Earn Month -->
                            <div class="col-sm-6 mt-1">
                                <label for="earn_month">Earning Month</label>
                                <input type="date" class="form-control" id="earn_month" name="earn_month">
                            </div>

                            <!-- Paid Checkbox -->
                            <div class="col-sm-6 mt-1">
                                <label class="d-block" for="is_paid">Paid?</label>
                                <input type="checkbox" id="is_paid" name="is_paid" value="1">
                            </div>

                            <!-- Paid At -->
                            <div class="col-sm-6 mt-1">
                                <label for="paid_at">Paid At</label>
                                <input type="date" class="form-control" id="paid_at" name="paid_at">
                            </div>

                            <!-- Notes -->
                            <div class="col-sm-12 mt-1">
                                <label class="d-block" for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="w-100" style="resize: auto;"></textarea>
                            </div>

                            <div class="col-sm-12 my-4">
                                <!-- Hidden input for AJAX submit URL -->
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

            <div class="modal-footer justify-content-between"></div>

        </div>
    </div>
</div>

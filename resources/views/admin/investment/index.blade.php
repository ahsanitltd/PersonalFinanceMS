@extends('admin.master.app')

@section('custom-css')
@endsection


@section('main-content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-head-fixed text-nowrap" id="dataTable" data-type="expandable"
                                data-url="{{ route('api-investment-data.index') }}"
                                data-expandable-url="{{ route('api-investment-log-data.index') }}"
                                data-columns='["agreed_amount","amount_invested","your_due","partner_due","profit_type","profit_value","status","notes","created_by","partner_name","created_at"]'
                                data-expandable-columns='["investment_id", "type", "amount", "paid_by", "log_date", "note", "created_by"]'
                                data-expandable-key="investment_id">
                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            <button type="button" class="btn btn-primary w-100 create-btn mb-2"
                                                data-url="{{ route('api-investment-data.store') }}" data-toggle="modal"
                                                data-target="form-modal">
                                                <i class="fas fa-plus"></i>
                                                Create
                                            </button>
                                        </th>
                                        <th colspan="2" class="text-center">
                                            <button class="btn btn-outline-danger mr-1 d-none" type="submit"
                                                data-url="{{ route('api-investment-data.destroy', 0) }}"
                                                id="multiple_delete_btn">
                                                Delete all
                                            </button>
                                            <button class="btn btn-outline-success mr-1" onclick="exportToExcel()">
                                                <i class="fas fa-download"></i> Excel
                                            </button>
                                            <button class="btn btn-outline-danger mx-1" onclick="exportToPDF()">
                                                <i class="fas fa-download"></i> PDF
                                            </button>
                                            <button class="btn btn-outline-primary mx-1" onclick="exportToWord()">
                                                <i class="fas fa-download"></i> Word
                                            </button>
                                        </th>
                                        <th colspan="6">
                                            <input class="form-control" type="search" placeholder="Search"
                                                id="searchInput">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Partner name</th>
                                        <th>Calculations</th>
                                        <th>Profit</th>
                                        <th>Notes</th>
                                        <th>Created by</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be loaded via AJAX -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="100" class="text-center">
                                            <div id="pagination-wrapper"></div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('admin.investment.form-modal')
    @include('admin.investment.log-form-modal')
@endsection

@section('custom-js')
    <script>
        function renderExpandableRows(rows, highlightId = null, perPage = 10) {
            $tbody.empty();

            const parentColumnsLength = columns.length;

            if (!rows || rows.length === 0) {
                $tbody.append(`<tr><td colspan="${parentColumnsLength + 3}">No data found.</td></tr>`);
                return;
            }

            rows.forEach((item, index) => {

                const serial = (currentPage - 1) * perPage + index + 1;
                const highlightClass = item.id == highlightId ? 'highlight-row' : '';


                let rowHtml = `
                    <tr class="${highlightClass}" data-expandable-row data-id="${item.id}" aria-expanded="false">
                        <td>${serial}</td>
                        <td>
                            ${item.partner_name}
                            <br>
                            <span class="px-2 py-1 small rounded-pill ${item.status?.toLowerCase() === 'active' ? 'bg-success text-white' : 'bg-danger text-white'}">
                                ${item.status || '-'}
                            </span>
                        </td>
                        <td style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.5;">
                            <div><strong>Agreed Amount:</strong> ${item.agreed_amount}</div>
                            <div><strong>Amount Invested:</strong> ${item.amount_invested}</div>
                            <div><strong>Partner Due:</strong> ${item.partner_due}</div>
                            <div><strong>Your Due:</strong> ${item.your_due}</div>
                        </td>
                        <td>
                            <div><strong>${item.profit_type} :</strong><br> ${item.profit_value}</div>
                        </td>
                        <td>
                            ${item.notes?.trim() || '--'}
                        </td>
                        <td>
                            <div><strong>By:</strong> ${item.created_by}</div>
                            <div><strong>Created:</strong> ${item.created_at}</div>
                            <div><strong>Updated:</strong> ${item.updated_at}</div>
                        </td>
                        <td>
                           <button class="btn btn-sm btn-outline-primary mr-1 create-log-btn" data-url="${expandableUrl}">Create Log</button>
                            <br>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-url="${baseUrl}/${item.id}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-url="${baseUrl}/${item.id}" data-id="${item.id}">Delete</button>
                        </td>
                    </tr>
                    <tr class="expandable-body d-none">
                        <td colspan="${parentColumnsLength}" style="border-left: 2px solid #F00;">
                            <div class="nested-table-wrapper"></div>
                        </td>
                    </tr>`;

                $tbody.append(rowHtml);
            });

            // Handle row expand/collapse and nested load
            $tbody.off('click', 'tr[data-expandable-row]').on('click', 'tr[data-expandable-row]', function() {
                const $row = $(this);
                const parentId = $row.data('id');
                const $nestedRow = $row.next('tr.expandable-body');
                const $wrapper = $nestedRow.find('.nested-table-wrapper');
                const isOpen = $row.attr('aria-expanded') === 'true';
                const cols = $table.data('expandable-columns');

                if (isOpen) {
                    $row.attr('aria-expanded', 'false');
                    $nestedRow.addClass('d-none');
                    return;
                }

                $row.attr('aria-expanded', 'true');
                $nestedRow.removeClass('d-none');

                if ($wrapper.data('loaded')) return;

                loadNestedPage(parentId, 1);

                function loadNestedPage(id, page) {
                    $wrapper.html('Loading...');

                    $.get(expandableUrl, {
                        [expandableKey]: id,
                        columns: cols,
                        page
                    }).done(res => {
                        if (res?.success && res.data?.data?.length) {

                            const thead = `
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Paid by</th>
                                        <th>Note</th>
                                        <th>Created by</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>`;

                            const rows = res.data.data.map((item, index) => {
                                const serial = (res.data.current_page - 1) * res.data.per_page +
                                    index + 1;

                                return `
                                    <tr>
                                        <td style="width: 50px;">${serial}</td>
                                        <td>${item.type ?? '--'}</td>
                                        <td>${item.amount ?? '--'}</td>
                                        <td>${item.paid_by_name ?? '--'}</td>
                                        <td>${item.note ?? '--'}</td>
                                        <td>
                                            <div><strong>By:</strong> ${item.created_by}</div>
                                            <div><strong>Created:</strong> ${item.created_at}</div>
                                            <div><strong>Updated:</strong> ${item.updated_at}</div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger delete-btn" data-url="${expandableUrl}/${item.id}" data-id="${item.id}">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            }).join('');

                            const html = `
                                <div class="card">
                                    <table class="table table-sm table-hover table-striped">
                                        ${thead}
                                        <tbody>${rows}</tbody>
                                        <tfoot>
                                            <tr><td colspan="7" class="nested-pagination-cell"></td></tr>
                                        </tfoot>
                                    </table>
                                </div>`;

                            $wrapper.html(html);

                            // Pagination
                            const totalPages = Math.ceil(res.data.total / res.data.per_page);
                            if (totalPages > 1) {
                                let pagHtml = `
                                    <ul class="pagination pagination-sm justify-content-center mb-0">
                                        <li class="page-item ${page === 1 ? 'disabled' : ''}">
                                            <a href="#" class="page-link" data-page="${page - 1}">&laquo;</a>
                                        </li>`;

                                for (let p = 1; p <= totalPages; p++) {
                                    pagHtml += `
                                        <li class="page-item ${p === page ? 'active' : ''}">
                                            <a href="#" class="page-link" data-page="${p}">${p}</a>
                                        </li>`;
                                }

                                pagHtml += `
                                        <li class="page-item ${page === totalPages ? 'disabled' : ''}">
                                            <a href="#" class="page-link" data-page="${page + 1}">&raquo;</a>
                                        </li>
                                    </ul>`;

                                $wrapper.find('.nested-pagination-cell').html(pagHtml);
                            }

                            $wrapper.data('loaded', true);
                        } else {
                            $wrapper.html('<div class="text-muted">No data available.</div>');
                        }
                    }).fail(() => {
                        $wrapper.html('<div class="text-danger">Error loading data.</div>');
                    });
                }


                // Delegate pagination clicks inside nested table
                $wrapper.off('click').on('click', 'a.page-link', function(e) {
                    e.preventDefault();
                    const page = parseInt($(this).data('page'));
                    if (page && page > 0) loadNestedPage(parentId, page);
                });
            });
        }
    </script>

    <script>
        $(function() {
            let m = false;
            $('#amount_invested').on('input', () => m = true);
            $('#form').on('input change', function() {
                let a = +$('#agreed_amount').val() || 0,
                    i = $('#amount_invested'),
                    t = $('select[name="profit_type"]').val();
                if (!m) i.val(a);
                $('#your_due').val((a - (+i.val() || 0)).toFixed(2));
                $('#profit_value').prop('disabled', !t);
                $('#partner_due').val(i.val());
            }).trigger('change');
        });

        let logConfig = {
            formId: '#log_form',
            modalId: '#form_log_modal',
            titleSelector: '.modal-title',
            urlInputId: '#logUrl',
            methodInputName: '_method',
            submitBtnId: '#logFormSubmitBtn',
            createBtnClass: '.create-log-btn',
        };
        // Event bindings
        $(document).on('click', '.create-log-btn', function() {
            const url = $(this).attr('data-url');
            const dataId = $(this).closest('tr').data('id');
            $('#form_log_modal').find('input[name="investment_id"]').val(dataId);

            $('#form_log_modal').modal({
                backdrop: 'static',
                keyboard: false
            });

            openModal(logConfig, url);
        });

        $(document).on('click', logConfig.submitBtnId, function(e) {
            e.preventDefault();
            ajaxCall({
                type: $(`${logConfig.formId} input[name="${logConfig.methodInputName}"]`).val() || $(
                    logConfig
                    .formId).attr('method'),
                url: $(logConfig.urlInputId).val(),
                dataType: 'JSON',
                data: $(logConfig.formId).serialize(),
                crud: 'formSubmit'
            });

            logCloseModal();
        });

        function logCloseModal() {
            const $form = $(logConfig.formId);
            const $modal = $(logConfig.modalId);
            const $title = $modal.find(logConfig.titleSelector);

            if ($title.length) $title.text("Add ");
            if ($form.length) {
                $form.trigger('reset');
                $form.find('input, textarea').val('');
                $form.find('select').prop('selectedIndex', 0);
                $form.find(`input[name="${logConfig.methodInputName}"]`).remove();
                if (logConfig.urlInputId) $(logConfig.urlInputId).val('');
            }
            if ($modal.length) $modal.modal('hide');
        }
    </script>
@endsection

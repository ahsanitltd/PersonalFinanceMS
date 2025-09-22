// Custom DataTable operation start here  
// selecting table, get url and data values... 
// this options are completely replacedable and usable dynamically many times if needed on each page
const $table = $('#dataTable');
const $tbody = $table.find('tbody');
const baseUrl = $table.data('url');

// serially showable columns on table like, ["name", "mobile", "address"]... will come as modified name from data
// const columns = JSON.parse($table.attr('data-columns'));
let columns = $table.attr('data-columns') ? JSON.parse($table.attr('data-columns')) : [];

const tableType = $table.data('type') || 'simple'; // default to simple
const expandableUrl = $table.data('expandable-url');
const expandableKey = $table.data('expandable-key') || 'parent_id';


const $pagination = $('#pagination-wrapper');


let currentPage = 1;
let search = '';

// get data from api server 
function fetchData(page = 1, searchQuery = '', highlightId = null) {
    currentPage = page;

    $('#loader').show();
    $.get(baseUrl, {
        page,
        search: searchQuery,
    }, response => {
        $('#loader').hide();

        if (!response.success) {
            $tbody.html(`<tr><td colspan="${columns.length + 2}">Error fetching data</td></tr>`);
            $pagination.empty();
            return;
        }

        if (tableType === 'expandable') {
            renderExpandableRows(response.data.data, highlightId, response.data.per_page);
            // initializeExpandableRows(); // Optional: if needed for UI behavior
        } else {
            renderRows(response.data.data, highlightId, response.data.per_page);
        }

        renderPagination(response.data);

        if (highlightId) {
            setTimeout(() => {
                $tbody.find(`tr.highlight-row`).removeClass('highlight-row').addClass('fade-out');
            }, 3000); // fades after 3 seconds
        }

    }).fail(() => {
        $('#loader').hide();
        $tbody.html(`<tr><td colspan="${columns.length + 2}">Error fetching data</td></tr>`);
        $pagination.empty();
    });
}


function renderExpandableRows(rows, highlightId = null, perPage = 10) {
    $tbody.empty();

    if (!rows || rows.length === 0) {
        $tbody.append(`<tr><td colspan="${columns.length + 3}">No data found.</td></tr>`);
        return;
    }

    rows.forEach((item, index) => {
        const serial = (currentPage - 1) * perPage + index + 1;
        const highlightClass = item.id == highlightId ? 'highlight-row' : '';
        const nestedCols = ['id', 'name', 'type', 'contact', 'description'];
        const nestedColsJson = JSON.stringify(nestedCols);

        let rowHtml = `<tr class="${highlightClass}" data-expandable-row data-id="${item.id}" data-expandable-columns='${nestedColsJson}' aria-expanded="false">`;

        // Serial + checkbox
        rowHtml += `
            <td>${serial}</td>`;

        // <td class="text-center">
        //     <input type="checkbox" name="id" class="checkitem" value="${item.id}" />
        // </td>


        // Data columns
        columns.forEach(col => {
            let val = item[col] ?? '';
            val = String(val).trim();
            if (!val) val = '<span class="text-muted">-</span>';
            else if (val.length > 100) val = `<span title="${val}">${val.slice(0, 100)}...</span>`;
            rowHtml += `<td>${val}</td>`;
        });

        // Actions
        rowHtml += `
            <td>
                <button class="btn btn-sm btn-outline-primary mr-1 create-btn" data-url="${baseUrl}/${item.id}">Create</button>
                <button class="btn btn-sm btn-outline-primary edit-btn" data-url="${baseUrl}/${item.id}">Edit</button>
                <button class="btn btn-sm btn-outline-danger delete-btn" data-url="${baseUrl}/${item.id}" data-id="${item.id}">Delete</button>
            </td>
        </tr>`;

        // Expandable row with nested content
        rowHtml += `
            <tr class="expandable-body d-none">
                <td colspan="${columns.length + 3}">
                    <div class="nested-table-wrapper"></div>
                </td>
            </tr>`;

        $tbody.append(rowHtml);
    });

    // Handle row expand/collapse and nested load
    $tbody.off('click', 'tr[data-expandable-row]').on('click', 'tr[data-expandable-row]', function () {
        const $row = $(this);
        const parentId = $row.data('id');
        const $nestedRow = $row.next('tr.expandable-body');
        const $wrapper = $nestedRow.find('.nested-table-wrapper');
        const isOpen = $row.attr('aria-expanded') === 'true';

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

            let cols = [];
            try {
                cols = JSON.parse($row.attr('data-expandable-columns'));
            } catch {
                cols = [];
            }

            $.get(expandableUrl, { [expandableKey]: id, columns: cols, page }, function (res) {
                if (res?.success && res.data?.data?.length) {
                    let html = '<div class="card"><table class="table table-sm table-hover"><thead><tr>';
                    cols.forEach(c => html += `<th>${typeof c === 'string' ? c : c.label || c.key}</th>`);
                    html += '<th>Actions</th></tr></thead><tbody>';

                    res.data.data.forEach((nested, index) => {
                        const serial = (res.data.current_page - 1) * res.data.per_page + index + 1;
                        html += '<tr>';
                        html += `<td>${serial}</td>`;
                        cols.forEach(c => {
                            const key = typeof c === 'string' ? c : c.key;
                            if (key === 'id') return;
                            html += `<td>${nested[key] ?? ''}</td>`;
                        });
                        html += `
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-url="${expandableUrl}/${nested.id}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-url="${expandableUrl}/${nested.id}" data-id="${nested.id}">Delete</button>
                            </td>
                        </tr>`;
                    });


                    // Table footer for pagination
                    html += `</tbody>
                        <tfoot>
                            <tr>
                                <td colspan="${cols.length + 1}" class="nested-pagination-cell"></td>
                            </tr>
                        </tfoot>
                    </table></div>`;

                    $wrapper.html(html);

                    // Build pagination inside tfoot
                    const totalPages = Math.ceil(res.data.total / res.data.per_page);
                    if (totalPages > 1) {
                        let pagHtml = '<ul class="pagination pagination-sm justify-content-center mb-0">';
                        pagHtml += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                            <a href="#" class="page-link" data-page="${page - 1}">&laquo;</a></li>`;

                        for (let p = 1; p <= totalPages; p++) {
                            pagHtml += `<li class="page-item ${p === page ? 'active' : ''}">
                                <a href="#" class="page-link" data-page="${p}">${p}</a></li>`;
                        }

                        pagHtml += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                            <a href="#" class="page-link" data-page="${page + 1}">&raquo;</a></li>`;
                        pagHtml += '</ul>';

                        $wrapper.find('.nested-pagination-cell').html(pagHtml);
                    }

                    $wrapper.data('loaded', true);
                } else {
                    $wrapper.html('<div class="text-muted">No nested data available.</div>');
                }
            }).fail(() => {
                $wrapper.html('<div class="text-danger">Error loading nested data.</div>');
            });
        }

        // Delegate pagination clicks inside nested table
        $wrapper.off('click').on('click', 'a.page-link', function (e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page > 0) loadNestedPage(parentId, page);
        });
    });
}

// decorate inside table by api response
function renderRows(rows, highlightId = null, perPage = 10) {

    $tbody.empty();
    if (!rows || rows.length === 0) {
        $tbody.append(`<tr><td colspan="${columns.length + 2}">No data found.</td></tr>`);
        return;
    }

    rows.forEach((item, index) => {
        const serial = (currentPage - 1) * perPage + index + 1;

        const highlightClass = (item.id == highlightId) ? 'highlight-row' : '';
        // console.log(`Row ID: ${item.id}, Highlight: ${highlightClass ? 'YES' : 'NO'}`);

        let rowHtml = `<tr class="${highlightClass}">`;

        // Serial number
        rowHtml += `<td>${serial}</td>`;

        // Checkbox
        rowHtml += `
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input checkitem" name="id" type="checkbox" value="${item.id}" />
                        </div>
                    </td>`;


        // Data columns
        columns.forEach(col => {

            const rawValue = item[col] ?? '';
            const value = String(rawValue).trim();
            const lowerCol = col.toLowerCase();

            let cellContent = '';

            // 1. Empty/null/undefined
            if (!value) {
                cellContent = `<span class="text-muted">-</span>`;
            }
            // 2. HTML content from editors like TinyMCE/Summernote
            else if (/<\/?[a-z][\s\S]*>/i.test(value)) {
                const div = document.createElement('div');
                div.innerHTML = value;
                const textContent = div.textContent || div.innerText || '';

                if (textContent.length > 100) {
                    const shortText = textContent.slice(0, 100).trim() + '...';
                    cellContent =
                        `<span title="${textContent.replace(/"/g, '&quot;')}">${shortText}</span>`;
                } else {
                    cellContent = value; // Render full HTML
                }
            }
            // 3. Detect and render typed fields
            else if (lowerCol.includes('email')) {
                cellContent = `<a href="mailto:${value}">${value}</a>`;
            } else if (lowerCol.includes('tel') || lowerCol.includes('phone') || lowerCol.includes(
                'mobile')) {
                cellContent = `<a href="tel:${value}">${value}</a>`;
            } else if (lowerCol.includes('url') || lowerCol.includes('website')) {
                const safeUrl = value.startsWith('http') ? value : `http://${value}`;
                cellContent = `<a href="${safeUrl}" target="_blank" rel="noopener">${value}</a>`;
            } else if (lowerCol.includes('address') || lowerCol.includes('description') || lowerCol
                .includes('bio')) {
                if (value.length > 100) {
                    const shortText = value.slice(0, 100).trim() + '...';
                    cellContent =
                        `<span title="${value.replace(/"/g, '&quot;')}">${shortText}</span>`;
                } else {
                    cellContent = `<address>${value}</address>`;
                }
            }
            // 4. Default fallback with truncation
            else {
                cellContent = value.length > 100 ?
                    `<span title="${value.replace(/"/g, '&quot;')}">${value.slice(0, 100).trim()}...</span>` :
                    value;
            }

            rowHtml += `<td>${cellContent}</td>`;
        });

        // Action buttons
        rowHtml += `
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-outline-primary mr-1 edit-btn" data-url="${baseUrl}/${item.id}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger ml-1 delete-btn" data-url="${baseUrl}/${item.id}" data-id="${item.id}">Delete</button>
                        </div>
                    </td>`;

        rowHtml += `</tr>`;

        rowHtml += `
            <tr class="expandable-body" style="display:none;">
              <td colspan="${columns.length + 1}">
              <p>ldsfhdsakdfhasjk hhfsadhjksda kjsfhdkjdsfh kjshfkhkh </p>
              </td>
            </tr>
        `;

        $tbody.append(rowHtml);
    });
}

// search data 
$('#searchInput').on('input', function () {
    search = $(this).val();
    fetchData(1, search);
});

function renderPagination(data) {

    $pagination.empty();
    const currentPage = data.current_page;
    const lastPage = data.last_page;

    // Helper to create a page button
    const pageButton = (page, label = null, disabled = false, active = false) => {
        if (label === '...') {
            return `<span class="mx-1">...</span>`;
        }

        return `
            <button 
                class="btn btn-sm btn-outline-secondary mx-1 page-btn ${active ? 'active' : ''}" 
                data-page="${page}" 
                ${disabled ? 'disabled' : ''}
                ${active ? 'disabled' : ''}>
                ${label ?? page}
            </button>
        `;
    };

    // Prev Button
    $pagination.append(pageButton(currentPage - 1, 'Prev', currentPage === 1));

    const pages = [];

    if (lastPage <= 10) {
        // Show all pages if total <= 10
        for (let i = 1; i <= lastPage; i++) {
            pages.push(i);
        }
    } else {
        // Always show first 5 pages
        for (let i = 1; i <= 4; i++) {
            pages.push(i);
        }

        // If currentPage > 6, show first ellipsis
        if (currentPage > 4) {
            pages.push('...');
        }

        // Show pages around current page (only if it's far from start and end)
        const middlePagesStart = Math.max(4, currentPage - 1);
        const middlePagesEnd = Math.min(lastPage - 4, currentPage + 1);

        for (let i = middlePagesStart; i <= middlePagesEnd; i++) {
            if (!pages.includes(i)) {
                pages.push(i);
            }
        }

        // If current page < lastPage - 4, show second ellipsis
        if (currentPage < lastPage - 4) {
            pages.push('...');
        }

        // Always show last 2 pages
        for (let i = lastPage - 1; i <= lastPage; i++) {
            if (i > 5 && !pages.includes(i)) {
                pages.push(i);
            }
        }
    }

    // Render pages
    pages.forEach(p => {
        if (p === '...') {
            $pagination.append(pageButton(null, '...'));
        } else {
            $pagination.append(pageButton(p, null, false, p === currentPage));
        }
    });

    // Next Button
    $pagination.append(pageButton(currentPage + 1, 'Next', currentPage === lastPage));

    // "Go to page" input
    $pagination.append(`
        <br>
        <div class="my-2">
            <span class="mx-2">Go to page:</span>
            <input type="number" min="1" max="${lastPage}" id="gotoPageInput" class="form-control d-inline-block" style="width: 120px;" value="${currentPage}">
            <button class="btn btn-sm btn-outline-primary mx-1" id="gotoPageBtn">Go</button>
        </div>
    `);
}

// pagination buttons
$(document).on('click', '.page-btn', function (e) {
    e.preventDefault();
    const selectedPage = parseInt($(this).data('page'));
    if (!isNaN(selectedPage)) {
        fetchData(selectedPage, search);
    }
});

// direct to page
$(document).on('click', '#gotoPageBtn', function () {
    const inputPage = parseInt($('#gotoPageInput').val());
    if (!isNaN(inputPage) && inputPage >= 1) {
        fetchData(inputPage, search);
    }
});

$(document).on('input', '#gotoPageInput', function () {
    $(this).val($(this).val().replace(/[^\d]/g, ''));
});

// Doanlodable options
// Export options like (PDF, EXCEL, WORD)
function isTableEmpty(tableId) {

    playAudio('infoAudio');
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

    if (rows.length === 0) return true;

    if (rows.length === 1) {
        const cellText = rows[0].innerText.trim().toLowerCase();
        // You can customize this match to suit your placeholder
        if (cellText.includes('no data')) return true;
    }

    return false;
}


function exportToPDF() {

    if (isTableEmpty('dataTable')) {
        toastr.info('No data available in the table to pdf.', 'Export Skipped');
        return;
    }

    const {
        jsPDF
    } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Company Data", 14, 16);

    const rows = [];
    $('#dataTable tbody tr').each(function () {
        const row = [];
        $(this).find('td').each(function () {
            row.push($(this).text().trim());
        });
        rows.push(row);
    });

    const headerRow = ['#', ...columns.map(col => col.charAt(0).toUpperCase() + col.slice(1)), 'Action'];

    doc.autoTable({
        head: [headerRow],
        body: rows,
        startY: 20
    });

    doc.save('company-data.pdf');
}

function exportToExcel() {

    if (isTableEmpty('dataTable')) {
        toastr.info('No data available in the table to excel.', 'Export Skipped');
        return;
    }

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(document.getElementById('dataTable'));
    XLSX.utils.book_append_sheet(wb, ws, "Company Data");
    XLSX.writeFile(wb, "company-data.xlsx");
}

function exportToWord() {
    if (isTableEmpty('dataTable')) {
        toastr.info('No data available in the table to word.', 'Export Skipped');
        return;
    }

    const table = document.getElementById('dataTable').outerHTML;
    const blob = new Blob(['<html><body>' + table + '</body></html>'], {
        type: 'application/msword'
    });

    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'company-data.doc';
    a.click();
}

// Initial load DAta on table
$(document).ready(function () {
    fetchData();
});
//
// Custom DataTable(Read) operation ends here  
//
















// Custom CRUD operation using Modal (Create, Edit, Update, Delete) operation starts here 
// Configurable options (can be modified globally)
let config = {
    formId: '#form',
    modalId: '#form-modal',
    titleSelector: '.modal-title',
    urlInputId: '#url',
    methodInputName: '_method',
    submitBtnId: '#formSubmitBtn',
    createBtnClass: '.create-btn',
    editBtnClass: '.edit-btn',
    deleteBtnClass: '.delete-btn',
    checkItemClass: '.checkitem',
    checkAllBoxId: '#check_all_box',
    multipleDeleteBtnId: '#multiple_delete_btn'
};

// Success callback handlers
const handleCrudSuccess = {
    edit: (data) => {
        openModal(config, data);
        // playAudio('infoAudio');
    },
    formSubmit: (data) => {
        // Handle form submission success
        fetchData(currentPage, search, data.data.id); // reload current page with highlight
        closeModal();
        toastr.success(data.message);
        playAudio('successAudio');
    },
    delete: () => {
        fetchData();
        toastr.success('Deleted successfully');
        playAudio('successAudio');
        // setTimeout(() => window.location.reload(), 400);
    }
};

// Process AJAX success based on CRUD type
const processSuccessResponse = (data, crudType) => {
    if (crudType in handleCrudSuccess) {
        handleCrudSuccess[crudType](data);
    }
};

// Handle AJAX error response
const handleErrorResponse = ($xhr, timeout) => {
    const errorData = $xhr.responseJSON;
    if (typeof errorData.message === "string") {
        toastr.error('', errorData.message, { timeOut: timeout });
        playAudio('errorAudio');
    } else {
        let played = false;
        $.each(errorData.message, (key, message) => {
            toastr.error('', message, { timeOut: timeout });
            if (!played) { playAudio('errorAudio'); played = true; }
        });
    }
};

// Centralized AJAX function
const ajaxCall = (param) => {
    const tostrTimeOut = 3000;

    $.ajax({
        headers: {
            'Authorization': `Bearer ${$('meta[name="bearer-token"]').attr('content')}`,
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        type: param.type,
        url: param.url,
        dataType: param.dataType,
        data: param.data,
        success: (response) => {
            response.tostrTimeOut = tostrTimeOut;
            if (param.successCallback) {
                param.successCallback(response);
            } else if (param.crud) {
                processSuccessResponse(response, param.crud);
            }
        }
    }).fail(($xhr) => {
        if (param.errorCallback) {
            param.errorCallback($xhr);
        } else {
            handleErrorResponse($xhr, tostrTimeOut);
        }
    });
};

function toggleNotes() {
    const shortN = document.getElementById('shortNotes');
    const fullN = document.getElementById('fullNotes');
    const link = event.target;

    if (fullN.style.display === 'none') {
        fullN.style.display = 'inline';
        shortN.style.display = 'none';
        link.textContent = 'See less';
    } else {
        fullN.style.display = 'none';
        shortN.style.display = 'inline';
        link.textContent = 'See more';
    }
}

// Modal open handler (Create/Edit)
const openModal = (config, response) => {

    const $modal = $(config.modalId);
    const $form = $(config.formId);
    const $title = $modal.find(config.titleSelector);

    initSelect2WithAjaxCall($modal);

    if (typeof response !== "string" && Object.keys(response).length > 0) {
        $title.text("Edit Form");

        //  $('#url').val(response.data.update_url || response.data.url || `${baseUrl}/${response.data.id}`);
        $(config.urlInputId).val(`${baseUrl}/${response.data.id}`);

        $.each(response.data, (key, value) => {
            const $field = $(`[name="${key}"]`);
            if (!$field.length) return;

            if ($field.hasClass('select2-ajax')) {
                // For select2 ajax fields, need to add option with id and text manually if missing
                if (!$field.find(`option[value="${value}"]`).length) {
                    // Try to get text from nested object with same key prefix
                    let displayText = value; // fallback text

                    // Extract base name (remove _id suffix)
                    const baseKey = key.replace(/_id$/, '');

                    // Try get display text from nested object e.g. investment_partner.name
                    if (response.data[baseKey] && response.data[baseKey].name) {
                        displayText = response.data[baseKey].name;
                    }

                    const newOption = new Option(displayText, value, true, true);
                    $field.append(newOption).trigger('change');
                } else {
                    $field.val(value).trigger('change');
                }
            } else {
                // Normal fields
                $field.val(value);
            }
        });


        if (!$form.find(`input[name="${config.methodInputName}"]`).length) {
            $form.append(`<input type="hidden" name="${config.methodInputName}" value="PUT">`);
        }
    } else {
        $(config.urlInputId).val(response);
        $title.text("Create Form");
    }

    $modal.modal('show');
};

// Modal close handler (reset form)
function closeModal() {

    const $form = $(config.formId);
    const $modal = $(config.modalId);
    const $title = $modal.find(config.titleSelector);

    if ($title.length) $title.text("Add ");

    if ($form.length) {
        $form.trigger('reset');
        $form.find('input[type="text"], input[type="number"], input[type="hidden"], textarea').val('');
        $form.find('select').prop('selectedIndex', 0);
        $form.find(`input[name="${config.methodInputName}"]`).remove();

        if (config.urlInputId) $(config.urlInputId).val('');
    }
    if ($modal.length) $modal.modal('hide');

    console.log('Form element:', $form.length, $form);
    console.log('Modal element:', $modal.length, $modal);
    console.log('Title element:', $title.length, $title);
}



$(document).ready(function () {

    toastr.options = {
        "closeButton": false,                // Show 'X' to close
        "debug": true,                     // Useful for development; keep false in prod
        "newestOnTop": true,                // New toasts appear above old ones
        "progressBar": true,                // Show a loading bar
        "positionClass": "toast-top-right", // Position on screen
        "preventDuplicates": true,          // Avoid spamming the same toast
        "onclick": null,                    // You can bind a click event if needed
        "showDuration": "300",              // How fast to show (ms)
        "hideDuration": "1000",             // How fast to hide (ms)
        "timeOut": "3000",                  // Auto-hide after 3s
        "extendedTimeOut": "1000",          // Hovered timeout
        "showEasing": "swing",              // Animation when showing
        "hideEasing": "linear",             // Animation when hiding
        "showMethod": "fadeIn",             // jQuery method to show
        "hideMethod": "fadeOut",            // jQuery method to hide
        "tapToDismiss": true                // Allow click to dismiss
    };

    // Event bindings
    $(document).on('click', config.createBtnClass, function () {
        const url = $(this).attr('data-url');
        openModal(config, url);
    });

    $(document).on('click', config.editBtnClass, function () {
        ajaxCall({
            type: 'GET',
            url: $(this).data('url'),
            dataType: 'JSON',
            crud: 'edit'
        });
    });

    $(document).on('click', config.submitBtnId, function (e) {
        e.preventDefault();
        ajaxCall({
            type: $(`${config.formId} input[name="${config.methodInputName}"]`).val() || $(config.formId).attr('method'),
            url: $(config.urlInputId).val(),
            dataType: 'JSON',
            data: $(config.formId).serialize(),
            crud: 'formSubmit'
        });
    });

    $(document).on('click', config.deleteBtnClass, function () {
        playAudio('warningAudio');

        setTimeout(() => {
            if (confirm('Are you sure you want to delete this task?')) {
                ajaxCall({
                    type: 'DELETE',
                    url: $(this).data('url'),
                    dataType: 'JSON',
                    data: { ids: $(this).data('id') },
                    crud: 'delete'
                });
                $(this).closest('tr').remove();
            }
        }, 200);
    });

    // single item boxes, each row on table
    $(document).on('change', config.checkItemClass, function () {
        if (!$(config.checkItemClass).length) return;

        const checked = $(config.checkItemClass + ":checked").length > 0;
        $(config.multipleDeleteBtnId).toggleClass('d-none', !checked);
        $(config.checkAllBoxId).prop('checked', checked);
    });

    // total table rows select bx 
    $(config.checkAllBoxId).click(function () {
        const isChecked = $(this).is(':checked');
        $(config.checkItemClass).prop('checked', isChecked);
        $(config.multipleDeleteBtnId).toggleClass('d-none', !isChecked);
    });

    $(config.multipleDeleteBtnId).on('click', function () {
        playAudio('warningAudio');

        const selectedIds = $("input:checkbox[name=id]:checked").map(function () {
            return $(this).val();
        }).get();

        if (confirm('Are you sure you want to delete these tasks?')) {
            ajaxCall({
                type: 'DELETE',
                url: $(this).data('url'),
                dataType: 'JSON',
                data: { ids: selectedIds },
                crud: 'delete'
            });
        }
    });

    $(document).on('hidden.bs.modal', config.modalId, closeModal); // end ajax crud process

});

// Generic play function
function playAudio(id) {
    const audio = document.getElementById(id);
    if (audio) {
        audio.play().catch(err => {
            console.warn("Audio playback failed:", err);
        });
    }
}
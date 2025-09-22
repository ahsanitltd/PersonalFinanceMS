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
                            <table class="table table-hover table-head-fixed text-nowrap" id="dataTable"
                                data-url="{{ route('api-job-earning-data.index') }}"
                                data-columns='["company_name", "amount", "earn_month", "is_paid"]'>

                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            <button type="button" class="btn btn-sm btn-outline-primary create-btn w-100"
                                                data-url="{{ route('api-job-earning-data.store') }}" data-toggle="modal"
                                                data-target="form-modal">
                                                <i class="fas fa-plus"></i> Create
                                            </button>
                                        </th>
                                        <th colspan="2">
                                            <button id="multiple_delete_btn" class="btn btn-outline-danger mr-2 d-none"
                                                type="submit" data-url="{{ route('api-job-earning-data.destroy', 0) }}">
                                                Delete all
                                            </button>
                                            <button class="btn btn-outline-success mr-1" onclick="exportToExcel()">
                                                <i class="fas fa-download"></i> Excel
                                            </button>
                                            <button class="btn btn-outline-danger mx-1" onclick="exportToPDF()">
                                                <i class="fas fa-download"></i> PDF
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" onclick="exportToWord()">
                                                <i class="fas fa-download"></i> Word
                                            </button>
                                        </th>
                                        <th colspan="3">
                                            <input class="form-control" type="search" placeholder="Search"
                                                id="searchInput">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center" style="vertical-align: top;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="check_all_box" />
                                            </div>
                                        </th>
                                        <th>Company</th>
                                        <th>Amount</th>
                                        <th>Month</th>
                                        <th>Paid</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <!-- Loaded via AJAX -->
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

    @include('admin.jobEarning.form-modal')
@endsection

@section('custom-js')
@endsection

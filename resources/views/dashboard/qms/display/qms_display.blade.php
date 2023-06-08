@extends('dashboard.qms.display.qms_layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!--begin::Content-->
<div class="docs-content d-flex flex-column flex-column-fluid" id="kt_docs_content">
    <!--begin::Container-->
    <div class="px-5 mx-5" id="kt_docs_content_container">

        <!-- <div class="row">
            <table style="width: 100%; border: 1px solid black">
                <tbody>
                    <tr>
                        <td style="border: 1px solid black"> ANTRIAN LABORATORIUM KLINIK</td>
                    </tr>
                </tbody>
            </table>
        </div> -->
        <br>
        <div class="row">
            <!--PRE ANALYTICS-->
            <div class="col-md-4 col-lg-12 col-xl-4">
                <!--begin::Col header-->
                <div class="mb-9">
                    <div class="d-flex flex-stack">
                        <div class="fw-bolder fs-1 pt-15">
                            DALAM ANTRIAN
                        </div>
                    </div>
                    <div class="h-3px w-100 bg-warning"></div>
                </div>
                <!--end::Col header-->
                <!--begin::Card-->
                <div class="card mb-6 mb-xl-9">
                    <!-- begin::Content -->
                    <table class="table table-striped align-middle table-row-dashed fs-6 gy-5" cellpadding="0" width="100%" style="width:100%; font-size: 15px; font-weight: bold; text-align: left;">
                        <thead style="text-transform: uppercase; font-size: 20px; font-weight: bold;">
                            <th width="10%" style="text-align:left;">No.</th>
                            <th width="60%" style="text-align:left;">Nama</th>
                            <th width="30%" style="text-align:left;">No. Lab</th>
                        </thead>
                    </table>
                    <div class="card-content table-responsive table-full-width" style="height:55vh; overflow-y:auto; overflow-x:hidden" id="table_pre">
                        <table id="table_1" class="table table-striped align-middle table-row-dashed fs-6 gy-5" cellspacing="0" width="100%" style="width:100%; font-size: 20px; font-weight: bold;">
                            <tbody id="data_1">
                            </tbody>
                        </table>
                    </div>
                    <!-- end::Content -->
                </div>
            </div>
            <!--END PRE ANALYTICS-->

            <!--ANALYTICS-->
            <div class="col-md-4 col-lg-12 col-xl-4">
                <!--begin::Col header-->
                <div class="mb-9">
                    <div class="d-flex flex-stack">
                        <div class="fw-bolder fs-1 pt-15">
                            SEDANG PROSES PEMERIKSAAN
                        </div>
                    </div>
                    <div class="h-3px w-100 bg-primary"></div>
                </div>
                <!--end::Col header-->
                <!--begin::Card-->
                <div class="card mb-6 mb-xl-9">
                    <!-- begin::Content -->
                    <table class="table table-striped align-middle table-row-dashed fs-6 gy-5" cellpadding="0" width="100%" style="width:100%; font-size: 15px; font-weight: bold; text-align: left;">
                        <thead style="text-transform: uppercase; font-size: 20px; font-weight: bold;">
                            <th width="7%" style="text-align:left;">No.</th>
                            <th width="45%" style="text-align:left;">Nama</th>
                            <th width="20%" style="text-align:left;">No. Lab</th>
                            <th width="20%" style="text-align:left;">Status</th>
                        </thead>
                    </table>
                    <div class="card-content table-responsive table-full-width" style="height:55vh; overflow-y:auto; overflow-x:hidden" id="table_proses">
                        <table id="table_2" class="table table-striped align-middle table-row-dashed fs-6 gy-5" cellspacing="0" width="100%" style="width:100%; font-size: 20px; font-weight: bold;">
                            <tbody id="data_2">
                            </tbody>
                        </table>
                    </div>
                    <!-- end::Content -->
                </div>
            </div>
            <!--END ANALYTICS-->

            <!--POST ANALYTICS-->
            <div class="col-md-4 col-lg-12 col-xl-4">
                <!--begin::Col header-->
                <div class="mb-9">
                    <div class="d-flex flex-stack">
                        <div class="fw-bolder fs-1 pt-15">
                            PEMERIKSAAN SELESAI
                        </div>
                    </div>
                    <div class="h-3px w-100 bg-success"></div>
                </div>
                <!--end::Col header-->
                <!--begin::Card-->
                <div class="card mb-6 mb-xl-9">
                    <!-- begin::Content -->
                    <table class="table table-striped align-middle table-row-dashed fs-6 gy-5" cellpadding="0" width="100%" style="width:100%; font-size: 15px; font-weight: bold; text-align: left;">
                        <thead style="text-transform: uppercase; font-size: 20px; font-weight: bold;">
                            <th width="7%" style="text-align:left;">No.</th>
                            <th width="45%" style="text-align:left;">Nama</th>
                            <th width="20%" style="text-align:left;">No. Lab</th>
                        </thead>
                    </table>
                    <div class="card-content table-responsive table-full-width" style="height:55vh; overflow-y:auto; overflow-x:hidden" id="table_selesai">
                        <table id="table_2" class="table table-striped align-middle table-row-dashed fs-6 gy-5" cellspacing="0" width="100%" style="width:100%; font-size: 20px; font-weight: bold;">
                            <tbody id="data_3">
                            </tbody>
                        </table>
                    </div>
                    <!-- end::Content -->
                </div>
            </div>
            <!--END POST ANALYTICS-->

        </div>

    </div>
    <!--end::Container-->


</div>
<!--end::Content-->

@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/qms/index_display.js')}}"></script>
@endsection
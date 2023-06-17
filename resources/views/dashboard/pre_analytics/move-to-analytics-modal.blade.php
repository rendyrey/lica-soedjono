<div class="modal fade" id="move-to-analytics-modal">
  <div class="modal-dialog modal-xl">
    <form action="{{url('pre-analytics/go-to-analytics-bulk')}}" method="post" id="move-to-analytics-bulk-form">
      <div class="modal-content">
          <div class="modal-header">
              <h2 class="modal-title">Move to analytics</h2>

              <!--begin::Close-->
              <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                  <span class="fs-4">&times;</span>
              </div>
              <!--end::Close-->
          </div>

          <div class="modal-body row">
            <!--begin::CRUD-->
            <div class="col-sm-12">
              
              <div class="py-5">
                  <!--begin::Wrapper-->
                  <div class="d-flex flex-stack flex-wrap mb-5">
                    <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                        <h3>Select transactions you want to be moved to analytics</h3>
                    </div>
                      <!--begin::Search-->
                      <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                          <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                          <span class="svg-icon svg-icon-1 position-absolute ms-6">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                  <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                  <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                              </svg>
                          </span>
                          <!--end::Svg Icon-->
                          
                      </div>
                      <!--end::Search-->

                      <!--begin::Group actions-->
                      <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                          <div class="fw-bolder me-5">
                          <span class="me-2" data-kt-docs-table-select="selected_count"></span>Selected</div>
                          <button type="button" class="btn btn-danger" data-kt-docs-table-select="delete_selected">Selection Action</button>
                      </div>
                      <!--end::Group actions-->
                  </div>
                  <!--end::Wrapper-->
                  <!--begin::Datatable-->
                  <table class="table gy-1 align-middle table-striped px-0 move-to-analytics-table">
                        <thead>
                            <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="text-start min-w-80px">Date</th>
                                <th>Transaction ID</th>
                                <th class="text-start">Lab No</th>
                                <th class="text-start">Medrec</th>
                                <th class="text-start">Name</th>
                                <th class="text-start">Room</th>
                                <th class="text-end min-w-50px">Move?</th>
                            </tr>
                        </thead>
                        
                            @csrf
                            <tbody class="fw-bold"></tbody>
                        
                    </table>
                  <!--end::Datatable-->
              </div>
            </div>
            <!--end::CRUD-->
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="move-to-analytics-bulk-btn" onClick="moveToAnalyticsBulk()">
                    <span class="indicator-label">
                        Move To Analytics
                    </span>
                </button>
            </div>
          </div>
      </div>
      </form>

  </div>
</div>
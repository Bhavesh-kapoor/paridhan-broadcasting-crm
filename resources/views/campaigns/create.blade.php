@extends('layouts.app_layout')

@section('style')
    <style>
        .recipient-list {
            max-height: 18rem;
            overflow-y: auto;
        }

        .small-note {
            font-size: .85rem;
        }
        .recipient-list .form-check{
            margin-left: 5px;
        }
    </style>
@endsection

@section('wrapper')
    <div class="page-wrapper">
        <div class="page-content">

            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Campaign Management</div>
                <div class="ps-3">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active">Create Campaign</li>
                    </ol>
                </div>

                <div class="ms-auto">
                    <a href="{{ route('campaigns.index') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bx bxs-megaphone"></i>&nbsp;All Campaigns
                    </a>
                </div>
            </div>

            <!-- Create Campaign Form -->
            <form id="campaignForm" action="{{ route('campaigns.store') }}" method="POST">
                @csrf

                <div class="row mt-4">
                    <!-- Left Side: Campaign Info -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="fw-semibold text-dark d-flex align-items-center mb-0 py-2">
                                    <i class="bx bxs-megaphone text-primary me-2"></i>Campaign Information
                                </h6>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label">Campaign Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="Enter campaign name">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Campaign Subject <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="subject"
                                            placeholder="Enter campaign subject">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="type">
                                            <option value="">Select type</option>
                                            <option value="email">Email Campaign</option>
                                            <option value="sms">SMS Campaign</option>
                                            <option value="whatsapp">WhatsApp Campaign</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Schedule Date (Optional)</label>
                                        <input type="datetime-local" class="form-control" name="scheduled_at">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="message" rows="7" placeholder="Enter campaign message..."></textarea>
                                        <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Max 5000
                                            characters</small>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Recipients -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="fw-semibold text-dark d-flex align-items-center mb-0 py-2">
                                    <i class="lni lni-users text-primary me-2"></i>Select Recipients
                                </h6>
                            </div>

                            <div class="card-body">

                                <!-- Tabs -->
                                <ul class="nav nav-tabs nav-fill mb-3" id="recipientTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="exhibitors-tab" data-bs-toggle="tab"
                                            data-bs-target="#exhibitorsTab" type="button" role="tab"
                                            aria-controls="exhibitorsTab" aria-selected="true">
                                            <i class="bx bx-store-alt"></i> Exhibitors
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="visitors-tab" data-bs-toggle="tab"
                                            data-bs-target="#visitorsTab" type="button" role="tab"
                                            aria-controls="visitorsTab" aria-selected="false">
                                            <i class="bx bx-user"></i> Visitors
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    <!-- Exhibitors TAB -->
                                    <div class="tab-pane fade show active" id="exhibitorsTab" role="tabpanel"
                                        aria-labelledby="exhibitors-tab">
                                        <div class="d-flex justify-content-between mb-2 align-items-center">
                                            <span class="fw-semibold">Select exhibitors:</span>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    id="selectPageExhibitors">Select All</button>
                                            </div>
                                        </div>

                                        <div id="exhibitorsContainer" class="recipient-list"></div>
                                    </div>

                                    <!-- Visitors TAB -->
                                    <div class="tab-pane fade" id="visitorsTab" role="tabpanel"
                                        aria-labelledby="visitors-tab">
                                        <div class="d-flex justify-content-between mb-2 align-items-center">
                                            <span class="fw-semibold">Select visitors:</span>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    id="selectPageVisitors">Select All</button>
                                            </div>
                                        </div>

                                        <div id="visitorsContainer" class="recipient-list"></div>
                                    </div>
                                </div>

                                <!-- Selected Count -->
                                <div class="border-top pt-3 mt-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="fw-semibold">Selected Recipients:</span>
                                            <div class="small-note text-muted">Selections persist when you change pages
                                            </div>
                                        </div>
                                        <span class="badge bg-primary align-self-center" id="selectedCount">0</span>
                                    </div>
                                </div>

                            </div>
                        </div>



                    </div>
                    <!-- Action Buttons -->
                    <div class="col-12">
                        <div class="card ">
                            <div class="card-body d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary d-flex align-items-center" id="submitBtn">
                                    <i class="bx bx-pencil"></i>&nbsp;Create Campaign
                                </button>

                                <a href="{{ route('campaigns.index') }}"
                                    class="btn btn-outline-danger d-flex align-items-center">
                                    <i class="bx bx-x"></i>&nbsp;Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        /* ========================================================
           GLOBAL STATE
        ======================================================== */
        let selectedRecipients = new Set();

        let exhibitorsToggle = false;
        let visitorsToggle = false;

        /* ========================================================
           UPDATE SELECT COUNT
        ======================================================== */
        function updateSelectedCount() {
            $("#selectedCount").text(selectedRecipients.size);

            $("#selectPageExhibitors").text('Select All');
            $("#selectPageVisitors").text('Select All');
        }

        /* ========================================================
           LOAD EXHIBITORS
        ======================================================== */
        function loadExhibitors(page = 1) {
            $.get("{{ route('ajax.exhibitors') }}", {
                page
            }, function(html) {
                $("#exhibitorsContainer").html(html);
                restoreSelections();
            });
        }

        /* ========================================================
           LOAD VISITORS
        ======================================================== */
        function loadVisitors(page = 1) {
            $.get("{{ route('ajax.visitors') }}", {
                page
            }, function(html) {
                $("#visitorsContainer").html(html);
                restoreSelections();
            });
        }

        /* ========================================================
           RESTORE SELECTIONS AFTER PAGINATION
        ======================================================== */
        function restoreSelections() {
            $(".recipient-checkbox").each(function() {
                const id = String($(this).data("id"));
                $(this).prop("checked", selectedRecipients.has(id));
            });

            updateSelectedCount();
        }

        /* ========================================================
           SINGLE CHECKBOX
        ======================================================== */
        $(document).on("change", ".recipient-checkbox", function() {
            const id = String($(this).data("id"));
            const type = $(this).data("type");

            if ($(this).is(":checked")) {
                selectedRecipients.add(id);
            } else {
                selectedRecipients.delete(id);

                if (type === "exhibitor") exhibitorsToggle = false;
                if (type === "visitor") visitorsToggle = false;
            }

            updateSelectedCount();
        });

        /* ========================================================
           SELECT ALL EXHIBITORS
        ======================================================== */
        $("#selectPageExhibitors").on("click", function() {
            exhibitorsToggle = !exhibitorsToggle;

            $.get("{{ route('ajax.exhibitors.all') }}", function(data) {
                data.forEach(id => {
                    if (exhibitorsToggle) selectedRecipients.add(String(id));
                    else selectedRecipients.delete(String(id));
                });

                restoreSelections();
                updateSelectedCount();
            });
        });

        /* ========================================================
           SELECT ALL VISITORS
        ======================================================== */
        $("#selectPageVisitors").on("click", function() {
            visitorsToggle = !visitorsToggle;

            $.get("{{ route('ajax.visitors.all') }}", function(data) {
                data.forEach(id => {
                    if (visitorsToggle) selectedRecipients.add(String(id));
                    else selectedRecipients.delete(String(id));
                });

                restoreSelections();
                updateSelectedCount();
            });
        });

        /* ========================================================
           PAGINATION CLICK
        ======================================================== */
        $(document).on("click", ".recipient-list .pagination a", function(e) {
            e.preventDefault();
            const page = new URL($(this).attr("href")).searchParams.get("page") || 1;

            if ($("#exhibitors-tab").hasClass("active")) {
                loadExhibitors(page);
            } else {
                loadVisitors(page);
            }
        });

        /* ========================================================
           FORM SUBMIT (FIXED VERSION)
        ======================================================== */
        $("#campaignForm").validate({
            errorClass: "text-danger validation-error",
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                subject: {
                    required: true,
                    minlength: 3
                },
                type: {
                    required: true
                },
                message: {
                    required: true,
                    minlength: 5,
                    maxlength: 5000
                },
                scheduled_at: {
                    required: false,
                    futureDate: true
                },
            },

            submitHandler: function(form, event) {
                event.preventDefault();

                if (selectedRecipients.size === 0) {
                    toastr.error("Please select at least one recipient.");
                    return;
                }

                let formData = new FormData(form);

                selectedRecipients.forEach(id => formData.append("recipients[]", id));

                $.ajax({
                    url: $("#campaignForm").attr("action"), // FIXED
                    type: 'POST',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json",

                    success: function(response) {
                        if (response.status === true) {

                            Swal.fire({
                                icon: "success",
                                title: "Campaign Created!",
                                html: response.message,
                                showCancelButton: true,
                                confirmButtonText: "Add More",
                                cancelButtonText: "Go to List"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('campaigns.create') }}";
                                } else {
                                    window.location.href = "{{ route('campaigns.index') }}";
                                }
                            });

                        } else if (response.status === "validation_error") {

                            Swal.fire({
                                icon: "error",
                                title: "Validation Error",
                                html: response.message
                            });

                        } else {

                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.message || "Something went wrong."
                            });

                        }
                    },

                    error: function(xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Server Error",
                            text: xhr.responseJSON?.message || "Please try again later."
                        });
                    }
                });
            }
        });

        $.validator.addMethod("futureDate", function(value, element) {
            if (!value) return true; // optional field
            let selected = new Date(value);
            let now = new Date();
            return selected > now;
        }, "Selected date and time must be in the future.");
        /* ========================================================
           INITIAL LOAD
        ======================================================== */
        $(function() {
            loadExhibitors();
            loadVisitors();
        });
    </script>
@endsection

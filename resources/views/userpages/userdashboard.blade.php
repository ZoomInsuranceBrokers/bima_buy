@extends('layouts.app')

@section('content')

    <div class="content-wrapper">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div id="updateLeadDetails">

            @foreach ($lead_details as $lead)
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div>
                        <strong>{{ $lead->first_name . ' ' . $lead->last_name }}'s documents are incomplete.</strong> Please
                        <a href="{{ route('user.show.form.to.updateLead', ['id' => Crypt::encrypt($lead->id)]) }}"
                            class="alert-link">click here</a> to update
                        and upload the details.
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pending Leads</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tracking ID</th>
                                        <th>Customer Name</th>
                                        <th>Verified by Zonalmanger</th>
                                        <th>Verified by Retail</th>
                                        <th>Quote Details</th>
                                        <th>Payment Status</th>
                                        <th>Final Status</th>
                                        <th>Remarks</th>
                                        <th>Last Update</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($pending_lead_details as $lead)
                                                                    <tr>
                                                                        <td> {{ $lead->id }} </td>
                                                                        <td>{{ $lead->first_name . ' ' . $lead->last_name }}</td>
                                                                        <td>
                                                                            @if ($lead->is_cancel)
                                                                                <label class="badge badge-danger">Cancel</label>
                                                                            @elseif($lead->is_zm_verified)
                                                                                <label class="badge badge-success">verified</label>
                                                                            @else
                                                                                <label class="badge badge-warning">Pending</label>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($lead->is_cancel)
                                                                                <label class="badge badge-danger">Cancel</label>
                                                                            @elseif($lead->is_retail_verified)
                                                                                <label class="badge badge-success">Verified</label>
                                                                            @else
                                                                                <label class="badge badge-warning">Pending</label>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($lead->is_cancel)
                                                                                <label class="badge badge-danger">Cancel</label>
                                                                            @elseif($lead->is_accepted)
                                                                                @foreach ($lead->quotes as $qote)
                                                                                    @if ($qote->is_accepted)
                                                                                        <label class="badge badge-success">Accepted at ₹{{$qote->price}}</label>
                                                                                        @break
                                                                                    @endif
                                                                                @endforeach
                                                                            @elseif(!$lead->quotes->isEmpty())
                                                                                <button type="button" class="btn btn-gradient-info btn-sm"
                                                                                    onclick="showQuoteDetails({{$lead->id}})">View Details</button>
                                                                            @else
                                                                                <label class="badge badge-warning">Pending</label>
                                                                            @endif
                                                                        </td>

                                                                        <td>
                                                                            @if ($lead->is_cancel)
                                                                                <label class="badge badge-danger">Cancel</label>

                                                                            @elseif(
                                                                                $lead->is_accepted && !empty($lead->payment_link) && empty($lead->payment_receipt)
                                                                            )
                                                                                                                    <button type="button" class="badge badge-danger"
                                                                                                                        onclick="uploadPaymentScreeShort({{$lead->id}})">
                                                                                                                        <i class="mdi mdi-upload btn-icon-prepend"></i> Upload Screen Short
                                                                                                                    </button>
                                                                            @elseif($lead->is_payment_complete)
                                                                                <label class="badge badge-success">Paid</label>
                                                                            @else
                                                                                <label class="badge badge-warning">Pending</label>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($lead->is_cancel)
                                                                                <label class="badge badge-danger">Cancel</label>
                                                                            @elseif($lead->final_status)
                                                                                <label class="badge badge-success">Completed</label>
                                                                            @else
                                                                                <label class="badge badge-warning">Pending</label>
                                                                            @endif
                                                                        </td>
                                                                        <td> <button type="button" class="btn btn-gradient-info btn-sm"
                                                                                onclick="showRemarks({{$lead->id}})">View</button>
                                                                        </td>
                                                                        <td>
                                                                            @if(!empty($lead->quotes) && $lead->quotes->isNotEmpty())
                                                                                <span>{{ $lead->quotes->first()->updated_at->format('M d, Y h:i A') }}</span>
                                                                            @else
                                                                                <span>{{ $lead->updated_at->format('M d, Y h:i A') }}</span>
                                                                            @endif
                                                                        </td>

                                                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for showing quote details -->
    <div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quoteModalLabel">Quote Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="quotesContainer" class="mb-4 row"></div>

                    <!-- Action Form -->
                    <div id="quoteActionForm" style="display: none;">
                        <label for="quoteAction">Choose an action</label>
                        <select id="quoteAction" class="form-control" onchange="handleActionChange()">
                            <option value="">-- Select --</option>
                            <option value="accept">Accept Quote</option>
                            <option value="ask_for_another">Ask for another Quote</option>
                            <option value="cancel">Cancel</option>
                        </select>

                        <div id="additionalDocuments" style="display: none;" class="shadow-lg rounded p-4 mt-3 bg-light ">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="aadharCardFront" class="form-label">Upload Aadhar Card Front Page<span
                                                class="text-danger">*</span></label>
                                        <input type="file" id="aadharCardFront" class="form-control" accept="image/*,.pdf"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="aadharCardBack" class="form-label">Upload Aadhar Card Backside Page<span
                                                class="text-danger">*</span></label>
                                        <input type="file" id="aadharCardBack" class="form-control" accept="image/*,.pdf"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="panCard" class="form-label">Upload PAN Card<span
                                        class="text-danger">*</span></label>
                                <input type="file" id="panCard" class="form-control" accept="image/*,.pdf" required>
                            </div>
                        </div>
                        <div class="form-group d-none mt-3" id="insufficientDetailsMessage">
                            <label for="insufficientMessage">Enter your message:</label>
                            <textarea id="insufficientMessage" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="submitQuoteAction()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  Upload Payment Screen Short Model -->
    <div class="modal fade" id="uploadPaymentScreeShortModal" tabindex="-1" role="dialog"
        aria-labelledby="uploadPaymentScreeShortModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPaymentScreeShortModalLabel">Upload Payment Screen Short</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadPolicyCopyForm" enctype="multipart/form-data">
                        @csrf
                        <div id="paymentLinkDisplay" class="mb-3" style="display: none;"></div>
                        <div class="form-group">
                            <label for="paymentScreenShort">Upload Payment Screen Short</label>
                            <input type="file" class="form-control" id="paymentScreenShort" name="paymentScreenShort"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="paymentAction">Choose an action</label>
                            <select id="paymentAction" name="paymentAction" class="form-control"
                                onchange="paymentActionChange()">
                                <option value="">-- Select --</option>
                                <option value="upload">Payment screen short uploaded</option>
                                <option value="resend_payment_link">Resend Payment Link or Payment Link is Expire</option>
                                <option value="cancel">Cancel</option>
                            </select>
                            <div class="form-group d-none mt-3" id="insufficientDetailsMessage2">
                                <label for="insufficientMessage2">Enter your message:</label>
                                <textarea id="insufficientMessage2" name="insufficientDetailsMessage2" class="form-control"
                                    rows="3"></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitPaymentScreeShort()">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Remark Modal -->

    <div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-labelledby="remarksModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- You can use 'modal-lg' for a larger modal -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarksModalLabel">Lead Remarks</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Remarks Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="remarksTableBody">
                                <!-- Dynamic Content Goes Here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>




    @push('scripts')
        <script>
            window.showQuoteDetails = function (leadId) {
                $('#preloader1').show();
                $.ajax({
                    url: '/quote-details/' + leadId,
                    type: 'GET',
                    success: function (response) {
                        $('#preloader1').hide();
                        let quotesHTML = '';
                        response.forEach(function (quote) {

                            quotesHTML += `<div class="col-md-4 mb-3"> 
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <h5 class="card-title">${quote.quote_name}</h5>
                                                                            <p><strong>Document:</strong> ${quote.file_path ? `<a href="${quote.temporary_url}" target="_blank">View</a>` : 'No file attached'}</p>
                                                                            <p><strong>Insured Declared Value:</strong> ₹${quote.vehicle_idv}</p>
                                                                            <p><strong>OD Premium:</strong> ₹${quote.od_premium}</p>
                                                                            <p><strong>TP Premium</strong> ₹${quote.tp_premium}</p>
                                                                            <p><strong>Final Premium:</strong> ₹${quote.price}</p>
                                                                            <p><strong>Status:</strong> ${quote.is_accepted ? 'Accepted' : 'Pending'}</p>
                                                                            <p><strong>Remarks:</strong> ${quote.remarks ? quote.remarks : 'No Remarks'}</p>
                                                                            <input type="checkbox" class="quoteCheckbox" data-quote-id="${quote.id}" />
                                                                        </div>
                                                                    </div>
                                                                </div>`;
                        });

                        // Insert generated HTML into the modal
                        $('#quotesContainer').html(quotesHTML);

                        // Show action form and modal
                        $('#quoteActionForm').show();
                        $('#quoteModal').modal('show');

                        // Enforce single checkbox selection
                        $('.quoteCheckbox').on('change', function () {
                            // Uncheck all checkboxes if this one is checked
                            if ($(this).prop('checked')) {
                                $('.quoteCheckbox').not(this).prop('checked', false);
                            }
                        });
                    }
                });
            }

            function handleActionChange() {
                var action = $('#quoteAction').val();
                if (action === "accept") {
                    $('#insufficientDetailsMessage').addClass('d-none');
                    // Show additional fields for Aadhar and PAN Card
                    $('#additionalDocuments').show();
                } else {
                    // Hide the fields when another action is selected
                    $('#additionalDocuments').hide();
                    $('#insufficientDetailsMessage').removeClass('d-none');
                }
            }
            // Function to handle form submission for accepting or asking for another quote
            function submitQuoteAction() {
                // Check if a checkbox is selected
                var selectedCheckbox = $('.quoteCheckbox:checked');
                if (selectedCheckbox.length === 0) {
                    // If no checkbox is selected, show an error
                    Swal.fire("Please select a quote to proceed.");
                    // alert("Please select a quote to proceed.");
                    return;
                }

                // Get the selected quote ID
                var selectedQuoteId = selectedCheckbox.data('quote-id');
                var action = $('#quoteAction').val();
                const message = $('#insufficientMessage').val();

                if (!action) {
                    Swal.fire('Please select an action to proceed.');
                    return;
                }

                if (action != 'accept' && !message.trim()) {
                    Swal.fire('Please enter a message to continue.');
                    return;
                }

                if (action === "accept") {
                    var aadharFileFront = $('#aadharCardFront')[0].files[0];
                    var aadharFileBack = $('#aadharCardBack')[0].files[0];
                    var panFile = $('#panCard')[0].files[0];

                    if (!aadharFileFront || !aadharFileBack || !panFile) {
                        Swal.fire("Both Aadhar Card and PAN Card must be uploaded.");
                        return;
                    }
                } else {
                    if (!message.trim()) {
                        Swal.fire('Please enter a message to continue.');
                        return;
                    }
                }
                $('#preloader1').show();

                var formData = new FormData();
                formData.append('quote_id', selectedQuoteId);
                formData.append('action', action);
                formData.append('message', message);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('aadharCardFront', aadharFileFront);
                formData.append('aadharCardBack', aadharFileBack);
                formData.append('panCard', panFile);

                // Get the selected action (accept or ask for another quote)

                // Send the selected quote ID and action to the backend
                $.ajax({
                    url: '/user/submit-quote-action', // Adjust the backend URL as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#preloader1').hide();
                        // Handle success (e.g., show a success message)
                        Swal.fire("Form submitted successfully!").then(() => {
                            location.reload();
                        });
                        $('#quoteModal').modal('hide');
                    },
                    error: function () {
                        $('#preloader1').hide();
                        // Handle error
                        Swal.fire("An error occurred while submitting the action pls try again.");
                    }
                });
            }

            window.paymentActionChange = function () {
                var action = $('#paymentAction').val();
                if (action === "upload") {
                    $('#insufficientDetailsMessage2').addClass('d-none');

                } else if (action === "resend_payment_link") {
                    $('#insufficientDetailsMessage2').addClass('d-none');
                }
                else {
                    $('#insufficientDetailsMessage2').removeClass('d-none');
                }
            }

            window.uploadPaymentScreeShort = function (leadId) {

                $('#preloader1').show();

                $.get(`/payment/screenshort/link/${leadId}`, function (data) {
                    $('#preloader1').hide();
                    if (data.paymentLink) {

                        $('#paymentLinkDisplay').html(`<strong>Payment Link:</strong> <a href="${data.paymentLink}" target="_blank">View</a>`).show();

                    }
                })

                $('#uploadPaymentScreeShortModal').data('id', leadId).modal('show');
            };

            window.submitPaymentScreeShort = function () {

                var action = $('#paymentAction').val();
                const message = $('#insufficientMessage2').val();
                if (!action) {
                    Swal.fire('Please select an action to proceed.');
                    return;
                }

                if (action === "upload") {
                    var paymentScreenShort = $('#paymentScreenShort')[0].files[0];

                    if (!paymentScreenShort) {
                        Swal.fire("Please upload payment screen short to proceed.");
                        return;
                    }
                }
                 else if (action === "cancel") {
                    if (!message.trim()) {
                        Swal.fire('Please enter a message to continue.');
                        return;
                    }
                }

                const leadId = $('#uploadPaymentScreeShortModal').data('id');
                const formData = new FormData($('#uploadPolicyCopyForm')[0]);
                $('#preloader1').show();
                $.ajax({
                    url: `/leads/${leadId}/upload-Payment-Scree-Short`,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        $('#preloader1').hide();
                        if (response.success) {
                            $('#uploadPaymentScreeShortModal').modal('hide');
                            Swal.fire({
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(response.message);
                        }
                    },
                    error: function () {
                        $('#preloader1').hide();
                        Swal.fire('Failed to upload policy copy.');
                    }
                });
            };

            window.showRemarks = function (leadId) {
                $('#preloader1').show();
                $.ajax({
                    url: '/remarks/' + leadId,
                    type: 'GET',
                    success: function (response) {
                        $('#preloader1').hide();
                        const remarks = response.remarks;
                        $('#remarksTableBody').empty();
                        remarks.forEach(function (remark) {
                            const fromName = remark.sender.first_name + ' ' + remark.sender.last_name;
                            const toName = remark.receiver.first_name + ' ' + remark.receiver.last_name;
                            const message = remark.message;
                            const createdAt = new Date(remark.created_at).toLocaleString(); // Format the date
                            const row = `
                                                                    <tr>
                                                                        <td>${fromName}</td>
                                                                        <td>${toName}</td>
                                                                        <td>${message}</td>
                                                                        <td>${createdAt}</td>
                                                                    </tr>
                                                                `;
                            $('#remarksTableBody').append(row);
                        });
                        $('#remarksModal').modal('show');
                    },
                    error: function () {
                        $('#preloader1').hide();
                        alert('Error fetching remarks data.');
                    }
                });
            };


        </script>
        <script type="module">
            window.Echo.private('updateLeadDetails.{{ Auth::user()->id }}')
                .listen('UpdateLead', (data) => {
                    console.log(data);
                    //     $('#updateLeadDetails').html(`
                    //     <div class="alert alert-warning d-flex align-items-center" role="alert">
                    //         <i class="bi bi-exclamation-circle-fill me-2"></i>
                    //         <div>
                    //             <strong>${data.update_message}'s documents are incomplete.</strong> Please
                    //             <a href="{{ route('user.show.form.to.updateLead', ['id' => '__id__']) }}"
                    //                 class="alert-link">click here</a> to update and update the details.
                    //         </div>
                    //     </div>
                    // `.replace('__id__', data.lead_id));
                });
        </script>
    @endpush

@endsection
@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Leads</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Rc Name</th>
                                    <th>Tracking ID </th>
                                    <th>Customer Name</th>
                                    <th>Verify By Zm</th>
                                    <th>Verify Retail</th>
                                    <th>Send Quote Details</th>
                                    <th>Payment Status</th>
                                    <th>Upload Policy Copy</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($pendingLeads as $lead)
                                    <tr>
                                        <td>{{$lead->user->first_name . ' ' . $lead->user->first_name}}</td>
                                        <td>{{$lead->id}}</td>
                                        <td>{{$lead->first_name . ' ' . $lead->last_name}}</td>

                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @else
                                                <label class="badge badge-success">Verifyed by
                                                    {{$lead->zonalManager->name}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->is_issue && !$lead->is_retail_verified) 
                                                <label class="badge badge-warning">Pending</label>
                                            @elseif($lead->is_retail_verified)
                                                <label class="badge badge-success">Verifyed</label>
                                            @else
                                                <button type="button" class="btn btn-gradient-info btn-sm"
                                                    onclick="getLeadDetails({{ $lead->id }})">View Details</button>
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
                                            @elseif($lead->is_retail_verified)
                                                <button type="button" class="btn btn-gradient-info btn-sm"
                                                    onclick="sendQuoteDetails({{$lead->id}})">Send Quote</button>
                                            @else
                                                <label class="badge badge-warning">Pending</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->is_payment_complete)
                                                <label class="badge badge-success">complete</label>
                                            @elseif($lead->is_accepted)
                                                <button type="button" class="btn btn-gradient-info btn-sm"
                                                    onclick="upadatePayment({{$lead->id}})">Update Payment</button>
                                            @else
                                                <label class="badge badge-warning">Pending</label>
                                            @endif
                                        </td>

                                        <td> @if ($lead->is_cancel)
                                            <label class="badge badge-danger">Cancel</label>
                                        @elseif($lead->final_status)
                                            <label class="badge badge-success">Booked</label>
                                        @elseif($lead->is_payment_complete)
                                            <button type="button" class="btn btn-gradient-info btn-sm"
                                                onclick="uploadPolicyCopy({{$lead->id}})">Upload Policy</button>
                                        @else
                                            <label class="badge badge-warning">Pending</label>

                                        @endif
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

<!-- Modal -->
<div class="modal fade" id="leadDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Details will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="submitAction" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>
<!-- Quote Modal -->
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
                <button type="button" class="btn btn-primary btn-sm mb-3" onclick="addQuoteField()">
                    <i class="md md-plus"></i> Add New Quote
                </button>

                <form id="addQuoteForm">
                    @csrf
                    <div id="quotesContainer" class="mb-4">
                        <!-- Quotes will be appended here -->
                    </div>


                    <input type="hidden" name="lead_id" id="leadIdInput" />
                    <button type="submit" class="btn btn-primary mt-3">Submit Quotes</button>
                </form>
            </div>
        </div>
    </div>


</div>
<!-- Payment Status Modal -->
<div class="modal fade" id="updatePaymentModal" tabindex="-1" role="dialog" aria-labelledby="updatePaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePaymentModalLabel">Update Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updatePaymentForm">
                    <div class="form-group">
                        <label for="paymentAction">Choose Action</label>
                        <select class="form-control" id="paymentAction" name="paymentAction">
                            <option value="" disabled selected>Select an action</option>
                            <option value="complete">Payment Complete</option>
                            <option value="notify">Send Notification</option>
                            <option value="cancel">Cancel Payment</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitPaymentAction()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Document Upload Model -->
<div class="modal fade" id="uploadPolicyCopyModal" tabindex="-1" role="dialog"
    aria-labelledby="uploadPolicyCopyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPolicyCopyModalLabel">Upload Policy Copy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadPolicyCopyForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="policyCopy">Upload Policy Copy</label>
                        <input type="file" class="form-control" id="policyCopy" name="policyCopy" required />
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitPolicyCopy()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')

    <script>
        $(document).ready(function () {
            // Fetch lead details and populate modal
            window.getLeadDetails = function (leadId) {
                $.get(`/leads/details/${leadId}`, function (data) {
                    if (data.success) {
                        let imagesHtml = '';
                        data.lead.documents.forEach((doc) => {
                            imagesHtml += `
                                                                                                <tr>
                                                                                                    <td>${doc.document_name}</td>
                                                                                                    <td><a href="${doc.file_path}" target="_blank">View</a></td>
                                                                                                </tr>
                                                                                            `;
                        });

                        $('#leadDetailsModal .modal-body').html(`
                                                                                            <div class="row">
                                                                                                <div class="col-md-6"><p><strong>First Name:</strong> ${data.lead.first_name}</p></div>
                                                                                                <div class="col-md-6"><p><strong>Last Name:</strong> ${data.lead.last_name}</p></div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6"><p><strong>Mobile No:</strong> ${data.lead.mobile_no}</p></div>
                                                                                                <div class="col-md-6"><p><strong>Vehicle No:</strong> ${data.lead.vehicle_number}</p></div>
                                                                                            </div>
                                                                                            <div class="row">
                                                                                                <div class="col-md-6"><p><strong>Email ID:</strong> ${data.lead.email ?? 'N/A'}</p></div>
                                                                                                <div class="col-md-6"><p><strong>Date of Birth:</strong> ${data.lead.date_of_birth}</p></div>
                                                                                            </div>
                                                                                            <p><strong>Documents:</strong></p>
                                                                                            <table class="table table-bordered">
                                                                                                <thead>
                                                                                                    <tr><th>File Name</th><th>Action</th></tr>
                                                                                                </thead>
                                                                                                <tbody>${imagesHtml}</tbody>
                                                                                            </table>
                                                                                            <div class="form-group">
                                                                                                <label for="action">Select Action:</label>
                                                                                                <select id="action" class="form-control">
                                                                                                    <option value="">-- Select --</option>
                                                                                                    <option value="insufficient_details">Send to user for insufficient details</option>
                                                                                                    <option value="verified">Verified</option>
                                                                                                    <option value="cancel">Cancel</option>
                                                                                                </select>
                                                                                            </div>
                                                                                            <div class="form-group d-none" id="insufficientDetailsMessage">
                                                                                                <label for="insufficientMessage">Enter your message:</label>
                                                                                                <textarea id="insufficientMessage" class="form-control" rows="3"></textarea>
                                                                                            </div>
                                                                                        `);

                        $('#leadDetailsModal').data('id', leadId).modal('show');

                        $('#action').on('change', function () {
                            if ($(this).val() === 'insufficient_details') {
                                $('#insufficientDetailsMessage').removeClass('d-none');
                            } else {
                                $('#insufficientDetailsMessage').addClass('d-none');
                            }
                        });
                    } else {
                        alert(data.message);
                    }
                });
            };

            // Handle action submission
            $('#submitAction').on('click', function () {
                const leadId = $('#leadDetailsModal').data('id');
                const action = $('#action').val();
                const message = $('#insufficientMessage').val();

                if (!action) {
                    alert('Please select an action to proceed.');
                    return;
                }

                if (action === 'insufficient_details' && !message.trim()) {
                    alert('Please provide a message for insufficient details.');
                    return;
                }

                $.post(`/leads/action/retail/${leadId}`, {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    message: action === 'insufficient_details' ? message : null
                }, function (response) {
                    if (response.success) {
                        alert('Action submitted successfully!');
                        $('#leadDetailsModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            });

            window.sendQuoteDetails = function (leadId) {
                // Fetch quote details from the server
                console.log(leadId);
                $.ajax({
                    url: `/leads/${leadId}/quotes`,
                    method: 'GET',
                    success: function (response) {
                        let quotesContainer = $('#quotesContainer');
                        quotesContainer.empty();

                        // Populate existing quotes
                        response.quotes.forEach(quote => {
                            let features = quote.description.map(feature =>
                                `<li>${feature}</li>`
                            ).join('');

                            quotesContainer.append(`
                                                                <div class="quote-item mb-4">
                                                                    <div class="d-flex justify-content-between">
                                                                        <p><strong>Policy Name:</strong> ${quote.quote}</p>
                                                                    </div>
                                                                    <p><strong>Features:</strong></p>
                                                                    <ul>${features}</ul>
                                                                     <p><strong>Price:</strong> ₹${quote.price}</p>
                                                                    <p><strong>Status:</strong> ${quote.is_accepted ? 'Accepted' : 'Pending'}</p>
                                                                </div>
                                                            `);
                        });
                        $('#leadIdInput').val(leadId);
                        // Show the modal
                        $('#quoteModal').modal('show');
                    },
                    error: function () {
                        alert('Failed to fetch quote details.');
                    }
                });
            };

            window.addFeatureField = function (quoteIndex) {
                // Append only the feature input and remove button (no price input)
                $(`#quote_${quoteIndex} .feature-fields`).append(`
                                                <div class="row feature-item mb-2">
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="quotes[${quoteIndex}][features][]" placeholder="Feature" required />
                                                    </div>
                                                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-feature">Remove</button>
                                                    </div>
                                                </div>
                                            `);
            };

            window.addQuoteField = function () {
                let quoteIndex = $('#quotesContainer .quote-item').length; // Determine the index for the new quote
                $('#quotesContainer').append(`
                                            <div class="quote-item mb-4" id="quote_${quoteIndex}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <input type="text" class="form-control" name="quotes[${quoteIndex}][quote_name]" placeholder="Quote Name or Policy Name" required />
                                                    <button type="button" class="btn btn-danger btn-sm remove-quote">Remove Quote</button>
                                                </div>
                                                <div class="form-group">
                                                    <label for="price_${quoteIndex}">Price</label>
                                                    <input type="number" class="form-control" name="quotes[${quoteIndex}][price]" id="price_${quoteIndex}" placeholder="Price" required />
                                                </div>
                                                <div class="feature-fields">
                                                    <!-- Initially, only one feature input is displayed -->
                                                    <div class="row feature-item mb-2">
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="quotes[${quoteIndex}][features][]" placeholder="Feature" required />
                                                        </div>
                                                        <div class="col-md-1">
                                                            <!-- Add button only for the first feature input -->
                                                            <button type="button" class="btn btn-success btn-sm" onclick="addFeatureField(${quoteIndex})">Add Feature</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `);
            };

            $(document).on('click', '.remove-feature', function () {
                $(this).closest('.feature-item').remove();
            });

            $(document).on('click', '.remove-quote', function () {
                $(this).closest('.quote-item').remove();
            });

            $('#addQuoteForm').on('submit', function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                $.ajax({
                    url: '/quotes',
                    method: 'POST',
                    data: formData,
                    success: function () {
                        alert('Quote added successfully!');
                        $('#quoteModal').modal('hide');
                    },
                    error: function () {
                        alert('Failed to add quote.');
                    }
                });
            });

            window.upadatePayment = function (leadId) {
                $('#updatePaymentModal').data('id', leadId).modal('show');
            };

            window.submitPaymentAction = function () {
                const leadId = $('#updatePaymentModal').data('id');
                const action = $('#paymentAction').val();

                if (!action) {
                    alert('Please select an action to proceed.');
                    return;
                }

                $.post(`/leads/payment/${leadId}`, {
                    _token: '{{ csrf_token() }}',
                    action: action
                }, function (response) {
                    if (response.success) {
                        alert('Action submitted successfully!');
                        $('#updatePaymentModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            };

            window.uploadPolicyCopy = function (leadId) {
                $('#uploadPolicyCopyModal').data('id', leadId).modal('show');
            };

            window.submitPolicyCopy = function () {
                const leadId = $('#uploadPolicyCopyModal').data('id');
                const formData = new FormData($('#uploadPolicyCopyForm')[0]);

                $.ajax({
                    url: `/leads/${leadId}/upload-policy`,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            alert('Policy copy uploaded successfully!');
                            $('#uploadPolicyCopyModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Failed to upload policy copy.');
                    }
                });
            };
        });
    </script>
@endpush

@endsection
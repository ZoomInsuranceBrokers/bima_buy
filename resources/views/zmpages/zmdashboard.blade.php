@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div id="notification"></div>
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                </li>
            </ul>
        </nav>
    </div>

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
                                    <th>RC Mobile No</th>
                                    <th>Verify By Zm</th>
                                    <th>Verify Retail</th>
                                    <th>Quote Details</th>
                                    <th>Payment Status</th>
                                    <th>Final Status</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leads as $lead)
                                    <tr>
                                        <td>{{$lead->user->first_name. ' ' . $lead->user->last_name}}</td>
                                        <td>{{$lead->id}}</td>
                                        <td>{{$lead->first_name . ' ' . $lead->last_name}}</td>
                                        <td>{{$lead->user->mobile}}</td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->is_issue && !$lead->is_zm_verified) 
                                                <label class="badge badge-warning">Pending</label>
                                            @elseif($lead->is_zm_verified)
                                                <label class="badge badge-success">Verified</label>
                                            @else
                                                <button type="button" class="btn btn-gradient-info btn-sm"
                                                    onclick="getLeadDetails({{ $lead->id }})">View Details</button>
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
                                                @foreach ($lead->quotes as $quote)
                                                    @if ($quote->is_accepted)
                                                        <label class="badge badge-success">Accepted at ₹{{$quote->price}}</label>
                                                        @break
                                                    @endif
                                                @endforeach
                                            @elseif($lead->quotes->isNotEmpty())
                                                <button type="button" class="btn btn-gradient-info btn-sm"
                                                    onclick="sendQuoteDetails({{$lead->id}})">View Details</button>
                                            @else
                                                <label class="badge badge-warning">Pending</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->is_payment_complete)
                                                <label class="badge badge-success">Complete</label>
                                            @else
                                                <label class="badge badge-warning">Pending</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->final_status)
                                                <label class="badge badge-success">Booked</label>
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
                                @empty
                                    <tr>
                                    <td colspan="8" class="text-center">No leads have been generated yet.</td>
                                    </tr>
                                @endforelse
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
                <div id="quotesContainer" class="mb-4">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            window.getLeadDetails = function (leadId) {
                // Fetch lead details
                $.get(`/zm/leads/details/${leadId}`, function (data) {
                    if (data.success) {
                        // Build the modal content dynamically
                        let imagesHtml = '';

                        // Iterate over the documents to create table rows
                        data.lead.documents.forEach((doc) => {
                            imagesHtml += `
                                                                                    <tr>
                                                                                        <td>${doc.document_name}</td>
                                                                                        <td><a href="${doc.file_path}" target="_blank">View</a></td>
                                                                                    </tr>
                                                                                `;
                        });

                        // Populate modal with lead and images details
                        $('#leadDetailsModal .modal-body').html(`
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>First Name:</strong> ${data.lead.first_name}</p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Last Name:</strong> ${data.lead.last_name}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Mobile No:</strong> ${data.lead.mobile_no}</p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Vehicle No:</strong> ${data.lead.vehicle_number}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Email ID:</strong> ${data.lead.email ?? 'N/A'}</p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Date of Birth:</strong> ${data.lead.date_of_birth}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <p><strong>Documents:</strong></p>
                                                                                <table class="table table-bordered">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>File Name</th>
                                                                                            <th>Action</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        ${imagesHtml}
                                                                                    </tbody>
                                                                                </table>
                                                                                <div class="form-group">
                                                                                    <label for="action">Select Action:</label>
                                                                                    <select id="action" class="form-control">
                                                                                        <option value="">-- Select --</option>
                                                                                        <option value="insufficient_details">Send to user for insufficient details</option>
                                                                                        <option value="verified">I verified and send to retail team</option>
                                                                                        <option value="cancel">Cancel</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group d-none" id="insufficientDetailsMessage">
                                                                                    <label for="insufficientMessage">Enter your message:</label>
                                                                                    <textarea id="insufficientMessage" class="form-control" rows="3"></textarea>
                                                                                </div>
                                                                            `);

                        // Set the lead ID as a data attribute on the modal
                        $('#leadDetailsModal').data('id', leadId);

                        // Show the modal
                        $('#leadDetailsModal').modal('show');

                        // Handle the change event for the action dropdown
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

            // Submit selected action
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

                $.post(`/leads/action/${leadId}`, {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    message: action === 'insufficient_details' ? message : null
                }, function (response) {
                    if (response.success) {
                        alert('Form submitted successfully!');
                        $('#leadDetailsModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                });
            });


            window.sendQuoteDetails = function (leadId) {

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


        });
    </script>

@endpush



@endsection
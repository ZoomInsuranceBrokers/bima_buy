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
                    <h4 class="card-title">Pending Leads</h4>
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
                                    <td>{{$lead->user->first_name . ' ' . $lead->user->last_name}}</td>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Details will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

@push('styles')
<style>
    #quotesContainer {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .quote-card {
        flex: 1 1 calc(33.33% - 20px);
        /* Make each card take 1/3 of the row */
        box-sizing: border-box;
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        background-color: #fff;
        transition: transform 0.3s ease-in-out;
    }

    .quote-card h5 {
        margin-top: 0;
        font-size: 1.25rem;
        font-weight: bold;
    }

    .quote-card p {
        margin: 5px 0;
    }

    .quote-card a {
        color: #007bff;
        text-decoration: none;
    }

    .quote-card a:hover {
        text-decoration: underline;
    }

    /* Optional: Hover effect for each card */
    .quote-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quote-fields {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
    }

    /* Media query to make the layout responsive on smaller screens */
    @media (max-width: 768px) {
        .quote-card {
            flex: 1 1 calc(50% - 20px);
            /* 2 quotes per row on medium screens */
        }
    }

    @media (max-width: 480px) {
        .quote-card {
            flex: 1 1 100%;
            /* 1 quote per row on small screens */
        }
    }
</style>
@endpush


@push('scripts')
<script>
    $(document).ready(function() {

        window.getLeadDetails = function(leadId) {
            // Fetch lead details
            $('#preloader1').show();
            $.get(`/zm/leads/details/${leadId}`, function(data) {
                $('#preloader1').hide();
                if (data.success) {
                    // Build the modal content dynamically
                    let imagesHtml = '';

                    // Iterate over the documents to create table rows
                    data.lead.documents.forEach((doc) => {
                        imagesHtml += `
                                                                                        <tr>
                                                                                            <td>${doc.document_name}</td>
                                                                                            <td><a href="{{ url('storage/${doc.file_path}') }}" target="_blank">View</a></td>
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
                                                                                            <p><strong>Vechicle Type:</strong> ${data.lead.vehicle_type}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <p><strong>Policy Type:</strong> ${data.lead.policy_type}</p>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <p><strong>Claim Staus:</strong> ${data.lead.claim_status}</p>
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
                    $('#action').on('change', function() {
                        if ($(this).val() === 'insufficient_details') {
                            $('#insufficientDetailsMessage').removeClass('d-none');
                        } else {
                            $('#insufficientDetailsMessage').addClass('d-none');
                        }
                    });
                } else {
                    Swal.fire(data.message);
                }
            });
        };

        // Submit selected action
        $('#submitAction').on('click', function() {
            const leadId = $('#leadDetailsModal').data('id');
            const action = $('#action').val();
            const message = $('#insufficientMessage').val();

            if (!action) {
                Swal.fire('Please select an action to proceed.');
                return;
            }

            if (action === 'insufficient_details' && !message.trim()) {
                Swal.fire('Please provide a message for insufficient details.');
                return;
            }

            $('#preloader1').show();

            $.post(`/leads/action/${leadId}`, {
                _token: '{{ csrf_token() }}',
                action: action,
                message: action === 'insufficient_details' ? message : null
            }, function(response) {
                $('#preloader1').hide();
                if (response.success) {
                    Swal.fire('Form submitted successfully!');
                    $('#leadDetailsModal').modal('hide');
                    location.reload();
                } else {
                    Swal.fire(response.message);
                }
            });
        });


        window.sendQuoteDetails = function(leadId) {
            $('#preloader1').show();
            $.ajax({
                url: `/leads/${leadId}/quotes`,
                method: 'GET',
                success: function(response) {
                    $('#preloader1').hide();
                    let quotesContainer = $('#quotesContainer');
                    quotesContainer.empty();

                    // Populate existing quotes
                    response.forEach(function(quote) {
                        const quoteItem = `
                                            <div class="quote-card col-md-4">
                                                <h5>${quote.quote_name}</h5>
                                                <p><strong>Price:</strong> ₹${quote.price}</p>
                                                <p><strong>Insured Declared Value:</strong> ₹${quote.vehicle_idv}</p>
                                                <p><strong>OD Premium:</strong> ₹${quote.od_premium}</p>
                                                <p><strong>TP Premium</strong> ₹${quote.tp_premium}</p>
                                                <p><strong>Status:</strong> ${quote.is_accepted ? 'Accepted' : 'Not Accepted'}</p>
                                                <p><strong>Payment Status:</strong> ${quote.payment_status ? 'Paid' : 'Unpaid'}</p>
                                                <p><strong>Document:</strong> 
                                                    ${quote.file_path ? `<a href="${quote.temporary_url}" target="_blank">View</a>` : 'No file attached'}
                                                </p>
                                            </div>
                                            <hr>
                                             `;
                        // Append the quote item to the modal list
                        quotesContainer.append(quoteItem);
                    });

                    // Show the modal
                    $('#quoteModal').modal('show');
                },
                error: function() {
                    $('#preloader1').hide();
                    Swal.fire('Failed to fetch quote details.');
                }
            });
        };


    });
</script>

@endpush



@endsection
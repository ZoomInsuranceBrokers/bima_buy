@extends('layouts.app')

@section('content')

<div class="content-wrapper">
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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @foreach ($lead_details as $lead)
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <div>
                <strong>{{ $lead->first_name . ' ' . $lead->last_name }}'s documents are incomplete.</strong> Please
                <a href="{{ route('user.show.foam.to.updateLead',  ['id' => Crypt::encrypt($lead->id)]) }}" class="alert-link">click here</a> to update
                and upload the details.
            </div>
        </div>
    @endforeach

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Leads</h4>
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
                                                <label class="badge badge-success">Verifyed</label>
                                            @else
                                                <label class="badge badge-warning">Pending</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->is_retail_verified)
                                                <label class="badge badge-success">Verifyed</label>
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
                                            @elseif($lead->is_payment_complete)
                                                <label class="badge badge-success">Paid</label>
                                            @else
                                                <label class="badge badge-warning">Pending</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lead->is_cancel)
                                                <label class="badge badge-danger">Cancel</label>
                                            @elseif($lead->is_payment_complete)
                                                <label class="badge badge-success">Completed</label>
                                            @else
                                                <label class="badge badge-warning">In Progress</label>
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
                    <select id="quoteAction" class="form-control">
                        <option value="accept">Accept Quote</option>
                        <option value="ask_for_another">Ask for another Quote</option>
                        <option value="cancel">Cancel</option>
                    </select>
                    <button type="button" class="btn btn-primary mt-3" onclick="submitQuoteAction()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        window.showQuoteDetails = function (leadId) {
            $.ajax({
                url: '/user/quote-details/' + leadId,
                type: 'GET',
                success: function (response) {
                    let quotesHTML = '';
                    response.quotes.forEach(function (quote) {
                        let featuresList = '';
                        if (Array.isArray(quote.description)) {
                            quote.description.forEach(function (feature) {
                                featuresList += `<li>${feature}</li>`;
                            });
                        } else {
                            featuresList = `<li>${quote.description}</li>`;
                        }

                        // Using Bootstrap grid to create a responsive layout
                        quotesHTML += `<div class="col-12 col-md-6 mb-3"> <!-- 12 for mobile, 4 for medium screens -->
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">${quote.quote_name}</h5>
                                                        <p><strong>Features:</strong></p>
                                                        <ul>${featuresList}</ul>
                                                        <p><strong>Price:</strong> $${quote.price}</p>
                                                        <p><strong>Status:</strong> ${quote.is_accepted ? 'Accepted' : 'Pending'}</p>
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
        // Function to handle form submission for accepting or asking for another quote
        function submitQuoteAction() {
            // Check if a checkbox is selected
            var selectedCheckbox = $('.quoteCheckbox:checked');
            if (selectedCheckbox.length === 0) {
                // If no checkbox is selected, show an error
                alert("Please select a quote to proceed.");
                return;
            }

            // Get the selected quote ID
            var selectedQuoteId = selectedCheckbox.data('quote-id');
            var action = $('#quoteAction').val(); // Get the selected action (accept or ask for another quote)

            // Send the selected quote ID and action to the backend
            $.ajax({
                url: '/user/submit-quote-action', // Adjust the backend URL as needed
                type: 'POST',
                data: {
                    quote_id: selectedQuoteId,
                    action: action,
                    _token: '{{ csrf_token() }}'  // Include CSRF token if needed
                },
                success: function (response) {
                    // Handle success (e.g., show a success message)
                    alert("Action submitted successfully!");
                    $('#quoteModal').modal('hide');
                },
                error: function () {
                    // Handle error
                    alert("An error occurred while submitting the action.");
                }
            });
        }



    </script>
@endpush

@endsection
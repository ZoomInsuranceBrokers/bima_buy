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
                        <div class="form-group mt-3">
                            <label for="aadharCard" class="form-label">Upload Aadhar Card</label>
                            <input type="file" id="aadharCard" class="form-control" accept="image/*,.pdf" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="panCard" class="form-label">Upload PAN Card</label>
                            <input type="file" id="panCard" class="form-control" accept="image/*,.pdf" required>
                        </div>
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
                        <label for="policyCopy">Upload Payment Screen Short</label>
                        <input type="file" class="form-control" id="policyCopy" name="paymentScreenShort" required />
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitPaymentScreeShort()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        window.showQuoteDetails = function (leadId) {
            $.ajax({
                url: '/quote-details/' + leadId,
                type: 'GET',
                success: function (response) {
                    let quotesHTML = '';
                    response.forEach(function (quote) {
                        // Using Bootstrap grid to create a responsive layout
                        quotesHTML += `<div class="col-md-4 mb-3"> 
                                                                                                                                        <div class="card">
                                                                                                                                            <div class="card-body">
                                                                                                                                                <h5 class="card-title">${quote.quote_name}</h5>
                                                                                                                                                <p><strong>Document:</strong> ${quote.file_path ? `<a href="${quote.temporary_url}" target="_blank">View</a>` : 'No file attached'}</p>
                                                                                                                                                <p><strong>Price:</strong> ₹${quote.price}</p>
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

        function handleActionChange() {
            var action = $('#quoteAction').val();
            if (action === "accept") {
                // Show additional fields for Aadhar and PAN Card
                $('#additionalDocuments').show();
            } else {
                // Hide the fields when another action is selected
                $('#additionalDocuments').hide();
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

            if (!action) {
                Swal.fire('Please select an action to proceed.');
                return;
            }

            if (action === "accept") {
                var aadharFile = $('#aadharCard')[0].files[0];
                var panFile = $('#panCard')[0].files[0];

                if (!aadharFile || !panFile) {
                    Swal.fire("Both Aadhar Card and PAN Card must be uploaded.");
                    return;
                }
            }

            var formData = new FormData();
            formData.append('quote_id', selectedQuoteId);
            formData.append('action', action);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('aadharCard', aadharFile);
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
                    // Handle success (e.g., show a success message)
                    Swal.fire("Form submitted successfully!").then(() => {
                        location.reload();
                    });
                    $('#quoteModal').modal('hide');
                },
                error: function () {
                    // Handle error
                    Swal.fire("An error occurred while submitting the action pls try again.");
                }
            });
        }

        window.uploadPaymentScreeShort = function (leadId) {

            $.get(`/payment/screenshort/link/${leadId}`, function (data) {
                if (data.paymentLink) {

                    $('#paymentLinkDisplay').html(`<strong>Payment Link:</strong> <a href="${data.paymentLink}" target="_blank">View</a>`).show();

                }
            })

            $('#uploadPaymentScreeShortModal').data('id', leadId).modal('show');
        };

        window.submitPaymentScreeShort = function () {
            const leadId = $('#uploadPaymentScreeShortModal').data('id');
            const formData = new FormData($('#uploadPolicyCopyForm')[0]);

            $.ajax({
                url: `/leads/${leadId}/upload-Payment-Scree-Short`,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Payment screen Short uploaded successfully!');
                        $('#uploadPaymentScreeShortModal').modal('hide');
                        location.reload();
                    } else {
                        Swal.fire(response.message);
                    }
                },
                error: function () {
                    Swal.fire('Failed to upload policy copy.');
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
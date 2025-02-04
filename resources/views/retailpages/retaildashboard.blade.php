@extends('layouts.app')

@section('content')

<div class="content-wrapper ">
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
                                    <th>Rc Mobile No</th>
                                    <th>Verify By Zm</th>
                                    <th>Verify Retail</th>
                                    <th>Send Quote Details</th>
                                    <th>Payment Status</th>
                                    <th>Upload Policy Copy</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingLeads as $lead)
                                <tr>
                                    <td>{{$lead->user->first_name . ' ' . $lead->user->last_name}}</td>
                                    <td>{{$lead->id}}</td>
                                    <td>{{$lead->first_name . ' ' . $lead->last_name}}</td>
                                    <td>{{$lead->user->mobile}}</td>
                                    <td>
                                        @if ($lead->is_cancel)
                                        <label class="badge badge-danger">Cancel</label>
                                        @else
                                        <label class="badge badge-success">Verified by
                                            {{$lead->zonalManager->name}}</label>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lead->is_cancel)
                                        <label class="badge badge-danger">Cancel</label>
                                        @elseif($lead->is_issue && !$lead->is_retail_verified)
                                        <label class="badge badge-warning">Pending</label>
                                        @elseif($lead->is_retail_verified)
                                        <button type="button" class="btn btn-gradient-info btn-sm"
                                            onclick="getLeadDetailsAfterVerify({{ $lead->id }})">Verified</button>
                                        @else
                                        <button type="button" class="btn btn-gradient-info btn-sm"
                                            onclick="getLeadDetails({{ $lead->id }})">View Details</button>
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
                                        <label class="badge badge-success">Complete</label>
                                        @elseif($lead->is_accepted && !$lead->is_issue)
                                        <button type="button" class="btn btn-gradient-info btn-sm"
                                            onclick="updatePayment({{$lead->id}})">Update Payment</button>
                                        @else
                                        <label class="badge badge-warning">Pending</label>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($lead->is_cancel)
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
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No pending leads have been generated yet.</td>
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
                <button type="button" class="btn btn-primary btn-sm mb-3" onclick="addQuoteField()">
                    <i class="md md-plus"></i> Add New Quote
                </button>

                <form id="addQuoteForm" enctype="multipart/form-data">
                    <div id="quotesContainer" class="mb-4">
                        <!-- Quotes will be appended here -->
                    </div>


                    <input type="hidden" name="lead_id" id="leadIdInput" />
                    <button type="submit" class="btn btn-primary mt-3">Submit Quotes</button>
                    <button type="button" class="btn btn-secondary mt-3 close" data-dismiss="modal" aria-label="Close">
                        close
                    </button>
                </form>
            </div>
        </div>
    </div>


</div>
<!-- Payment Status Modal  and verify documents-->
<div class="modal fade" id="updatePaymentModalFirst" tabindex="-1" role="dialog"
    aria-labelledby="updatePaymentModalFirstLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePaymentModalFirstLabel">Update Payment Link</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updatePaymentFormFirst">
                    <div class="form-group">
                        <div id="aadharPanLinks" class="mb-3">
                            <p id="aadharCardLink" style="font-weight: bold;"></p>
                            <p id="panCardLink" style="font-weight: bold;"></p>
                        </div>
                        <div class="input-group" id="paymentLinkInputFirst">
                            <input type="text" class="form-control" id="paymentLinkFirst" name="paymentLink"
                                placeholder="Enter Payment Link">
                        </div>

                        <div id="paymentActionFirst1" class="mt-3">
                            <label for="paymentActionFirst">Choose Action</label>
                            <select class="form-control" id="paymentActionFirst" name="paymentAction">
                                <option value="" disabled selected>Select an action</option>
                                <option value="send_payment_link">Documents are correct, and send payment link</option>
                                <option value="upload_aadhar">Re-upload Aadhar card</option>
                                <option value="upload_pan">Re-upload PAN card</option>
                                <option value="upload_both_aader_pan">Re-upload both Aadhar and PAN cards</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitPaymentActionFirst()">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Status Modal  and  verify payment-->
<div class="modal fade" id="updatePaymentModalSecond" tabindex="-1" role="dialog"
    aria-labelledby="updatePaymentModalSecondLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePaymentModalSecondLabel">Update Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updatePaymentFormSecond">
                    <div class="form-group">
                        <div id="paymentScreensort" class="mb-3"></div>
                        <div id="paymentActionSecond2">
                            <label for="paymentActionSecond">Choose Action</label>
                            <select class="form-control" id="paymentActionSecond" name="paymentAction">
                                <option value="" disabled selected>Select an action</option>
                                <option value="complete">Payment Complete</option>
                                <option value="reupload">Reupload unclear payment screenshot</option>
                                <option value="notify">Send Notification For Payment</option>
                                <option value="cancel">Cancel</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitPaymentActionSecond()">Submit</button>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Policy Start Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="policy_start_date" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">Policy e Date<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="policy_end_date" required />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="policyCopy">Upload Policy Copy<span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="policyCopy" name="policyCopy" required />
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitPolicyCopy()">Submit</button>
                </form>

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

        window.getLeadDetailsAfterVerify = function(leadId) {
            $('#preloader1').show();
            $.get(`/zm/leads/details/${leadId}`, function(data) {
                $('#preloader1').hide();
                if (data.success) {
                    let imagesHtml = '';
                    data.lead.documents.forEach((doc) => {
                        imagesHtml += `
                                    <tr>
                                        <td>${doc.document_name}</td>
                                        <td><a href="{{ url('storage/${doc.file_path}') }}" target="_blank">View</a></td>
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
                            <div class="col-md-6"><p><strong>Vehicle Type:</strong> ${data.lead.vehicle_type}</p></div>
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
                               <tr><th>File Name</th><th>Action</th></tr>
                           </thead>
                           <tbody>${imagesHtml}</tbody>
                       </table>
                    `);

                    $('#leadDetailsModal').data('id', leadId).modal('show');

                } else {
                    Swal.fire(data.message);
                }
            });

        }
        // Fetch lead details and populate modal
        window.getLeadDetails = function(leadId) {
            $('#preloader1').show();
            $.get(`/zm/leads/details/${leadId}`, function(data) {
                $('#preloader1').hide();
                if (data.success) {
                    let imagesHtml = '';
                    data.lead.documents.forEach((doc) => {
                        imagesHtml += `
                                                                     <tr>
                                                                         <td>${doc.document_name}</td>
                                                                         <td><a href="{{ url('storage/${doc.file_path}') }}" target="_blank">View</a></td>
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
                                                                                                                                                                             <div class="col-md-6"><p><strong>Vehicle Type:</strong> ${data.lead.vehicle_type}</p></div>
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

        // Handle action submission
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

            $.post(`/leads/action/retail/${leadId}`, {
                _token: '{{ csrf_token() }}',
                action: action,
                message: action === 'insufficient_details' ? message : null
            }, function(response) {
                $('#preloader1').hide();
                if (response.success) {
                    Swal.fire('form submitted successfully!');
                    $('#leadDetailsModal').modal('hide');
                    location.reload();
                } else {
                    Swal.fire(response.message);
                }
            });
        });

        window.sendQuoteDetails = function(leadId) {
            // Fetch quote details from the server
            console.log(leadId);
            $('#preloader1').show();
            $.ajax({
                url: `/leads/${leadId}/quotes`,
                method: 'GET',
                success: function(response) {
                    $('#preloader1').hide();
                    let quotesContainer = $('#quotesContainer');
                    quotesContainer.empty();
                    response.forEach(function(quote) {
                        const quoteItem = `
                                                                                                                        <div class="quote-card col-md-4">
                                                                                                                            <h5>${quote.quote_name}</h5>
                                                                                                                            <p><strong>Price (Inclusive of GST):</strong> ₹${quote.price}</p>
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
                    $('#leadIdInput').val(leadId);
                    // Show the modal
                    $('#quoteModal').modal('show');
                },
                error: function() {
                    $('#preloader1').hide();
                    Swal.fire('Failed to fetch quote details.');
                }
            });
        };

        window.addQuoteField = function() {
            const quoteField = `                                                                   
                                                                                                    <div class="quote-fields mb-3">
                                                                                                        <div class="row">
                                                                                                            <div class="form-group col-md-6">
                                                                                                               <label for="quoteName">Company Name</label>
                                                                                                               <input type="text" class="form-control" name="quote_name[]" placeholder="Enter company name" required>
                                                                                                            </div>
                                                                                                             <div class="form-group col-md-6">
                                                                                                                <label for="quoteFile">Upload Document</label>
                                                                                                                <input type="file" class="form-control" name="file_path[]">
                                                                                                            </div> 
                                                                                                         </div>
                                                                                                         <div class="row">
                                                                                                             <div class="form-group col-md-6">
                                                                                                                 <label for="odPremium">OD Premium</label>
                                                                                                                 <input type="number" class="form-control" name="od_premium[]" placeholder="Enter OD Premium" required>
                                                                                                             </div>
                                                                                                            <div class="form-group col-md-6">
                                                                                                                <label for="tpPremium">TP Premium</label>
                                                                                                                <input type="number" class="form-control" name="tp_premium[]" placeholder="Enter TP Premium" required>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                         <div class="row">
                                                                                                            <div class="form-group col-md-6">
                                                                                                                <label for="price">Price (Inclusive of GST)</label>
                                                                                                                <input type="number" class="form-control" name="price[]" placeholder="Enter price" required>
                                                                                                            </div>
                                                                                                            <div class="form-group col-md-6">
                                                                                                                 <label for="vehicleIdv">Vehicle IDV</label>
                                                                                                                 <input type="number" class="form-control" name="vehicle_idv[]" placeholder="Enter Vehicle IDV" required>
                                                                                                             </div>
                                                                                                          </div>
                                                                                                       
                                                                                                        <button type="button" class="btn btn-danger" onclick="$(this).closest('.quote-fields').remove()">
                                                                                                            Remove
                                                                                                        </button>
                                                                                                    </div>
                                                                                                `;
            // Append the new fields to the container
            $('#quotesContainer').append(quoteField);

        };

        $('#addQuoteForm').on('submit', function(e) {
            e.preventDefault();
            $('#preloader1').show();
            let formData = new FormData(this);
            $.ajax({
                url: '/quotes',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                processData: false,
                contentType: false,
                success: function() {
                    $('#preloader1').hide();
                    Swal.fire('Quote added successfully!');
                    $('#quoteModal').modal('hide');
                },
                error: function(response) {
                    $('#preloader1').hide();
                    Swal.fire(response.responseJSON.message);
                }
            });
        });

        window.updatePayment = function(leadId) {
            $('#preloader1').show();
            $.get(`/payment/screenshort/link/${leadId}`, function(data) {
                $('#preloader1').hide();
                if (data.success) {
                    if (data.screenShort) {
                        $('#paymentScreensort').html(`<strong>Payment Screen Short:</strong> <a href="${data.screenShort}" target="_blank">View</a>`);
                    } else {
                        $('#paymentScreensort').html(`<strong>Payment Screen Short:</strong> Not Upload Yet`);

                    }
                    if (data.paymentLink) {
                        $('#paymentLinkDisplayFirst').html(`<strong>Payment Link:</strong> <a href="${data.paymentLink}" target="_blank">View</a>`).show();
                        $('#updatePaymentModalSecond').data('id', leadId).modal('show');
                    } else {
                        $('#aadharCardLink').html(`<strong>Aadhar Card:</strong> <a href="{{ url('storage/${data.aadhar_card}') }}" target="_blank">View Aadhar</a>`);
                        $('#panCardLink').html(`<strong>PAN Card:</strong> <a href="{{ url('storage/${data.pan_card}') }}" target="_blank">View PAN</a>`);
                        $('#updatePaymentModalFirst').data('id', leadId).modal('show');
                    }
                } else {
                    Swal.fire("something went wrong pls try again after some.");
                    return;
                }
            });
        };

        // Submit action for first modal
        window.submitPaymentActionFirst = function() {
            const leadId = $('#updatePaymentModalFirst').data('id');
            const action = $('#paymentActionFirst').val();
            const paymentLink = $('#paymentLinkFirst').val();

            if (!action) {
                Swal.fire('Please select an action to proceed.');
                return;
            }

            if (action === 'send_payment_link' && !paymentLink.trim()) {
                Swal.fire('Please enter a payment link to proceed.');
                return;
            }

            $('#preloader1').show();

            // If no new payment link, just submit the action
            $.post(`/leads/payment/${leadId}`, {
                _token: '{{ csrf_token() }}',
                action: action,
                paymentLink: paymentLink
            }, function(response) {

                $('#preloader1').hide();
                if (response.success) {
                    Swal.fire('Form submitted successfully!');
                    $('#updatePaymentModalFirst').modal('hide');
                    location.reload();
                } else {
                    Swal.fire(response.message);
                }
            });
        };

        // Submit action for second modal
        window.submitPaymentActionSecond = function() {
            const leadId = $('#updatePaymentModalSecond').data('id');
            const action = $('#paymentActionSecond').val();

            if (!action) {
                Swal.fire('Please select an action to proceed.');
                return;
            }

            $('#preloader1').show();

            $.post(`/leads/payment/${leadId}`, {
                _token: '{{ csrf_token() }}',
                action: action
            }, function(response) {

                $('#preloader1').hide();
                if (response.success) {
                    Swal.fire('Form submitted successfully!');
                    $('#updatePaymentModalSecond').modal('hide');
                    location.reload();
                } else {
                    Swal.fire(response.message);
                }
            });
        };

        window.uploadPolicyCopy = function(leadId) {
            $('#uploadPolicyCopyModal').data('id', leadId).modal('show');
        };

        window.submitPolicyCopy = function() {
            const leadId = $('#uploadPolicyCopyModal').data('id');
            const formData = new FormData($('#uploadPolicyCopyForm')[0]);
            $('#preloader1').show();
            $.ajax({
                url: `/leads/${leadId}/upload-policy`,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#preloader1').hide();
                    if (response.success) {
                        Swal.fire('Policy copy uploaded successfully!');
                        $('#uploadPolicyCopyModal').modal('hide');
                        location.reload();
                    } else {
                        Swal.fire(response.message);
                    }
                },
                error: function() {
                    $('#preloader1').hide();
                    Swal.fire('Failed to upload policy copy.');
                }
            });
        };
    });
</script>
@endpush

@endsection
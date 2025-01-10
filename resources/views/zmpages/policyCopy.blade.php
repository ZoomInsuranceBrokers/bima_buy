@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Policy Copy</h4>
                    <form method="GET" action="{{ route('zm.policyCopy.search') }}" id="searchForm">
                        <div class="form-row d-flex align-items-center">
                            <!-- Tracking ID -->
                            <div class="col-md-5">
                                <label for="tracking_id" class="sr-only">Tracking ID</label>
                                <input type="text" class="form-control mb-2" id="tracking_id" name="tracking_id"
                                    placeholder="Enter Tracking ID">
                            </div>

                            <!-- OR keyword -->
                            <div class="col-md-2 text-center align-self-center">
                                <p class="my-2"><strong>OR</strong></p>
                            </div>

                            <!-- Mobile No -->
                            <div class="col-md-5">
                                <label for="mobile_no" class="sr-only">Mobile No</label>
                                <input type="text" class="form-control mb-2" id="mobile_no" name="mobile_no"
                                    placeholder="Enter Mobile No">
                            </div>
                        </div>

                        <!-- Error message -->
                        <div class="alert alert-danger mt-3 d-none" id="error-message">
                            Please fill at least one field (Tracking ID or Mobile No).
                        </div>

                        <!-- Submit button -->
                        <div class="col-md-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Search</button>
                        </div>
                    </form>

                    <!-- Display policy details -->
                    <div id="policy-table" class="mt-4 d-none">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>Mobile No</th>
                                    <th>Customer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="policy-details"></tbody>
                        </table>
                    </div>

                    <!-- Action buttons -->
                    <div id="action-buttons" class="mt-4 d-none">
                        <button class="btn btn-success" id="whatsapp-btn">Send via WhatsApp</button>
                        <button class="btn btn-info" id="email-btn">Send via Email</button>
                        <button class="btn btn-primary" id="message-btn">Send via Message</button>
                        <button class="btn btn-secondary" id="download-btn">Download Policy Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#searchForm').on('submit', function (e) {
                e.preventDefault();

                let trackingId = $('#tracking_id').val().trim();
                let mobileNo = $('#mobile_no').val().trim();

                if (trackingId === '' && mobileNo === '') {
                    $('#error-message').removeClass('d-none');
                } else {
                    // console.log('Search form submitted');
                    $('#error-message').addClass('d-none');
                    $.ajax({
                        url: "{{ route('zm.policyCopy.search') }}",
                        method: 'GET',
                        data: { tracking_id: trackingId, mobile_no: mobileNo },
                        success: function (response) {
                            if (response.success) {
                                $('#policy-table').removeClass('d-none');
                                $('#policy-details').html(`
                                        <tr>
                                            <td>${response.data.tracking_id}</td>
                                            <td>${response.data.mobile_no}</td>
                                            <td>${response.data.name}</td>
                                            <td>
                                                <button class="btn btn-info" id="send-policy-btn" data-policy-id="${response.data.id}">Send</button>
                                            </td>
                                        </tr>
                                    `);
                                $('#action-buttons').removeClass('d-none');
                            } else {
                                Swal.fire('No policy found.');
                            }
                        },
                        error: function () {
                            Swal.fire('An error occurred. Please try again.');
                        }
                    });
                }
            });
        });

        $('#download-btn').click(function () {
            var policyId = $('#send-policy-btn').data('policy-id');
            window.location.href = "{{ route('zm.policyCopy.download') }}?policy_id=" + policyId;
        });


    </script>
@endpush
@endsection
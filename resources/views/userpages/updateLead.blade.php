@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Lead Information</h4>

                    {{-- Success or Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div style="color: red; padding-bottom: 10px;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="form-sample" method="POST" action="{{ route('user.update.lead', $lead->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- This is necessary for PUT requests in Laravel --}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Policy Type<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="policy_type" value="New"
                                                    {{ old('policy_type', $lead->policy_type) == 'New' ? 'checked' : '' }}>
                                                New
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="policy_type"
                                                    value="Fresh" {{ old('policy_type', $lead->policy_type) == 'Fresh' ? 'checked' : '' }}> Fresh
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="policy_type"
                                                    value="Renewal" {{ old('policy_type', $lead->policy_type) == 'Renewal' ? 'checked' : '' }}>
                                                Renewal <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    @error('policy_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="col-md-4">
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" id="cancelLeadCheckbox" name="cancelLead"
                                            class="form-check-input">Cancel Lead
                                        <i class="input-helper"></i></label>
                                </div>
                            </div>

                        </div>
                        <p class="card-description">Personal Information</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">First Name<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ old('first_name', $lead->first_name) }}" />
                                        @error('first_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Last Name<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', $lead->last_name) }}" />
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Gender<span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="gender" class="form-control">
                                            <option value="Male" {{ old('gender', $lead->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $lead->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Vehicle Type<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select name="vehicle_type" class="form-control">
                                            <option value="" {{ old('vehicle_type', $lead->vehicle_type) == '' ? 'selected' : '' }} disabled>Select Vehicle Type</option>
                                            <option value="Motorcycle" {{ old('vehicle_type', $lead->vehicle_type) == 'Motorcycle' ? 'selected' : '' }}>
                                                Motorcycle</option>
                                            <option value="Private Car" {{ old('vehicle_type', $lead->vehicle_type) == 'Private Car' ? 'selected' : '' }}>Private Car
                                            </option>
                                            <option value="Commercial Vehicle" {{ old('vehicle_type', $lead->vehicle_type) == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial
                                                Vehicle</option>
                                        </select>
                                        @error('vehicle_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Mobile Number<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="mobile_number" class="form-control"
                                            value="{{ old('mobile_number', $lead->mobile_no) }}" />
                                        @error('mobile_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Email ID<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="email" name="email" class="form-control" placeholder="Enter Email ID"
                                            value="{{ old('email', $lead->email) }}" />
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="card-description">Vehicle Information</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Vehicle Number<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="vehicle_number" class="form-control"
                                            value="{{ old('vehicle_number', $lead->vehicle_number) }}" />
                                        @error('vehicle_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Claim Status<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="claim_status"
                                                    id="claimStatusYes" value="yes" {{ old('claim_status', $lead->claim_status) == 'yes' ? 'checked' : '' }}> Yes
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="claim_status"
                                                    id="claimStatusNo" value="no" {{ old('claim_status', $lead->claim_status) == 'no' ? 'checked' : '' }}> No
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    @error('claim_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <p class="card-description">Upload Vehicle Documents</p>
                            <div id="documents-container">
                                @foreach($lead->documents as $index => $document)
                                    <div class="form-group row document-upload" id="document-{{ $index }}">
                                        <label class="col-sm-3 col-form-label">Document Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="documents[{{ $index }}][name]" class="form-control"
                                                value="{{ old('documents.' . $index . '.name', $document->document_name) }}" />
                                        </div>
                                        <label class="col-sm-3 col-form-label">Upload File</label>
                                        <div class="col-sm-9">
                                            <input type="file" name="documents[{{ $index }}][file]"
                                                accept="image/*,application/pdf" class="form-control">
                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">Open
                                                Document</a> {{-- Link to open in a new tab --}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Comments:</label>
                                        <div class="col-sm-10">
                                            <textarea name="comments" id="comments" class="form-control" rows="3"></textarea>
                                            @error('comments')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-gradient-primary" id="add-document">
                                    Add Document
                                </button>
                            </div>

                            <button type="submit" id="update" class="btn btn-gradient-primary btn-lg btn-block">
                                Update
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                let documentCount = {{ count($lead->documents) }};

                $('#add-document').on('click', function () {
                    documentCount++;
                    const documentUpload = `
                                                                                            <div class="form-group row document-upload mt-3" id="document-${documentCount - 1}">
                                                                                                <label class="col-sm-3 col-form-label">Document Name</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="text" name="documents[${documentCount - 1}][name]" class="form-control" placeholder="Enter Document Name" disabled>
                                                                                                </div>
                                                                                                <label class="col-sm-3 col-form-label">Upload File</label>
                                                                                                <div class="col-sm-9">
                                                                                                    <input type="file" name="documents[${documentCount - 1}][file]" accept="image/*,application/pdf" class="form-control" onchange="toggleDocumentName(${documentCount - 1})">
                                                                                                </div>
                                                                                                <button type="button" class="btn btn-danger mt-2 col-md-2" onclick="removeDocument(${documentCount - 1})">Remove</button>
                                                                                            </div>`;
                    $('#documents-container').append(documentUpload);
                });

                // Enable/Disable the document name field based on file selection
                window.toggleDocumentName = function (index) {
                    const documentNameField = $(`input[name="documents[${index}][name]"]`);
                    const fileInput = $(`input[name="documents[${index}][file]"]`);

                    if (fileInput[0].files.length > 0) {
                        documentNameField.prop('disabled', false);
                    } else {
                        documentNameField.prop('disabled', true);
                    }
                };

                // Remove document input field
                window.removeDocument = function (index) {
                    $(`#document-${index}`).remove();
                    documentCount--;
                };

                $('form').on('submit', function (e) {
                    const isCancelChecked = $('#cancelLeadCheckbox').is(':checked');
                    const comment = $('#comments').val().trim();

                    // Validation: If cancel is checked and comment is empty, prevent submission
                    if (isCancelChecked && comment === '') {
                        e.preventDefault(); 
                        Swal.fire({
                            title: "Comment Required",
                            text: "You must provide a comment when canceling a lead. Either uncheck the 'Cancel Lead' checkbox or write a comment.",
                            icon: "warning",
                            confirmButtonText: "OK",
                        });
                    } else {
                        $('#preloader1').show();
                    }
                });
                $("#cancelLeadCheckbox").change(function () {
                    if (this.checked) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "Do you really want to cancel this lead?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes, Cancel it!",
                            cancelButtonText: "No, Keep it",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire("Click on Update", "Note: Please write the reason in the comment box for why you want to cancel.", "success");
                            } else {
                                // Uncheck the checkbox if the user cancels
                                $("#cancelLeadCheckbox").prop("checked", false);
                            }
                        });
                    }
                });
            });

        </script>
    @endpush
@endsection
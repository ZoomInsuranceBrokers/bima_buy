@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Customer Information</h4>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
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

                    <form class="form-sample" method="POST" id="leadsubmit" action="{{ route('store.user.lead') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Policy Type<span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="policy_type" value="New"
                                                    {{ old('policy_type') == 'New' ? 'checked' : '' }}> New <i
                                                    class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="policy_type"
                                                    value="Fresh" {{ old('policy_type') == 'Fresh' ? 'checked' : '' }}> Fresh
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="policy_type"
                                                    value="Renewal" {{ old('policy_type') == 'Renewal' ? 'checked' : '' }}>
                                                Renewal <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    @error('policy_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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
                                            placeholder="Enter First Name" value="{{ old('first_name') }}" />
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
                                            placeholder="Enter Last Name" value="{{ old('last_name') }}" />
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
                                            <option value="" {{ old('gender') == '' ? 'selected' : '' }} disabled>Select
                                                Gender</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female
                                            </option>
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
                                            <option value="" {{ old('vehicle_type') == '' ? 'selected' : '' }} disabled>Select
                                                Vehicle Type</option>
                                            <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                            <option value="Private Car" {{ old('vehicle_type') == 'Private Car' ? 'selected' : '' }}>Private Car</option>
                                            <option value="Commercial Vehicle" {{ old('vehicle_type') == 'Commercial Vehicle' ? 'selected' : '' }}>Commercial Vehicle</option>
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
                                            placeholder="Enter Mobile Number" value="{{ old('mobile_number') }}" />
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
                                            value="{{ old('email') }}" />
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
                                            placeholder="Enter Vehicle Number" value="{{ old('vehicle_number') }}" />
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
                                                    id="claimStatusYes" value="yes" {{ old('claim_status') == 'yes' ? 'checked' : '' }}> Yes
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="claim_status"
                                                    id="claimStatusNo" value="no" {{ old('claim_status') == 'no' ? 'checked' : '' }}> No
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                    @error('claim_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Comments:</label>
                                    <div class="col-sm-10">
                                        <textarea name="comments" class="form-control" rows="3"></textarea>
                                        @error('comments')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <p class="card-description">Upload Vehicle Documents</p>
                        <div id="documents-container">
                            <div class="form-group row document-upload" id="document-0">
                                <label class="col-sm-3 col-form-label">Document Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="documents[0][name]" class="form-control"
                                        value="Registration certificate(RC)" placeholder="Enter Document Name" disabled>
                                    @error('documents.0.name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <label class="col-sm-3 col-form-label">Upload File</label>
                                <div class="col-sm-9">
                                    <input type="file" name="documents[0][file]" accept="image/*,application/pdf"
                                        class="form-control" required onchange="toggleDocumentName(0)">
                                    @error('documents.0.file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row document-upload" id="document-1">
                                <label class="col-sm-3 col-form-label">Document Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="documents[1][name]" class="form-control"
                                        value="Previous Year Policy" placeholder="Enter Document Name" disabled>
                                    @error('documents.1.name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <label class="col-sm-3 col-form-label">Upload File</label>
                                <div class="col-sm-9">
                                    <input type="file"  name="documents[1][file]" accept="image/*,application/pdf"
                                        class="form-control" onchange="toggleDocumentName(1)">
                                    @error('documents.1.file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-gradient-primary" id="add-document">
                                Add Document
                            </button>
                        </div>

                        <button type="submit" class="btn btn-gradient-primary btn-lg btn-block">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let documentCount = 2;

            // Add document input fields
            $('#add-document').on('click', function () {
                documentCount++;
                const documentUpload = `
                                <div class="form-group row document-upload mt-3" id="document-${documentCount}">
                                    <label class="col-sm-3 col-form-label">Document Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="documents[${documentCount - 1}][name]" class="form-control" value="" placeholder="Enter Document Name" disabled>
                                    </div>
                                    <label class="col-sm-3 col-form-label">Upload File</label>
                                    <div class="col-sm-9">
                                        <input type="file" name="documents[${documentCount - 1}][file]" accept="image/*,application/pdf" required class="form-control" onchange="toggleDocumentName(${documentCount - 1})">
                                    </div>
                                    <button type="button" class="btn btn-danger mt-2 col-md-2" onclick="removeDocument(${documentCount})">Remove</button>
                                </div>
                            `;
                $('#documents-container').append(documentUpload);
            });

            // Enable/Disable the document name field based on file selection
            function toggleDocumentName(index) {
                const documentNameField = $(`input[name="documents[${index}][name]"]`);
                const fileInput = $(`input[name="documents[${index}][file]"]`);

                if (fileInput[0].files.length > 0) {
                    documentNameField.prop('disabled', false);
                } else {
                    documentNameField.prop('disabled', true);
                }
            }

            // Remove document input field
            function removeDocument(index) {
                $(`#document-${index}`).remove();
                documentCount--;
            }

            $('#leadsubmit').on('submit', function () {
                $('#preloader1').show();
            })


        </script>

    @endpush
@endsection
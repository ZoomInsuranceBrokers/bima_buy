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

                <form class="form-sample" method="POST" action="{{ route('store.user.lead') }}"
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
                                            <input type="radio" class="form-check-input" name="policy_type" value="new"
                                                {{ old('policy_type') == 'new' ? 'checked' : '' }}> New <i
                                                class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="policy_type"
                                                value="fresh" {{ old('policy_type') == 'fresh' ? 'checked' : '' }}> Fresh
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="policy_type"
                                                value="renewal" {{ old('policy_type') == 'renewal' ? 'checked' : '' }}>
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
                                <label class="col-sm-3 col-form-label">Date of Birth<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="date_of_birth" placeholder="dd/mm/yyyy"
                                        type="date" value="{{ old('date_of_birth') }}" />
                                    @error('date_of_birth')
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
                                <label class="col-sm-3 col-form-label">Email ID</label>
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



                    <p class="card-description">Upload Vehicle Documents</p>
                    <div id="documents-container">
                        <div class="form-group row document-upload">
                            <label class="col-sm-3 col-form-label">Document 1 Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="documents[0][name]" class="form-control"
                                    placeholder="Enter Document Name" disabled>
                                @error('documents.0.name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <label class="col-sm-3 col-form-label">Document 1 File</label>
                            <div class="col-sm-9">
                                <input type="file" name="documents[0][file]" accept="image/*,application/pdf"
                                    class="form-control" onchange="toggleDocumentName(0)">
                                @error('documents.0.file')
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
        $(document).ready(function () {
            let documentCount = 1;

            $('#add-document').on('click', function () {
                documentCount++;
                const documentUpload = `
                                            <div class="form-group row document-upload mt-3">
                                                <label class="col-sm-3 col-form-label">Document ${documentCount} Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="documents[${documentCount - 1}][name]" class="form-control" placeholder="Enter Document Name" disabled>
                                                </div>
                                                <label class="col-sm-3 col-form-label">Document ${documentCount} File</label>
                                                <div class="col-sm-9">
                                                    <input type="file" name="documents[${documentCount - 1}][file]" accept="image/*,application/pdf" class="form-control" onchange="toggleDocumentName(${documentCount - 1})">
                                                </div>
                                            </div>`;
                $('#documents-container').append(documentUpload);
            });
        });

        // Enable/Disable the document name field based on file selection
        function toggleDocumentName(index) {
            const documentNameField = document.querySelector(`input[name="documents[${index}][name]"]`);
            const fileInput = document.querySelector(`input[name="documents[${index}][file]"]`);
            documentNameField.disabled = !fileInput.files.length;
        }
    </script>
@endpush
@endsection
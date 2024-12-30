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

                <form class="form-sample" method="POST" action="{{ route('user.update.lead', $lead->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- This is necessary for PUT requests in Laravel --}}
                    
                    <p class="card-description">Personal Information</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">First Name<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $lead->first_name) }}" />
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Last Name<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $lead->last_name) }}" />
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
                                <label class="col-sm-3 col-form-label">Date of Birth<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="date_of_birth" type="date" value="{{ old('date_of_birth', $lead->date_of_birth) }}" />
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
                                <label class="col-sm-3 col-form-label">Mobile Number<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number', $lead->mobile_no) }}" />
                                    @error('mobile_number')
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
                                <label class="col-sm-3 col-form-label">Vehicle Number<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $lead->vehicle_number) }}" />
                                    @error('vehicle_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="card-description">Upload Vehicle Documents</p>
                    <div id="documents-container">
                        @foreach($lead->documents as $index => $document)
                        <div class="form-group row document-upload">
                            <label class="col-sm-3 col-form-label">Document {{ $index + 1 }} Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="documents[{{ $index }}][name]" class="form-control" value="{{ old('documents.' . $index . '.name', $document->document_name) }}" />
                            </div>
                            <label class="col-sm-3 col-form-label">Document {{ $index + 1 }} File</label>
                            <div class="col-sm-9">
                                <input type="file" name="documents[{{ $index }}][file]" accept="image/*,application/pdf" class="form-control">
                                <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">Open Document</a> {{-- Link to open in a new tab --}}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-gradient-primary" id="add-document">
                            Add Document
                        </button>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary btn-lg btn-block">
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

@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Custmor Information</h4>

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif -->

                {{-- Form --}}
                <form class="form-sample" method="POST" action="{{ route('store.user.lead') }}" enctype="multipart/form-data">
                    @csrf

                    <p class="card-description">Personal Information</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">First Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="first_name" class="form-control" placeholder="Enter First Name" value="{{ old('first_name') }}" />
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Last Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="last_name" class="form-control" placeholder="Enter Last Name" value="{{ old('last_name') }}" />
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
                                <label class="col-sm-3 col-form-label">Gender</label>
                                <div class="col-sm-9">
                                    <select name="gender" class="form-control">
                                        <option value="" {{ old('gender') == '' ? 'selected' : '' }} disabled>select gender</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Date of Birth</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="date_of_birth" placeholder="dd/mm/yyyy" type="date" value="{{ old('date_of_birth') }}" />
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
                                <label class="col-sm-3 col-form-label">Mobile Number</label>
                                <div class="col-sm-9">
                                    <input type="text" name="mobile_number" class="form-control" placeholder="Enter Mobile Number" value="{{ old('mobile_number') }}" />
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
                                <label class="col-sm-3 col-form-label">Vehicle Number</label>
                                <div class="col-sm-9">
                                    <input type="text" name="vehicle_number" class="form-control" placeholder="Enter Vehicle Number" value="{{ old('vehicle_number') }}" />
                                    @error('vehicle_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="card-description">Upload Vehicle Documents</p>
                    <div id="documents-container">
                        <div class="form-group row document-upload">
                            <label class="col-sm-3 col-form-label">Document 1</label>
                            <div class="col-sm-9">
                                <input type="file" name="documents[]" accept="image/*" capture="user" class="form-control">
                                @error('documents.0')
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
                    <label class="col-sm-3 col-form-label">Document ${documentCount}</label>
                    <div class="col-sm-9">
                        <input type="file" name="documents[]" accept="image/*" capture="user" class="form-control">
                    </div>
                </div>`;
            $('#documents-container').append(documentUpload);
        });
    });
</script>
@endpush
@endsection

@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
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


        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add User</h4>
                    <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <p class="card-description">Select Role</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role<span class="text-danger">*</span></label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="">Select Role</option>
                                        <option value="zm" {{ old('role') == 'zm' ? 'selected' : '' }}>Zonal Manager</option>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Regional Cordinator
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <p class="card-description">User Information</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ old('first_name') }}">
                                    @error('first_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile_no">Mobile No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no"
                                        value="{{ old('mobile_no') }}">
                                    @error('mobile_no')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email ID<span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Select Gender<span class="text-danger">*</span></label>
                                    <select class="form-control" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="zm-container" style="display:none;">
                                <div class="form-group">
                                    <label for="zm">Select Zonal Manager<span class="text-danger">*</span></label>
                                    <select class="form-control" id="zm" name="zm">
                                        <option value="">Select ZM</option>
                                        @foreach ($zonalManagers as $zm)
                                            <option value="{{ $zm->id }}" {{ old('zm') == $zm->id ? 'selected' : '' }}>
                                                {{ $zm->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('zm')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_photo">Profile Photo</label>
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo"
                                        accept="image/*">
                                    @error('profile_photo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#role').change(function () {
                var role = $(this).val();

                if (role == 'user') {
                    $('#zm-container').show();
                } else {
                    $('#zm-container').hide();
                }
            });
        });
    </script>
@endpush
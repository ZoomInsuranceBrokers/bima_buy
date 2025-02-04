@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Personal Information</h4>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="form-sample" method="POST" action="{{ route('profile.update') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @php $user = Auth::user(); @endphp
                    <!-- Profile Picture Section -->
                    <div class="text-center">

                        <div class="d-flex justify-content-center">
                            <div class="rounded-circle"
                                style="width: 100px; height: 100px; overflow: hidden; position: relative;">
                                <img src="{{ asset('storage/' . $user->image_path) }}" alt="Profile Picture"
                                    class="img-fluid rounded-circle"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                                <!-- Camera Icon Overlay -->
                                <label for="profile_picture" class="camera-icon"
                                    style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.5); padding: 10px; border-radius: 50%; cursor: pointer;">
                                    <i class="mdi mdi-camera text-white"></i>
                                </label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                                    style="display: none;" onchange="this.form.submit();">
                            </div>
                        </div>
                    </div>

                    <p class="card-description"> Personal info </p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">First Name<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="first_name" class="form-control"
                                        value="{{ old('first_name', $user->first_name) }}" required />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Last Name<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="last_name" class="form-control"
                                        value="{{ old('last_name', $user->last_name) }}" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Gender<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="gender" class="form-control" required>
                                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Mobile Number<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="mobile" class="form-control"
                                        value="{{ old('mobile', $user->mobile) }}" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="card-description"> Bank Details </p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Bank Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Account.No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">IFSC Code</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary btn-lg btn-block">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
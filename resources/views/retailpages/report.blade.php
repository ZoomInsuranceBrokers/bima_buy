@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Form to select From Date and To Date -->
                <form action="{{ route('retail.generate.report') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_date">From Date:</label>
                                <input type="date" id="from_date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" value="{{ old('from_date') }}" required>
                                
                                <!-- Display error message for 'from_date' -->
                                @error('from_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_date">To Date:</label>
                                <input type="date" id="to_date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" value="{{ old('to_date') }}" required>

                                <!-- Display error message for 'to_date' -->
                                @error('to_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary mt-3">Get Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

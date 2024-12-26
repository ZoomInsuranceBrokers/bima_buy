@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Policy Copy</h4>
                    <form method="GET" action="">
                        <div class="form-row d-flex align-items-center">
                            <!-- Tracking ID and OR in one row -->
                            <div class="col-md-5">
                                <label for="tracking_id" class="sr-only">Tracking ID</label>
                                <input type="text" class="form-control mb-2" id="tracking_id" name="tracking_id"
                                    placeholder="Enter Tracking ID">
                            </div>

                            <!-- OR keyword in the middle -->
                            <div class="col-md-2 text-center align-self-center">
                                <p class="my-2"><strong>OR</strong></p>
                            </div>

                            <!-- Mobile No in the same row -->
                            <div class="col-md-5">
                                <label for="mobile_no" class="sr-only">Mobile No</label>
                                <input type="text" class="form-control mb-2" id="mobile_no" name="mobile_no"
                                    placeholder="Enter Mobile No">
                            </div>

                            <!-- Submit button in the next row -->

                        </div>
                        <div class="col-md-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
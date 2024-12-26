@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pending Leads</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tracking ID </th>
                                    <th>Customer Name</th>
                                    <th>Verified by Zonalmanger </th>
                                    <th>Verified by Retail Team</th>
                                    <th>Final Quote</th>
                                    <th>Quote details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td> David Grey</td>
                                    <td>
                                        <label class="badge badge-success">Verified</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-danger">Pending</label>
                                    </td>
                                    <td>
                                        ₹2,190
                                    </td>
                                    <td><button type="button" class="btn btn-gradient-info btn-sm">View Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td> David Grey</td>
                                    <td>
                                        <label class="badge badge-success">Verified</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-danger">Pending</label>
                                    </td>
                                    <td>
                                        ₹2,190
                                    </td>
                                    <td><button type="button" class="btn btn-gradient-info btn-sm">View Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td> David Grey</td>
                                    <td>
                                        <label class="badge badge-success">Verified</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-danger">Pending</label>
                                    </td>
                                    <td>
                                        ₹2,190
                                    </td>
                                    <td><button type="button" class="btn btn-gradient-info btn-sm">View Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td> David Grey</td>
                                    <td>
                                        <label class="badge badge-success">Verified</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-danger">Pending</label>
                                    </td>
                                    <td>
                                        ₹2,190
                                    </td>
                                    <td><button type="button" class="btn btn-gradient-info btn-sm">View Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
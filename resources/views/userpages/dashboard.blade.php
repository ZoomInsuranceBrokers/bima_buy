@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Leads</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tracking ID </th>
                                    <th>Customer Name</th>
                                    <th>insurence category</th>
                                    <th>Payment status</th>
                                    <th>Final Status</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td>Naveen</td>
                                    <td>Moter</td>
                                    <td>
                                        <label class="badge badge-danger">Pending</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-warning">In progress</label>
                                    </td>
                                    <td> Dec 5, 2017 </td>
                                </tr>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td>Praveen</td>
                                    <td>Bike</td>
                                    <td>
                                        <label class="badge badge-danger">Cancel</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-danger">Cancel</label>
                                    </td>
                                    <td> Dec 6, 2017 </td>
                                </tr>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td>Ankit</td>
                                    <td>Car</td>
                                    <td>
                                    <label class="badge badge-success">Completed</label>
                                    </td>
                                    <td>
                                    <label class="badge badge-success">Completed</label>
                                    </td>
                                    <td> Dec 9, 2017 </td>
                                </tr>
                                <tr>
                                    <td> WD-12345 </td>
                                    <td>Nipun</td>
                                    <td>Moter</td>
                                    <td>
                                        <label class="badge badge-danger">Pending</label>
                                    </td>
                                    <td>
                                        <label class="badge badge-warning">In progress</label>
                                    </td>
                                    <td> Dec 5, 2017 </td>
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
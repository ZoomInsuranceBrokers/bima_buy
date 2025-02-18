@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Leads</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tracking ID </th>
                                        <th>Rc Name</th>
                                        <th>Customer Name</th>
                                        <th>ZM Name</th>
                                        <th>Last Remark</th>
                                        <th>Lead Created</th>
                                        <th>Last Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leads as $lead)
                                        <tr>
                                            <td>{{ $lead->id }}</td>
                                            <td>{{ $lead->user->first_name.' '.$lead->user->last_name }}</td>
                                            <td>{{ $lead->first_name.' '.$lead->last_name}}</td>
                                            <td>{{ $lead->zonalManager->name }}</td>
                                            <td>{{ $lead->lastNotification->message}}</td>
                                            <td>{{ $lead->created_at }}</td>
                                            <td>{{ $lead->lastNotification->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
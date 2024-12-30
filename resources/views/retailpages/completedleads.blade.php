@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Leads</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Rc Name</th>
                                    <th>Tracking ID </th>
                                    <th>Customer Name</th>
                                    <th>Verify By Zm</th>
                                    <th>Amount</th>
                                    <th>Final staus</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($completedLeads as $lead)
                                    <tr>
                                        <td>{{$lead->user->first_name . ' ' . $lead->user->last_name}}</td>
                                        <td>{{$lead->id}}</td>
                                        <td>{{$lead->first_name . ' ' . $lead->last_name}}</td>
                                        <td><label class="badge badge-success">Verifyed by{{$lead->zonalManager->name}}</label></td>
                                        <td><strong>{{$lead->quotes[0]->price}}</strong></td>
                                        <td><label class="badge badge-success">Booked</label></td>
                                        <td>
                                            @if(!empty($lead->quotes) && $lead->quotes->isNotEmpty())
                                                <span>{{ $lead->quotes->first()->updated_at->format('M d, Y h:i A') }}</span>
                                            @else
                                                <span>{{ $lead->updated_at->format('M d, Y h:i A') }}</span>
                                            @endif
                                        </td>
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
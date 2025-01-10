@extends('layouts.app')

@section('content')

<div class="content-wrapper ">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Cancel Leads</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tracking ID </th>
                                    <th>Customer Name</th>
                                    <th>Mobile No</th>
                                    <th>Canceled</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cancelLeads as $lead)
                                    <tr>
                                        <td>{{$lead->id}}</td>
                                        <td>{{$lead->first_name . ' ' . $lead->last_name}}</td>
                                        <td>{{$lead->mobile_no}}</td>
                                        <td>
                                            @if ($lead->is_issue && $lead->is_zm_verified = 0)
                                                <label class="badge badge-danger">Cancel by Zm ({{ $lead->zonalManager->name }}) </label>
                                            @elseif ($lead->is_issue && $lead->is_retail_verified = 0)
                                                <label class="badge badge-danger">Cancel by me </label>
                                            @else
                                                <label class="badge badge-danger">Cancel by Rc ({{$lead->user->first_name.' '.$lead->user->last_name}})</label>
                                            @endif
                                        </td>

                                        <td>
                                            <span>{{ $lead->updated_at->format('M d, Y h:i A') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No cancel leads yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
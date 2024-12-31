@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pending Leads</h4>
                    <div class="table-responsive">
                        @if($completedLeads->isEmpty())
                            <p>No leads completed yet.</p>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tracking ID </th>
                                        <th>Customer Name</th>
                                        <th>Final Quote</th>
                                        <th>Quote details</th>
                                        <th>Status</th>
                                        <th>Last Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($completedLeads as $lead)
                                        <tr>
                                            <td>{{$lead->id}}</td>
                                            <td>{{$lead->first_name . ' ' . $lead->last_name}}</td>
                                            <td>
                                                @if(!empty($lead->quotes) && $lead->quotes->isNotEmpty())
                                                    {{$lead->quotes->first()->price}}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td><button type="button" class="btn btn-gradient-info btn-sm"
                                                    onclick="showQuoteDetails({{$lead->id}})">View Details</button>
                                            </td>
                                            <td><label class="badge badge-success">complete</label></td>
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="quoteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteModalLabel">Quote Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="quotesContainer" class="mb-4">
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function showQuoteDetails(leadId) {
            $.ajax({
                url: '/quote-details/' + leadId,
                type: 'GET',
                success: function (response) {
                    let quotesContainer = $('#quotesContainer');
                    quotesContainer.empty();
                    // Populate existing quotes
                    response.quotes.forEach(quote => {
                        let features = quote.description.map(feature =>
                            `<li>${feature}</li>`
                        ).join('');

                        quotesContainer.append(`
                            <div class="quote-item mb-4">
                                <div class="d-flex justify-content-between">
                                    <p><strong>Policy Name:</strong> ${quote.quote}</p>
                                </div>
                                <p><strong>Features:</strong></p>
                                <ul>${features}</ul>
                                <p><strong>Price:</strong> ₹${quote.price}</p>
                                <p><strong>Status:</strong> ${quote.is_accepted ? 'Accepted' : 'Pending'}</p>
                            </div>
                        `);
                    });
                    // Show the modal
                    $('#quoteModal').modal('show');
                },
                error: function () {
                    alert('Failed to fetch quote details.');
                }
            });
        }
    </script>
@endpush

@endsection
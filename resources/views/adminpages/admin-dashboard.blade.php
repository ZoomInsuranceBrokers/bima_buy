@extends('layouts.app')

@section('content')

    <div class="content-wrapper">
        <!-- <div class="page-header">
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
            </div> -->
        <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <a href="{{ route('admin.total.leads.today') }}" class="card bg-gradient-info card-img-holder text-white text-decoration-none">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Total leads created
                        </h4>
                        <h2 class="mb-5">{{ $report->total_count }}</h2>
                    </div>
                </a>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <a href="{{ route('admin.completed.leads.today') }}" class="card bg-gradient-success card-img-holder text-white text-decoration-none">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Total lead completed
                        </h4>
                        <h2 class="mb-5">{{ $report->policy_issued ?? 0}}</h2>
                    </div>
                </a>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <a  href="{{ route('admin.cancel.leads.today') }}"class="card bg-gradient-danger card-img-holder text-white text-decoration-none">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Cancel
                        </h4>
                        <h2 class="mb-5">{{ $report->cancel_count ?? 0 }}</h2>
                    </div>
                </a>
            </div>

        </div>
        <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <a href="{{ route('admin.pending.leads.today.rc') }}" class="card bg-gradient-primary card-img-holder text-white text-decoration-none">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Pending at RC end
                        </h4>
                        <h2 class="mb-5">{{ $report->pendin_at_rc ?? 0 }}</h2>
                    </div>
                </a>
            </div>

            <div class="col-md-4 stretch-card grid-margin">
                <a href="{{ route('admin.pending.leads.today.zm') }}" class="card bg-gradient-dark card-img-holder text-white text-decoration-none">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Pending at ZM end
                        </h4>
                        <h2 class="mb-5">{{ $report->pendin_at_zm ?? 0}}</h2>
                    </div>
                </a>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <a href="{{ route('admin.pending.leads.today.retail') }}" class="card bg-gradient-warning card-img-holder text-white text-decoration-none">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Pending at retail team
                        </h4>
                        <h2 class="mb-5">{{ $report->pendin_at_retail ?? 0 }}</h2>
                    </div>
                </a>
            </div>

        </div>
    </div>
   

@endsection
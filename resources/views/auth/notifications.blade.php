@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container mt-5">
        <h3 class="mb-4">All Notifications</h3>

        <!-- Notification List -->
        <div class="row">
            @foreach($notifications as $notification)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-light shadow-sm {{ $notification->is_read ? 'border-light' : 'border-primary' }}" id="notification-{{ $notification->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $notification->sender->image_path) }}" alt="sender image" class="rounded-circle" style="width: 40px; height: 40px;">
                                    <div class="ms-3">
                                        <h6 class="mb-1 text-dark">{{ $notification->sender->first_name }} {{ $notification->sender->last_name }}</h6>
                                        <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <i class="mdi {{ $notification->is_read ? 'mdi-check-circle' : 'mdi-bell-outline' }} text-muted" style="font-size: 1.5rem;"></i>
                            </div>
                            <p class="mt-3 mb-4">{{ $notification->message }}</p>
                            <button class="btn btn-sm {{ $notification->is_read ? 'btn-secondary' : 'btn-primary' }} mark-as-read"
                                    data-id="{{ $notification->id }}"
                                    {{ $notification->is_read ? 'disabled' : '' }}>
                                {{ $notification->is_read ? 'Read' : 'Mark as Read' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="noNotifications" class="text-center mt-5 {{ $notifications->count() ? 'd-none' : '' }}">
            <p>No notifications available.</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Handle Mark as Read button click
            $('.mark-as-read').on('click', function (e) {
                e.preventDefault();

                var button = $(this);
                var notificationId = button.data('id');

                // Make an AJAX request to mark the notification as read
                $.ajax({
                    url: '{{ route('notifications.markAsRead') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: notificationId
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update the UI: change button text and mark the notification as read
                            $('#notification-' + notificationId).removeClass('border-primary').addClass('border-light');
                            button.text('Read').attr('disabled', true);
                            button.removeClass('btn-primary').addClass('btn-secondary');
                            // Optionally add a success toast here
                        } else {
                            alert('Error marking notification as read');
                        }
                    },
                    error: function () {
                        alert('Something went wrong, please try again.');
                    }
                });
            });
        });
    </script>
@endpush

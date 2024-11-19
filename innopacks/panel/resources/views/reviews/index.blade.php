@extends('panel::layouts.app')
@section('body-class', '')

@section('title', __('panel/menu.reviews'))
@section('page-title-right')

@endsection

@section('content')
    <div class="card h-min-600">
        <div class="card-body">
            <x-panel-criteria :criteria="$criteria ?? []" :action="panel_route('reviews.index')" />

            @if ($reviews->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <td>{{ __('panel/review.id') }}</td>
                                <td>{{ __('panel/review.product') }}</td>
                                <td>{{ __('panel/review.rating') }}</td>
                                <td>{{ __('panel/review.review_content') }}</td>
                                <td>{{ __('panel/common.date') }}</td>
                                <td>{{ __('panel/common.active') }}</td>
                                <td>{{ __('panel/common.actions') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr>
                                    <td>{{ $review->id }}</td>
                                    <td data-title="product" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="{{ sub_string($review->product->translation->name ?? '', 200) }}">
                                        <img src="{{ image_resize($review->product->image->path ?? '') }}" alt="{{ $review->product->name ?? '' }}"
                                                 class="img-fluid wh-30">
                                        {{ sub_string($review->product->translation->name ?? '', 10) }}
                                    </td>
                                    <td>
                                        <x-front-review :rating="$review->rating"/>
                                    </td>
                                    <td class="btn-link-review_content" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            title="{{ $review->content }}">{{ sub_string($review->content) }}</td>
                                    <td>{{ $review->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if ($review->id)
                                            @include('panel::shared.list_switch', ['value' => $review->active, 'url' => panel_route('reviews.active', $review->id)])
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn delete-review btn-sm btn-outline-danger"
                                                        data-url="{{ account_route('reviews.destroy', $review->id) }}">{{ __('front/common.delete') }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $reviews->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
            @else
                <x-common-no-data/>
            @endif
        </div>
    </div>
    <div id="review-container" class="mt-4">
        <!-- Data reviews akan dimuat di sini -->
    </div>
@endsection

@push('footer')
<script src="{{ asset('build/panel/js/app.js') }}"></script>
<script>
        Pusher.logToConsole = true;
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true,
        });

        const channel = pusher.subscribe('reviews-channel');
        channel.bind('pusher:subscription_succeeded', function() {
                console.log('Successfully subscribed to reviews-channel');
        });

        // Listen for ReviewUpdated event
        channel.bind('review.updated', function(data) {
                console.log('New review received:', data);
                alert('New review received: ' + JSON.stringify(data));
                // Optional: Refresh review container or perform other DOM updates here
        });
        console.log('App.js loaded');
        console.log('Pusher initialized in index.blade.php');
</script>
@endpush

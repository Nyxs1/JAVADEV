<div class="event-reviews">
    <h2 class="event-section__title">Reviews</h2>

    {{-- Rating Summary --}}
    <div class="event-reviews__summary">
        <div class="event-reviews__rating">
            <span class="event-reviews__rating-value">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</span>
            <div class="event-reviews__stars">
                @for($i = 1; $i <= 5; $i++)
                    <img src="{{ asset('assets/icons/' . ($i <= round($avgRating ?? 0) ? 'star-filled.svg' : 'star-empty.svg')) }}" 
                         alt="" class="event-reviews__star">
                @endfor
            </div>
            <span class="event-reviews__count">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
        </div>
    </div>

    {{-- Absent User Message --}}
    @if($isAbsent ?? false)
    <div class="event-reviews__absent">
        <img src="{{ asset('assets/icons/alert-circle.svg') }}" alt="" class="event-reviews__absent-icon">
        <p>Kamu tidak bisa memberi review karena tidak tercatat hadir.</p>
    </div>
    {{-- Review Form (if eligible) --}}
    @elseif($canReview && !$hasReviewed)
    <div class="event-reviews__form-wrapper">
        <h3 class="event-reviews__form-title">Share Your Experience</h3>
        <form action="{{ route('events.reviews.store', $event) }}" method="POST" class="event-reviews__form">
            @csrf
            <div class="event-reviews__form-rating" data-rating-input>
                <label>Your Rating</label>
                <div class="event-reviews__form-stars">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="event-reviews__form-star" data-value="{{ $i }}">
                        <img src="{{ asset('assets/icons/star-empty.svg') }}" alt="{{ $i }} star" data-empty="{{ asset('assets/icons/star-empty.svg') }}" data-filled="{{ asset('assets/icons/star-filled.svg') }}">
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="" required>
                @error('rating')
                <span class="event-reviews__form-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="event-reviews__form-field">
                <label for="comment">Your Review (optional)</label>
                <textarea name="comment" id="comment" rows="4" placeholder="Tell us about your experience...">{{ old('comment') }}</textarea>
                @error('comment')
                <span class="event-reviews__form-error">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn--primary">Submit Review</button>
        </form>
    </div>
    @elseif($hasReviewed)
    <div class="event-reviews__submitted">
        <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-reviews__submitted-icon">
        <p>Review kamu sudah terkirim.</p>
    </div>
    @endif

    {{-- Reviews List --}}
    @if($feedback->isNotEmpty())
    <div class="event-reviews__list">
        @foreach($feedback as $review)
        <div class="event-review-card">
            <div class="event-review-card__header">
                <div class="event-review-card__user">
                    @if($review->fromUser->avatar)
                        <img src="{{ asset('storage/' . $review->fromUser->avatar) }}" alt="" class="event-review-card__avatar">
                    @else
                        <div class="event-review-card__avatar-placeholder">
                            {{ strtoupper(substr($review->fromUser->name ?? $review->fromUser->username, 0, 1)) }}
                        </div>
                    @endif
                    <span class="event-review-card__name">{{ $review->fromUser->name ?? $review->fromUser->username }}</span>
                </div>
                <div class="event-review-card__rating">
                    @for($i = 1; $i <= 5; $i++)
                        <img src="{{ asset('assets/icons/' . ($i <= $review->rating ? 'star-filled.svg' : 'star-empty.svg')) }}" 
                             alt="" class="event-review-card__star">
                    @endfor
                </div>
            </div>
            @if($review->comment)
            <p class="event-review-card__comment">{{ $review->comment }}</p>
            @endif
            <span class="event-review-card__date">{{ $review->created_at->diffForHumans() }}</span>
        </div>
        @endforeach
    </div>
    @elseif($reviewCount === 0)
    <div class="event-empty">
        <img src="{{ asset('assets/icons/chat.svg') }}" alt="" class="event-empty__icon">
        <p class="event-empty__text">Belum ada review. Jadi yang pertama, yuk!</p>
    </div>
    @endif
</div>

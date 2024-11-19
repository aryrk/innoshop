<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Panel\Controllers;

use App\Events\ReviewUpdated;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoShop\Common\Models\Review;
use InnoShop\Common\Repositories\ReviewRepo;
use InnoShop\Panel\Requests\ReviewRequest;
use Throwable;

class ReviewController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data = [
            'criteria' => ReviewRepo::getCriteria(),
            'reviews' => ReviewRepo::getInstance()->list($filters),
        ];

        return inno_view('panel::reviews.index', $data);
    }

    /**
     * Review creation page.
     *
     * @return mixed
     * @throws Exception
     */
    public function create(): mixed
    {
        return $this->form(new Review);
    }

    /**
     * @param  ReviewRequest  $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(ReviewRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();
            $review = ReviewRepo::getInstance()->create($data);

            // Broadcast event
            broadcast(new ReviewUpdated($review))->toOthers();
            \Log::info('Event ReviewUpdated dipancarkan', ['review' => $review]);

            return redirect(panel_route('reviews.index'))
                ->with('instance', $review)
                ->with('success', panel_trans('common.updated_success'));
        } catch (Exception $e) {
            return redirect(panel_route('reviews.index'))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Review  $review
     * @return mixed
     * @throws Exception
     */
    public function edit(Review $review): mixed
    {
        return $this->form($review);
    }

    /**
     * @param  $review
     * @return mixed
     * @throws Exception
     */
    public function form($review): mixed
    {
        $data = [
            'review' => $review,
        ];

        return inno_view('panel::reviews.form', $data);
    }

    /**
     * @param  ReviewRequest  $request
     * @param  Review  $review
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        try {
            $data = $request->all();
            ReviewRepo::getInstance()->update($review, $data);

            // Broadcast event
            broadcast(new ReviewUpdated($review))->toOthers();
            \Log::info('Event broadcasted', ['review' => $review]);
            \Log::info('Event ReviewUpdated dipancarkan', ['review' => $review]);

            return redirect(panel_route('reviews.index'))
                ->with('instance', $review)
                ->with('success', panel_trans('common.updated_success'));
        } catch (Exception $e) {
            return redirect(panel_route('reviews.index'))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Review  $review
     * @return RedirectResponse
     */
    public function destroy(Review $review): RedirectResponse
    {
        try {
            ReviewRepo::getInstance()->destroy($review);

            // Broadcast event
            broadcast(new ReviewUpdated($review))->toOthers();
            \Log::info('Event ReviewUpdated dipancarkan', ['review' => $review]);

            return back()->with('success', panel_trans('common.deleted_success'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function latest()
    {
        \Log::info('Fetching latest reviews');

        $reviews = Review::with('customer', 'product')
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'customer_name' => $review->customer->name ?? 'Anonymous',
                    'content' => $review->content,
                    'rating' => $review->rating,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            });

        \Log::info('Latest reviews fetched', ['reviews' => $reviews]);
        \Log::info('Broadcasting ReviewUpdated event', ['review' => $review]);

        return response()->json($reviews, 200);
    }

}

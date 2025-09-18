<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $myReviews = $user->receivedReviews()->with('reviewer')->orderBy('created_at', 'desc')->get();

        $reviewCount = $myReviews->count();

        $sumStars = 0;
        $myReviews->pluck('star')
            ->each(function ($e) use (&$sumStars) {
                $sumStars += $e;
            });

        if ($sumStars) {
            $averageStar = $sumStars / $reviewCount;

            $roundedAverageStar = round($averageStar, 2);
        }



        return inertia('Reviews/Reviews', [
            'visitor' => false,
            'averageStar' => $roundedAverageStar ?? 0.00,
            'sortedReviews' => $myReviews,
        ]);
    }

    public function visitProfile(User $id)
    {
        $user = Auth::user();

        if ($user->id === $id->id) {
            return redirect()->route('review.my-profile');
        }

        $owedReviews = null;
        if (
            $user->roles->first()->name === 'Employer'
            && ($id->roles->first()->name === 'Senior' || $id->roles->first()->name === 'PWD')
        ) {
            foreach ($user->employerJobPosts as $job) {
                if ($job->employedWorkers()->where('worker_id', $id->id)->where('reviewed_by_employer', false)->exists()) {
                    $owedReviews++;
                }
            }
        } elseif (
            $id->roles->first()->name === 'Employer'
            && ($user->roles->first()->name === 'Senior' || $user->roles->first()->name === 'PWD')
        ) {
            foreach ($id->employerJobPosts as $job) {
                if ($job->employedWorkers()->where('worker_id', $user->id)->where('reviewed_by_worker', false)->exists()) {
                    $owedReviews++;
                }
            }
        }

        $myReviews = $id->receivedReviews()->with('reviewer')->where('reviewer_id', $user->id)->orderBy('created_at', 'desc')->get();

        $otherReviews = $id->receivedReviews()->with('reviewer')->where('reviewer_id', '!=', $user->id)->orderBy('created_at', 'desc')->get();

        $sortedReviews = $myReviews->merge($otherReviews);

        //get the average of the stars
        $sumStars = 0;
        $reviewCount = $sortedReviews->count();
        $sortedReviews->pluck('star')->each(
            function ($e) use (&$sumStars) {

                $sumStars += $e;
            }
        );

        if ($sumStars) {
            $averageStar = $sumStars / $reviewCount;

            $roundedAverageStar = round($averageStar, 2);
        }


        return inertia('Reviews/Reviews', [
            'visitor' => true,
            'owedReviews' => $owedReviews,
            'sortedReviews' => $sortedReviews,
            'averageStar' => $roundedAverageStar ?? 0.00
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $id)
    {
        $fields = $request->validate([
            'comment' => 'nullable|string|max:250',
            'star' => 'required|integer|min:1|max:5'
        ]);

        // Trim comment just in case
        $fields['comment'] = $fields['comment']
            ? mb_substr($fields['comment'], 0, 500)
            : null;

        $user = Auth::user();

        $user->myReviews()->create([
            'star' => $fields['star'],
            'comment' => $fields['comment'],
            'reviewee_id' => $id->id
        ]);

        // just find the first row in the table and update it.
        if ($user->roles->first()->name === 'Employer') {
            foreach ($user->employerJobPosts as $job) {
                if ($job->employedWorkers()->where('worker_id', $id->id)->where('reviewed_by_employer', false)->exists()) {
                    $record = DB::table('hired_workers')->where('job_post_id', $job->id)
                        ->where('worker_id', $id->id)->where('reviewed_by_employer', false)->first();

                    if ($record) {
                        DB::table('hired_workers')
                            ->where('id', $record->id)
                            ->update(['reviewed_by_employer' => true]);
                    }

                    break;
                }
            }
        }

        if ($user->roles->first()->name === 'Senior' || $user->roles->first()->name === 'PWD') {
            foreach ($id->employerJobPosts as $job) {
                if ($job->employedWorkers()->where('worker_id', $user->id)->where('reviewed_by_worker', false)->exists()) {
                    $record = DB::table('hired_workers')->where('job_post_id', $job->id)
                        ->where('worker_id', $user->id)->where('reviewed_by_worker', false)->first();

                    if ($record) {
                        DB::table('hired_workers')
                            ->where('id', $record->id)
                            ->update([
                                'reviewed_by_worker' => true
                            ]);
                    }

                    break;
                }
            }
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $fields = $request->validate([
            'comment' => 'nullable|string|max:250',
            'star' => 'required|integer|min:1|max:5',
        ]);
        // Trim comment just in case
        $fields['comment'] = $fields['comment']
            ? mb_substr($fields['comment'], 0, 500)
            : null;

        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $review->update($fields);

        return redirect()->back()->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}

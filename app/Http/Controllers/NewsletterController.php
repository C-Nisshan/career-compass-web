<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Handle the newsletter subscription.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:newsletter_subscriptions,email',
        ]);

        NewsletterSubscription::create([
            'uuid' => Str::uuid(),
            'email' => $validated['email'],
            'user_id' => Auth::check() ? Auth::id() : null,
            'subscribed_at' => now(),
        ]);

        return redirect()->route('contact')->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
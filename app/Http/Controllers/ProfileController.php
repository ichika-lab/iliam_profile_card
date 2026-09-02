<?php

namespace App\Http\Controllers;

use App\Models\Profile;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $profile = $request->user()->profile ?? new Profile();
        return Inertia::render('Profile/EditCard', [
            // 'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            // 'status' => session('status'),
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validate([
            'display_name'     => ['required', 'string', 'max:255'],
            'oshi_mark'        => ['nullable', 'string', 'max:255'],
            'fan_name'         => ['nullable', 'string', 'max:255'],
            'bio'              => ['nullable', 'string'],
            'avatar_url'       => ['nullable', 'url', 'max:255'],
            'theme_color'      => ['required', 'string', 'max:7'],
            'iriam_url'        => ['nullable', 'url', 'max:255'],
            'twitter_id'       => ['nullable', 'string', 'max:255'],
            'marshmallow_url'  => ['nullable', 'url', 'max:255'],
        ]);

        // ログインユーザーに紐づくプロフィールを更新（無ければ新規作成）
        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return redirect()->back()->with('status', 'profile-updated');
        
        // $request->user()->fill($request->validated());

        // if ($request->user()->isDirty('email')) {
        //     $request->user()->email_verified_at = null;
        // }

        // $request->user()->save();

        // return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

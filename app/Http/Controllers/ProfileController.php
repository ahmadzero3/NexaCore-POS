<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
         $user = User::find(auth()->user()->id);

         return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $request->user()->fill($request->validated());

            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $request->user()->avatar   = $this->uploadImage($request->file('avatar'));
            }

            $request->user()->save();

            return response()->json([
                'message' => __('app.record_updated_successfully'),
            ]);
        } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 409);

        }
    }
    private function uploadImage($image) : String{
        // Generate a unique filename for the image
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();

        // Save the image to the storage disk
        Storage::putFileAs('public/images/avatar', $image, $filename);

        return $filename;
    }
    public function updatePassword(PasswordRequest $request): JsonResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return response()->json([
            'message' => __('app.password_updated_successfully'),
        ]);
    }
}

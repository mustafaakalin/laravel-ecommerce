<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'identity_number' => ['nullable', 'string', 'max:11'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'instagram_account' => ['nullable', 'string', 'max:255'],
            'facebook_account' => ['nullable', 'string', 'max:255'],
            'tiktok_account' => ['nullable', 'string', 'max:255'],
            'x_account' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profil bilgileriniz güncellendi.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Şifreniz başarıyla güncellendi.');
    }

    public function orders()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['items.product', 'address'])
            ->latest()
            ->get();

        return view('profile.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items.product', 'address', 'shipment']);

        return view('profile.order-detail', compact('order'));
    }
}

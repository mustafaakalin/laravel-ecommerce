<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class ProfileAddressController extends Controller
{
    private const MAX_ADDRESSES = 4;

    public function index()
    {
        $addresses = auth()->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('profile.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('profile.addresses.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->addresses()->count() >= self::MAX_ADDRESSES) {
            return redirect()
                ->route('profile.addresses.index')
                ->with('error', 'En fazla '.self::MAX_ADDRESSES.' adres ekleyebilirsiniz.');
        }

        $data = $this->validateAddress($request);

        $isDefault = $request->boolean('is_default');

        $address = auth()->user()->addresses()->create($data + ['is_default' => $isDefault]);

        if ($isDefault) {
            $this->makeDefault($address);
        }

        return redirect()
            ->route('profile.addresses.index')
            ->with('success', 'Adresiniz eklendi.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);

        return view('profile.addresses.edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $data = $this->validateAddress($request);

        $isDefault = $request->boolean('is_default');

        $address->update($data + ['is_default' => $isDefault]);

        if ($isDefault) {
            $this->makeDefault($address);
        }

        return redirect()
            ->route('profile.addresses.index')
            ->with('success', 'Adresiniz güncellendi.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);

        $address->delete();

        if (auth()->user()->addresses()->where('is_default', true)->doesntExist()) {
            auth()->user()->addresses()->first()?->update(['is_default' => true]);
        }

        return redirect()
            ->route('profile.addresses.index')
            ->with('success', 'Adresiniz silindi.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeAddress($address);

        $this->makeDefault($address);

        return redirect()
            ->route('profile.addresses.index')
            ->with('success', 'Varsayılan adres güncellendi.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:50'],
            'state' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:50'],
            'zip_code' => ['required', 'string', 'max:10'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'tax_office' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function makeDefault(Address $address): void
    {
        auth()->user()->addresses()
            ->where('id', '!=', $address->id)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);
    }

    private function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === auth()->id(), 403);
    }
}

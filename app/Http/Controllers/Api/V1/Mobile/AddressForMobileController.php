<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Mobile\AddressForMobileResource;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Permission;

class AddressForMobileController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $addresses = $user->addresses()
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => AddressForMobileResource::collection($addresses)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching addresses: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch addresses'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zip_code' => 'required|numeric|digits:5',
            'is_default' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Set the new address as default and unset the previous default
            if ($request->input('is_default', false)) {
                Address::where('user_id', $user->id)->update(['is_default' => false]);
            }

            // If it's the user's first address, set it as default
            if ($user->addresses()->count() === 0) {
                $request->merge(['is_default' => true]);
            }

            $address = $user->addresses()->create($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Address created successfully',
                'data' => new AddressForMobileResource($address)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating address: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create address'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => new AddressForMobileResource($address)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => ['sometimes', 'required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',
            'zip_code' => 'sometimes|required|numeric|digits:5',
            'is_default' => 'sometimes|boolean',
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:20',
            'tax_office' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // Set the updated address as default and unset the previous default
            if ($request->has('is_default') && $request->input('is_default')) {
                Address::where('user_id', $user->id)->update(['is_default' => false]);
            }

            $address->update($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Address updated successfully',
                'data' => new AddressForMobileResource($address)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating address: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update address'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // If the deleted address was the default, set another as default if available
            if ($address->is_default) {
                $nextAddress = $user->addresses()->first();
                if ($nextAddress) {
                    $nextAddress->is_default = true;
                    $nextAddress->save();
                }
            }

            $address->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Address deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting address: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Address not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}

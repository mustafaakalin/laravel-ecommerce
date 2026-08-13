<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
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
                'data' => AddressResource::collection($addresses)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Adresler alınırken bir hata oluştu'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
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
            ]);

            DB::beginTransaction();
            
            $user = Auth::user();

            // Eğer yeni adres varsayılan olarak işaretlendiyse, diğer varsayılan adresleri false yap
            if ($request->input('is_default', false)) {
                $user->addresses()->update(['is_default' => false]);
            }

            // Eğer bu kullanıcının ilk adresi ise, otomatik olarak varsayılan yap
            if ($user->addresses()->count() === 0) {
                $validated['is_default'] = true;
            }

            $address = $user->addresses()->create($validated);
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Adres başarıyla eklendi',
                'data' => new AddressResource($address)
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Adres eklenirken bir hata oluştu'
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
                'data' => new AddressResource($address)
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Yetkisiz Erişim İsteği Tespit Edildi!!! , Adres bulunamadı'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'phone' => ['sometimes', 'required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
                'address' => 'sometimes|required|string|max:255',
                'city' => 'sometimes|required|string|max:255',
                'state' => 'sometimes|required|string|max:255',
                'country' => 'sometimes|required|string|max:255',
                'zip_code' => 'sometimes|required|string|max:20',
                'is_default' => 'sometimes|required|boolean',
            ]);

            // DB::beginTransaction();

            $user = Auth::user();
            $address = $user->addresses()->findOrFail($id);

            // Eğer varsayılan adres olarak işaretlendiyse, diğer adresleri güncelle
            if ($request->has('is_default') && $request->input('is_default')) {
                $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
            }

            $address->update($validated);

            // DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Adres başarıyla güncellendi',
                'data' => new AddressResource($address)
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Yetkisiz Erişim İsteği Tespit Edildi !!! , Adres bulunamadı'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Adres güncellenirken bir hata oluştu'
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

            // Varsayılan adres siliniyorsa ve başka adres varsa, ilk adresi varsayılan yap
            if ($address->is_default) {
                $newDefaultAddress = $user->addresses()
                    ->where('id', '!=', $id)
                    ->first();
                    
                if ($newDefaultAddress) {
                    $newDefaultAddress->update(['is_default' => true]);
                }
            }

            $address->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Adres başarıyla silindi'
            ], Response::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Adres bulunamadı'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Adres silinirken bir hata oluştu'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function couponcheck(Request $request)
    {
        // Validasyon
        try {
            $request->validate([
                'coupon_code' => 'required|string|max:100',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Kupon kodunu al
        $coupon_code = $request->coupon_code;
        // Kupon kodunu kontrol et
        $coupon = Coupon::where('code', $coupon_code)->first();
        if (!$coupon) {
            return response()->json(['error' => 'Kupon kodu geçersiz.'], 400);
        }
        // Kupon kodu geçerlidir
        return response()->json(['message' => 'Kupon kodu geçerlidir.'], 200);
    }
}

<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckoutStoreController extends Controller
{
    //
    public function checkoutWithComprobante(Request $request)
    {
        $request->validate([
            'comprobante' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'method_payment' => 'required|in:1',
            'total' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();
    
            $user = auth('api')->user();
            $comprobantePath = $request->file('comprobante')->store('comprobantes', 'public');

            $saleId = DB::table('sales')->insertGetId([
                'user_id' => $user->id,
                'method_payment' => 'DEPOSITO',
                'currency_total' => 'GTQ',
                'currency_payment' => 'GTQ',
                'total' => $request->total,
                'n_transaccion' => $comprobantePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $carts = DB::table('carts')->where('user_id', $user->id)->get();

            foreach ($carts as $cart) {
                DB::table('sale_details')->insert([
                    'sale_id' => $saleId,
                    'course_id' => $cart->course_id,
                    'type_discount' => $cart->type_discount,
                    'discount' => $cart->discount,
                    'type_campaing' => $cart->type_campaing,
                    'code_cupon' => $cart->code_cupon,
                    'code_discount' => $cart->code_discount,
                    'precio_unitario' => $cart->precio_unitario,
                    'total' => $cart->total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('courses_students')->insert([
                    'course_id' => $cart->course_id,
                    'user_id' => $user->id,
                    'state' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('carts')->where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json(['message' => 'Checkout realizado con éxito'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('public')->delete($comprobantePath);
            return response()->json(['message' => 'Error al procesar el checkout', 'error' => $e->getMessage()], 500);
        }
    }
}

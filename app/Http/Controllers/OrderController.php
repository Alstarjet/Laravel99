<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function store(Request $request)
    {
        Log::info($request);
        try {
            $request->validate([
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
                'address' => 'required|string|max:255',
                'zipcode' => 'required|integer',
                'ext_num' => 'required|integer',
                'int_num' => 'nullable|integer',
                'products' => 'required|array',
                'products.*' => 'required|integer',
            ]);
            foreach ($request->products as $productData) {
                if ($productData>25){
                    return response()->json(['message' => 'Uno de tus productos es mallor a 25 kg contactanos a 0180099954 para una convenio especial']);
                }
            }
            // Crea una nueva orden
            $order = Order::create([
                'lat' => $request->lat,
                'lon' => $request->lon,
                'address' => $request->address,
                'zipcode' => $request->zipcode,
                'ext_num' => $request->ext_num,
                'int_num' => $request->int_num,
                'status' => "creado",
            ]);
            Log::info($order);
            foreach ($request->products as $productData) {
                $size = ($productData < 5) ? 'S' : (($productData < 15) ? 'M' : 'L');
                $product = Product::create([
                    'order_id'=>$order->id,
                    'size' => $size,
                ]);

                $order->products()->save($product);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error en los datos enviados', 'errors' => $e->errors()], 422);
        }

        // Responde con la orden creada
        return response()->json($order);
    }

}
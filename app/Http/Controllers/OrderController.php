<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class OrderController extends Controller
{
    public function index()
    {
        $users = Order::all();
        $userF = array();
        foreach ($users as $user) {
            $productos = Product::where('order_id', $user->id)->select('size')->get();
            $orden = ["orden" => $user, "Productos" => $productos];
            array_push($userF, $orden);
        }
        return response()->json($userF);
    }
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
                if ($productData > 25) {
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
                    'order_id' => $order->id,
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
    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'integer|required|exists:orders,id',
            ]);
            $orden = Order::find($request->id);
            switch ($orden->status) {
                case 'creado':
                    $orden->status = 'recolectado';
                    $orden->save();
                    break;
                case 'recolectado':
                    $orden->status = 'en_estacion';
                    $orden->save();
                    break;
                case 'en_estacion':
                    $orden->status = 'en_ruta';
                    $orden->save();
                    break;
                case 'en_ruta':
                    $orden->status = 'entregado';
                    $orden->save();
                    break;
                case 'entregado':
                    return response()->json(['message' => 'Tu orden ya fue entregada no puede actualizarce', 'orden' => $orden], 400);
                case 'cancelado':
                    return response()->json(['message' => 'Esta orden ya fue canselada no puede actualizarce', 'orden' => $orden], 400);
                default:
                    return response()->json(['message' => 'Error no Identificado'], 400);
            }
            return response()->json(['message' => $orden]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error en los datos enviados', 'errors' => $e->errors()], 422);
        }
    }
    public function cancel(Request $request)
    {
        try {
            $request->validate([
                'id' => 'integer|required|exists:orders,id',
            ]);
            $orden = Order::find($request->id);
            $fecha_actual = Carbon::now();
            $fecha1 = Carbon::parse($fecha_actual);
            $fecha2 = Carbon::parse($orden->created_at);
            $diferencia = $fecha2->diffInMinutes($fecha1,true);
            $reembolso ='Se realizara el rembolso';
            if ($diferencia>2){
                $reembolso='Ya no fuiste agredor al rembolso timepo limite 2 min';
            };

            switch ($orden->status) {
                case 'creado':
                    $orden->status = 'cancelado';
                    $orden->save();
                    break;
                case 'recolectado':
                    $orden->status = 'cancelado';
                    $orden->save();
                    break;
                case 'en_estacion':
                    $orden->status = 'cancelado';
                    $orden->save();
                    break;
                case 'en_ruta':
                    return response()->json(['message' => 'Tu orden esta en ruta no puede cancelarce', 'orden' => $orden], 400);
                case 'entregado':
                    return response()->json(['message' => 'Tu orden ya fue entregada no puede cancelarce', 'orden' => $orden], 400);
                case 'cancelado':
                    return response()->json(['message' => 'Ya fue cancelada', 'orden' => $orden], 400);
                default:
                    return response()->json(['message' => 'Error no Identificado'], 400);
            }
            return response()->json(['cancelacion'=>$reembolso,'message' => $orden]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error en los datos enviados', 'errors' => $e->errors()], 422);
        }
    }
    public function show($numero){
        $orden = Order::find($numero);
        if ($orden==null){
            return response()->json(['message' => "la orden no existe"],400);
        }
        return response()->json(['tuya' => $orden]);
    }
}
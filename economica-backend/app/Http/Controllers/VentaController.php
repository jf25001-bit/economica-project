<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // Mostrar todas las ventas
    public function index()
    {
        // Trae las ventas con el usuario que atendió y sus productos correspondientes
        $ventas = Venta::with(['usuario', 'detalles.producto'])->get();
        return response()->json($ventas, 200);
    }

    // Guardar venta
    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'productos' => 'required|array|min:1',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        // 1. Crear el registro principal de la venta
        $venta = Venta::create([
            'cliente' => $request->input('cliente', 'Consumidor Final'),
            'user_id' => $request->user_id,
            'total' => 0
        ]);

        $totalVenta = 0;

        // 2. Recorrer la lista de productos
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['producto_id']);

            if ($producto->stock < $item['cantidad']) {
                return response()->json([
                    'message' => "Stock insuficiente para el producto: {$producto->nombre}. Disponible: {$producto->stock}"
                ], 400);
            }

            $subtotal = $producto->precio_venta * $item['cantidad'];
            $totalVenta += $subtotal;

            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal
            ]);

            $producto->decrement('stock', $item['cantidad']);
        }

        // 3. Actualizar el importe total definitivo
        $venta->update(['total' => $totalVenta]);

        DB::commit();

        return response()->json([
            'message' => 'Venta procesada con éxito y stock actualizado',
            'data' => $venta->load('detalles')
        ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $venta = Venta::with(['usuario', 'detalles.producto'])->find($id);

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        return response()->json($venta, 200);
    }

    // Actualizar venta
    public function update(Request $request, $id)
    {
        try {

            $venta = Venta::findOrFail($id);

            $validated = $request->validate([
                'fecha_venta' => 'sometimes|date',
                'total' => 'sometimes|numeric',
                'cliente_id' => 'sometimes'
            ]);

            $venta->update($validated);

            return response()->json([
                'message' => 'Venta actualizada correctamente',
                'data' => $venta
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }

    // Eliminar venta
    public function destroy($id)
    {
        try {

            $venta = Venta::findOrFail($id);

            $venta->delete();

            return response()->json([
                'message' => 'Venta eliminada correctamente'
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }
    }
}
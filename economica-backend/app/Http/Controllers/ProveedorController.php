<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Cargar la relación de productos junto con los proveedores
            $proveedores = Proveedor::with('productos')->get();

            return response()->json($proveedores);

        } catch(\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'nombre_proveedor' => 'required|string|min:2|max:80|unique:proveedores,nombre_proveedor',
                    'telefono' => 'required|string|max:15',
                    'direccion' => 'required|string|max:255',
                    'productos' => 'nullable|array',
                    'productos.*' => 'exists:productos,id' // Valida que los IDs existan
                ],
                [
                    'nombre_proveedor.unique' => 'Ya existe un proveedor con este nombre'
                ]
            );

            // 1. Crear el proveedor
            $proveedor = Proveedor::create([
                'nombre_proveedor' => $request->nombre_proveedor,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion
            ]);

            // 2. Sincronizar los productos en la tabla pivote
            if ($request->has('productos')) {
                $proveedor->productos()->sync($request->productos);
            }

            // Cargar la relación para devolver el objeto completo
            $proveedor->load('productos');

            return response()->json([
                'message' => 'Proveedor registrado correctamente',
                'proveedor' => $proveedor
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errores' => $e->errors()
            ], 422);

        } catch(\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Cargar el proveedor con sus productos
            $proveedor = Proveedor::findOrFail($id)->load('productos');

            return response()->json($proveedor);

        } catch(ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Proveedor no encontrado con ID = '.$id
            ], 404);

        } catch(\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el proveedor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $proveedor = Proveedor::with('productos')->findOrFail($id);

            $request->validate([
                'nombre_proveedor' => [
                    'required',
                    'string',
                    'min:2',
                    'max:80',
                    Rule::unique('proveedores', 'nombre_proveedor')->ignore($id)
                ],
                'telefono' => 'required|string|max:15',
                'direccion' => 'required|string|max:255',
                'productos' => 'nullable|array',
                'productos.*' => 'exists:productos,id'
            ]);

            // Validar si hay compras hechas con productos que se quieren remover
            if ($request->has('productos')) {
                $nuevosProductosIds = collect($request->productos);
                $productosActualesIds = $proveedor->productos->pluck('id');

                // Detectar qué productos se quieren quitar
                $productosQuitados = $productosActualesIds->diff($nuevosProductosIds);

                if ($productosQuitados->isNotEmpty()) {
                    // Verificar si alguno de esos productos quitados tiene compras registradas con este proveedor en detalle_compras
                    $tieneCompras = DB::table('detalle_compras')
                        ->where('proveedor_id', $proveedor->id)
                        ->whereIn('producto_id', $productosQuitados)
                        ->exists();

                    if ($tieneCompras) {
                        return response()->json([
                            'message' => 'No se pueden desvincular productos que ya tienen órdenes de compra registradas con este proveedor.'
                        ], 422);
                    }
                }
            }

            // 1. Actualizar los datos del proveedor
            $proveedor->update([
                'nombre_proveedor' => $request->nombre_proveedor,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion
            ]);

            // 2. Sincronizar los productos actualizados
            if ($request->has('productos')) {
                $proveedor->productos()->sync($request->productos);
            }

            $proveedor->load('productos');

            return response()->json([
                'message' => 'Proveedor actualizado correctamente',
                'proveedor' => $proveedor
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el proveedor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $proveedor = Proveedor::with('productos')->findOrFail($id);

            // Validar si tiene productos afiliados
            if ($proveedor->productos()->count() > 0) {
                return response()->json([
                    'message' => "No se puede eliminar el proveedor \"{$proveedor->nombre_proveedor}\" porque tiene productos afiliados."
                ], 422);
            }

            $proveedor->delete();

            return response()->json([
                'message' => 'Proveedor eliminado correctamente'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Proveedor no encontrado con ID = ' . $id
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       try{

            $proveedores = Proveedor::all();

            return response()->json($proveedores);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         try{

            $request->validate(
                [
                    'nombre_proveedor' => 'required|string|min:2|max:80|unique:proveedores,nombre_proveedor',
                    'telefono' => 'required|string|max:15',
                    'direccion' => 'required|string|max:255'
                ],
                [
                    'nombre_proveedor.unique' => 'Ya existe un proveedor con este nombre'
                ]
            );

            $proveedor = Proveedor::create([
                'nombre_proveedor' => $request->nombre_proveedor,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion
            ]);

            return response()->json([
                'message' => 'Proveedor registrado correctamente',
                'proveedor' => $proveedor
            ],201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación.',
                'errores' => $e->errors()
            ],422);

        } catch(\Exception $e){

            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ],500);
        }
       
    }
       

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{

        $proveedor = Proveedor::findOrFail($id);

        return response()->json($proveedor);

    } catch(ModelNotFoundException $e){

        return response()->json([
            'message' => 'Proveedor no encontrado    con ID = '.$id
        ],404);

    } catch(\Exception $e){

        return response()->json([
            'message' => 'Proveedor no encontrado',
            'error' => $e->getMessage()
        ],500);
    }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         try{

        $proveedor = Proveedor::findOrFail($id);

        $request->validate(
            [
                'nombre_proveedor' => [
                    'required',
                    'string',
                    'min:2',
                    'max:80',
                    Rule::unique('proveedores','nombre_proveedor')->ignore($id)
                ],
                'telefono' => 'required|string|max:15',
                'direccion' => 'required|string|max:255'
            ]
        );

        $proveedor->update([
            'nombre_proveedor' => $request->nombre_proveedor,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion
        ]);

        return response()->json([
            'message' => 'Proveedor actualizado correctamente',
            'proveedor' => $proveedor
        ],202);

    } catch(\Exception $e){

        return response()->json([
            'message' => 'Proveedor no encontrado',
            'error' => $e->getMessage()
        ],500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         try {

        $proveedor = Proveedor::findOrFail($id);

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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;

class ImagenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Imagen::all());
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
         $request->validate([
        'imagen' => 'required|image|mimes:jpg,jpeg,png,jfif|max:2048',
        'producto_id' => 'required|exists:productos,id' 
    ]);

    $ruta = $request->file('imagen')->store('imagenes', 'public');

    $imagen = Imagen::create([
        'ruta' => $ruta,
        'producto_id' => $request->producto_id
    ]);

    return response()->json([
        'message' => 'Imagen guardada correctamente',
        'data' => $imagen
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagen = Imagen::findOrFail($id);

    // Eliminar archivo físico
    if (\Storage::disk('public')->exists($imagen->ruta)) {
        \Storage::disk('public')->delete($imagen->ruta);
    }

    // Eliminar registro de BD
    $imagen->delete();

    return response()->json([
        'message' => 'Imagen eliminada correctamente'
    ]);
    }
}

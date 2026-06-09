<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index()
    {
        try {

            $lotes = Lote::with([
                'producto'
            ])->get();

            return response()->json(
                $lotes
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al obtener lotes',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {

            $lote = Lote::with([
                'producto'
            ])->findOrFail($id);

            return response()->json(
                $lote
            );

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Lote no encontrado'
            ], 404);
        }
    }

    public function update(
        Request $request,
        $id
    ) {
        try {

            $lote =
                Lote::findOrFail($id);

            $request->validate([
                'numero_lote' =>
                    'nullable|string|max:100',

                'fecha_expiracion' =>
                    'nullable|date',

                'cantidad_actual' =>
                    'nullable|integer|min:0'
            ]);

            $lote->update(
                $request->all()
            );

            return response()->json([
                'message' =>
                    'Lote actualizado',

                'data' =>
                    $lote
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al actualizar lote',

                'error' =>
                    $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $lote =
                Lote::findOrFail($id);

            $lote->delete();

            return response()->json([
                'message' =>
                    'Lote eliminado'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' =>
                    'Error al eliminar lote'
            ], 500);
        }
    }
}
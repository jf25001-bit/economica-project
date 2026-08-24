<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * Obtener el estado actual de la caja activa DEL USUARIO AUTENTICADO.
     */
    public function estadoActual(Request $request)
    {
        $userId = Auth::id() ?? auth('api')->id() ?? $request->input('user_id') ?? $request->header('X-User-Id');

        $caja = Caja::where('user_id', $userId)
            ->where('estado', 'abierta')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'caja' => $caja,
            'monto_anterior' => 0.00
        ]);
    }

    /**
     * Abrir una nueva caja garantizando que NO exista NINGUNA otra caja abierta en el sistema.
     */
    public function abrir(Request $request)
    {
        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        $userId = Auth::id() ?? auth('api')->id() ?? $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'message' => 'No se pudo identificar al usuario autenticado.'
            ], 401);
        }

        // VALIDACIÓN GLOBAL
        $cajaAbiertaGlobal = Caja::where('estado', 'abierta')->first();

        if ($cajaAbiertaGlobal) {
            return response()->json([
                'message' => 'No se puede abrir caja. Ya existe una caja abierta en el sistema. Se debe realizar el cierre antes de aperturar un nuevo turno.'
            ], 400);
        }

        $caja = Caja::create([
            'user_id'        => (int)$userId,
            'monto_apertura' => $request->monto_apertura,
            'total_ventas'   => 0.00,
            'estado'         => 'abierta',
            'fecha_apertura' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Caja aperturada exitosamente.',
            'caja'    => $caja
        ], 201);
    }

    /**
     * Cerrar la caja activa del usuario actual.
     */
    public function cerrar(Request $request)
    {
        $request->validate([
            'monto_cierre' => 'required|numeric|min:0'
        ]);

        $userId = Auth::id() ?? auth('api')->id() ?? $request->input('user_id');

        $caja = Caja::where('user_id', $userId)
            ->where('estado', 'abierta')
            ->first();

        if (!$caja) {
            return response()->json([
                'message' => 'No tienes ninguna caja abierta para cerrar.'
            ], 400);
        }

        $caja->update([
            'monto_cierre' => $request->monto_cierre,
            'fecha_cierre' => Carbon::now(),
            'estado'       => 'cerrada'
        ]);

        return response()->json([
            'message' => 'Caja cerrada exitosamente.',
            'caja'    => $caja
        ]);
    }

    /**
     * Obtener todas las cajas abiertas en el sistema (Para Control de Cajas)
     */
    public function cajasActivas()
    {
        $cajas = Caja::where('estado', 'abierta')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($cajas);
    }

    /**
     * Obtener el historial completo de cajas (Para Control de Cajas)
     */
    public function historial()
    {
        $historial = Caja::orderBy('id', 'desc')->get();

        return response()->json($historial);
    }

    /**
     * Forzar el cierre de una caja específica desde el módulo de administración
     */
    public function forzarCierre(Request $request, $id)
    {
        $caja = Caja::find($id);

        if (!$caja) {
            return response()->json(['message' => 'Caja no encontrada.'], 404);
        }

        $caja->update([
            'monto_cierre' => $request->input('monto_cierre', $caja->monto_apertura),
            'fecha_cierre' => Carbon::now(),
            'estado'       => 'cerrada',
            'observacion'  => $request->input('observacion', 'Cierre forzado por Admin')
        ]);

        return response()->json([
            'message' => 'Caja cerrada forzosamente con éxito.',
            'caja'    => $caja
        ]);
    }
}
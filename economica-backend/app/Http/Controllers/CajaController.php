<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * Verifica si el usuario autenticado es administrador.
     */
    private function esAdministrador()
    {
        $user = Auth::user() ?? auth('api')->user();
        $rol = strtolower(trim($user->rol->nombre ?? $user->rol ?? ''));
        return $rol === 'administrador';
    }

    /**
     * Obtener el estado actual de la caja abierta en el sistema.
     */
    public function estadoActual(Request $request)
    {
        $caja = Caja::where('estado', 'abierta')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'caja' => $caja,
            'monto_anterior' => 0.00
        ]);
    }

    /**
     * Abrir una nueva caja (solo administrador) garantizando que NO exista otra caja abierta.
     */
    public function abrir(Request $request)
    {
        if (!$this->esAdministrador()) {
            return response()->json([
                'message' => 'Solo el administrador puede abrir la caja.'
            ], 403);
        }

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
     * Cerrar la caja abierta (solo administrador).
     */
    public function cerrar(Request $request)
    {
        if (!$this->esAdministrador()) {
            return response()->json([
                'message' => 'Solo el administrador puede cerrar la caja.'
            ], 403);
        }

        $request->validate([
            'monto_cierre' => 'required|numeric|min:0'
        ]);

        $userId = Auth::id() ?? auth('api')->id() ?? $request->input('user_id');

        $caja = Caja::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json([
                'message' => 'No hay ninguna caja abierta para cerrar.'
            ], 400);
        }

        $caja->update([
            'monto_cierre'   => $request->monto_cierre,
            'fecha_cierre'   => Carbon::now(),
            'estado'         => 'cerrada',
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
        $cajas = Caja::with('usuario')
            ->where('estado', 'abierta')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($cajas);
    }

    /**
     * Obtener el historial completo de cajas (Para Control de Cajas)
     */
    public function historial()
    {
        $historial = Caja::with(['usuario', 'cerradoPor'])
            ->orderBy('id', 'desc')
            ->get();

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

        $adminId = Auth::id() ?? auth('api')->id() ?? $request->input('admin_id');

        $caja->update([
            'monto_cierre'   => $request->input('monto_cierre', $caja->monto_apertura + $caja->total_ventas),
            'fecha_cierre'   => Carbon::now(),
            'estado'         => 'cerrada',
            'observacion'    => $request->input('observacion', 'Cierre forzado por Administrador'),
            'cerrado_por_id' => $adminId,
        ]);

        return response()->json([
            'message' => 'Caja cerrada forzosamente con éxito.',
            'caja'    => $caja->load(['usuario', 'cerradoPor'])
        ]);
    }
}
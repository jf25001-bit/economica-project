<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CajaController extends Controller
{
    /**
     * Obtener el estado actual de la caja activa en el sistema.
     * Retorna siempre la caja abierta actual sin importar el user_id.
     */
    public function estadoActual(Request $request)
    {
        try {
            $caja = Caja::where('estado', 'abierta')
                ->orderBy('id', 'desc')
                ->first();

            return response()->json([
                'caja' => $caja,
                'monto_anterior' => 0.00
            ], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Abrir una nueva caja BLOQUEANDO la acción si ya existe un turno activo.
     */
    public function abrir(Request $request)
    {
        try {
            $request->validate([
                'monto_apertura' => 'required|numeric|min:0',
            ]);

            // 1. Validar si ya existe CUALQUIER caja abierta en el sistema
            $cajaActiva = Caja::where('estado', 'abierta')->first();

            if ($cajaActiva) {
                return response()->json([
                    'message' => 'No se puede abrir una nueva caja. Ya existe un turno activo que debe ser cerrado primero.'
                ], 400);
            }

            // 2. Determinar el usuario que realiza la apertura
            $userId = Auth::id() ?? auth('api')->id() ?? $request->input('user_id');

            if (!$userId) {
                return response()->json([
                    'message' => 'No se pudo identificar al usuario autenticado.'
                ], 401);
            }

            // 3. Crear el nuevo registro de caja
            $caja = Caja::create([
                'user_id'        => (int)$userId,
                'monto_apertura' => $request->monto_apertura,
                'total_ventas'   => 0.00,
                'estado'         => 'abierta',
                'fecha_apertura' => now(),
            ]);

            return response()->json([
                'message' => 'Caja aperturada exitosamente.',
                'caja'    => $caja
            ], 201);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error al abrir caja: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cerrar la caja activa actual.
     */
    public function cerrar(Request $request)
    {
        try {
            $request->validate([
                'monto_cierre' => 'required|numeric|min:0'
            ]);

            // Buscar la caja que se encuentra abierta en el sistema
            $caja = Caja::where('estado', 'abierta')
                ->orderBy('id', 'desc')
                ->first();

            if (!$caja) {
                return response()->json([
                    'message' => 'No se encontró ninguna caja abierta para cerrar.'
                ], 400);
            }

            // Actualizar datos de cierre
            $caja->update([
                'monto_cierre' => $request->monto_cierre,
                'fecha_cierre' => now(),
                'estado'       => 'cerrada'
            ]);

            return response()->json([
                'message' => 'Caja cerrada exitosamente.',
                'caja'    => $caja
            ], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Error al cerrar caja: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener todas las cajas abiertas en el sistema.
     */
    public function cajasActivas()
    {
        try {
            $cajas = Caja::with('user')
                ->where('estado', 'abierta')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($cajas, 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener el historial completo de cajas.
     */
    public function historial()
    {
        try {
            $historial = Caja::with('user')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($historial, 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Forzar el cierre de una caja específica desde administración.
     */
    public function forzarCierre(Request $request, $id)
    {
        try {
            $caja = Caja::find($id);

            if (!$caja) {
                return response()->json(['message' => 'Caja no encontrada.'], 404);
            }

            if ($caja->estado === 'cerrada') {
                return response()->json(['message' => 'La caja ya se encuentra cerrada.'], 400);
            }

            $totalVentas = (float) ($caja->total_ventas ?? 0);

            $montoCierreCalculado = $request->filled('monto_cierre') && $request->input('monto_cierre') !== null
                ? (float) $request->input('monto_cierre')
                : $totalVentas;

            $observacionTexto = $request->input('observacion') ?? 'Cierre forzado por Admin';

            $caja->estado       = 'cerrada';
            $caja->monto_cierre = $montoCierreCalculado;
            $caja->fecha_cierre = now();
            $caja->observacion  = substr((string)$observacionTexto, 0, 255);
            $caja->save();

            return response()->json([
                'message' => 'Caja cerrada forzosamente con éxito.',
                'caja'    => $caja
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Error en base de datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
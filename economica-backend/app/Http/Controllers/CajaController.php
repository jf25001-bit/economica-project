<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    // Horarios de los turnos (hora inicio incluida, hora fin excluida)
    private const TURNOS = [
        'manana' => ['inicio' => 0,  'fin' => 12, 'texto' => 'Mañana (12:00 a.m. a 11:59 a.m.)'],
        'tarde'  => ['inicio' => 12, 'fin' => 24, 'texto' => 'Tarde (12:00 p.m. a 11:59 p.m.)'],
        // 'unico' no tiene restricción de horario
    ];

    private function usuarioActual()
    {
        return Auth::user() ?? auth('api')->user();
    }

    private function esAdministrador()
    {
        $user = $this->usuarioActual();
        $rol = strtolower(trim($user->rol->nombre ?? $user->rol ?? ''));
        return $rol === 'administrador';
    }

    private function nombreDe($user)
    {
        return $user->nombre ?? $user->name ?? ('Usuario #' . ($user->id ?? '?'));
    }

    /**
     * Estado de la caja DEL USUARIO autenticado.
     */
    public function estadoActual(Request $request)
    {
        $user = $this->usuarioActual();

        $caja = Caja::where('estado', 'abierta')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();

        $otra = null;
        if (!$caja) {
            $otra = Caja::with('usuario')->where('estado', 'abierta')->first();
        }

        return response()->json([
            'caja' => $caja,
            'caja_otro_usuario' => $otra ? [
                'id'      => $otra->id,
                'usuario' => $otra->usuario ? $this->nombreDe($otra->usuario) : 'otro usuario',
            ] : null,
            'monto_anterior' => 0.00,
        ]);
    }

    /**
     * Abrir caja (solo administrador), validando turno y que no haya otra abierta.
     */
    public function abrir(Request $request)
    {
        if (!$this->esAdministrador()) {
            return response()->json(['message' => 'Solo el administrador puede abrir la caja.'], 403);
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'turno'          => 'nullable|in:unico,manana,tarde',
        ]);

        $user  = $this->usuarioActual();
        $turno = $request->input('turno', 'unico');

        // Validación de horario del turno
        if (isset(self::TURNOS[$turno])) {
            // Hora de El Salvador fija, sin depender de config/app.php
            $ahora = Carbon::now('America/El_Salvador');
            $hora  = $ahora->hour;
            $t = self::TURNOS[$turno];
            if ($hora < $t['inicio'] || $hora >= $t['fin']) {
                return response()->json([
                    'message' => "No puedes abrir el turno {$t['texto']} fuera de su horario. Hora actual del sistema: " . $ahora->format('h:i A') . "."
                ], 422);
            }
        }

        // Solo una caja abierta en todo el sistema
        $cajaAbiertaGlobal = Caja::with('usuario')->where('estado', 'abierta')->first();
        if ($cajaAbiertaGlobal) {
            $quien = $cajaAbiertaGlobal->usuario ? $this->nombreDe($cajaAbiertaGlobal->usuario) : 'otro usuario';
            return response()->json([
                'message' => "Ya existe una caja abierta por {$quien}. Debe cerrarse antes de abrir un nuevo turno."
            ], 400);
        }

        $caja = Caja::create([
            'user_id'        => $user->id,
            'monto_apertura' => $request->monto_apertura,
            'total_ventas'   => 0.00,
            'estado'         => 'abierta',
            'turno'          => $turno,
            'fecha_apertura' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Caja aperturada exitosamente.', 'caja' => $caja], 201);
    }

    /**
     * Cierre NORMAL: solo la caja propia del usuario autenticado.
     */
    public function cerrar(Request $request)
    {
        if (!$this->esAdministrador()) {
            return response()->json(['message' => 'Solo el administrador puede cerrar la caja.'], 403);
        }

        $request->validate(['monto_cierre' => 'required|numeric|min:0']);

        $user = $this->usuarioActual();

        $caja = Caja::where('estado', 'abierta')
            ->where('user_id', $user->id)
            ->first();

        if (!$caja) {
            $otra = Caja::with('usuario')->where('estado', 'abierta')->first();
            if ($otra) {
                $quien = $otra->usuario ? $this->nombreDe($otra->usuario) : 'otro usuario';
                return response()->json([
                    'message' => "La caja abierta pertenece a {$quien}. Solo su dueño puede hacer cierre normal; "
                               . "como administrador debes hacer un Cierre Forzado desde Control de Cajas."
                ], 403);
            }
            return response()->json(['message' => 'No tienes ninguna caja abierta para cerrar.'], 400);
        }

        $caja->update([
            'monto_cierre'   => $request->monto_cierre,
            'fecha_cierre'   => Carbon::now(),
            'estado'         => 'cerrada',
            'tipo_cierre'    => 'normal',
            'cerrado_por_id' => $user->id,
        ]);

        return response()->json(['message' => 'Caja cerrada exitosamente.', 'caja' => $caja]);
    }

    /**
     * Cajas abiertas en el sistema (Control de Cajas).
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
     * Historial completo (Control de Cajas).
     */
    public function historial()
    {
        $historial = Caja::with(['usuario', 'cerradoPor'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($historial);
    }

    /**
     * Cierre FORZADO: solo admin, solo sobre cajas de OTROS usuarios.
     */
    public function forzarCierre(Request $request, $id)
    {
        if (!$this->esAdministrador()) {
            return response()->json(['message' => 'Solo un administrador puede forzar el cierre.'], 403);
        }

        $caja = Caja::find($id);

        if (!$caja) {
            return response()->json(['message' => 'Caja no encontrada.'], 404);
        }

        if ($caja->estado !== 'abierta') {
            return response()->json(['message' => 'Esta caja ya está cerrada.'], 400);
        }

        $admin = $this->usuarioActual();

        if ($caja->user_id === $admin->id) {
            return response()->json([
                'message' => 'No puedes forzar el cierre de tu propia caja. Usa el cierre normal desde Apertura / Cierre.'
            ], 403);
        }

        $caja->update([
            'monto_cierre'   => $request->input('monto_cierre', $caja->monto_apertura + $caja->total_ventas),
            'fecha_cierre'   => Carbon::now(),
            'estado'         => 'cerrada',
            'tipo_cierre'    => 'forzado',
            'observacion'    => $request->input('observacion', 'Cierre forzado por el Administrador'),
            'cerrado_por_id' => $admin->id,
        ]);

        return response()->json([
            'message' => 'Caja cerrada forzosamente con éxito.',
            'caja'    => $caja->load(['usuario', 'cerradoPor'])
        ]);
    }
}
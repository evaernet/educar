<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use App\Models\HorarioDeporte;
use App\Models\InscripcionDeportiva;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HorarioDeporteController extends Controller
{
    public function index(Deporte $deporte) { return view('deportes.horarios', compact('deporte')); }
    public function store(Request $request, Deporte $deporte)
    {
        $data = $request->validate($this->rules());
        $this->validateConflicts($deporte, $data);
        $deporte->horarios()->create($data);
        return back()->with('success', 'Horario deportivo registrado correctamente.');
    }
    public function destroy(Deporte $deporte, HorarioDeporte $horarioDeporte)
    {
        abort_unless($horarioDeporte->deporte_id === $deporte->id, 404);
        $horarioDeporte->delete();
        return back()->with('success', 'Horario deportivo eliminado correctamente.');
    }
    private function rules(): array { return ['dia_semana'=>'required|integer|between:1,6', 'hora_inicio'=>'required|date_format:H:i', 'hora_fin'=>'required|date_format:H:i|after:hora_inicio']; }
    private function validateConflicts(Deporte $deporte, array $data): void
    {
        $alumnos = InscripcionDeportiva::where('deporte_id', $deporte->id)->where('activo', true)->pluck('alumno_id');
        $conflict = HorarioDeporte::where('dia_semana', $data['dia_semana'])->where('hora_inicio', '<', $data['hora_fin'])->where('hora_fin', '>', $data['hora_inicio'])
            ->whereHas('deporte.inscripciones', fn ($query) => $query->whereIn('alumno_id', $alumnos)->where('activo', true)->where('deporte_id', '!=', $deporte->id))->exists();
        if ($conflict) throw ValidationException::withMessages(['hora_inicio' => 'El horario se superpone con otro deporte de un alumno inscripto.']);
    }
}

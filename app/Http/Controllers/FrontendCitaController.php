<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Tramite;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FrontendCitaController extends Controller
{
    public function create()
    {
        $tramites = Tramite::with('area.secretaria')->get();

        return view('citas.create', compact('tramites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tramite_id' => 'required|exists:tramites,id',
            'tipo_documento' => 'required|in:CC,CE,PA,TI,RC',
            'numero_documento' => 'required|string',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'direccion' => 'nullable|string|max:255',
            'fecha_cita' => 'required|date|after_or_equal:today',
            'hora_cita' => 'required|date_format:H:i',
            'acepta_tratamiento_datos' => 'required|accepted',
        ]);

        $cita = new Cita($validated);
        $cita->estado = 'programada';
        $cita->save();

        return redirect()->route('citas.create')->with('success', '¡Cita registrada exitosamente!');
    }
}

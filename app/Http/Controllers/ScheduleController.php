<?php

namespace App\Http\Controllers;

use App\Mail\ScheduleMail;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('schedule.index');
    }

    public function listar()
    {
        $agendamentos = Schedule::all();

        return response()->json($agendamentos);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'init' => 'required|date',
            'end' => 'required|date|after:init',
            'email' => 'required|email',
            'phone' => 'required'
        ]);

        $validated['init'] = Carbon::parse($request->init)->timezone('America/Sao_Paulo');
        $validated['end'] = Carbon::parse($request->end)->timezone('America/Sao_Paulo');

        try {
            $conflict = Schedule::where(function ($query) use ($request) {
                $query->whereBetween('init', [$request->init, $request->end])
                    ->orWhereBetween('end', [$request->init, $request->end]);
            })->exists();


            if ($conflict) {
                return response()->json(['error' => 'Horario indisponivel']);
            }

            $schedule = Schedule::create($validated);

            if ($schedule) {
                try {
                    Mail::send(new ScheduleMail($schedule));
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => 'Erro ao enviar o e-mail.',
                        'details' => $e->getMessage()
                    ], 500);
                }
            }

            return response()->json(['success' => 'Conteudo criado com sucesso.']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar o agendamento.',
                'details' => $e->getMessage()
            ], 500);
        }


        return response()->json($schedule);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->noContent(200);
    }
}

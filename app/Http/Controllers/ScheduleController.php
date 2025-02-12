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
                $query->where('init', [$request->init, $request->end])
                    ->orWhere('end', [$request->inti, $request->end]);
            })->exists();

            if ($conflict) {
                return response()->json(['error' => 'Horario indisponivel']);
            }

            $schedule = Schedule::create($validated);
            if ($schedule) {
                Mail::send(new ScheduleMail($schedule));
            }

            return response()->noContent();
            // return response()->json([
            //     'message' => 'Agendamento criado com sucesso!',
            //     'schedule' => $schedule
            // ], 201);
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

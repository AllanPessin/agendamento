<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $agendamentos = Schedule::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'start' => $item->hour,
            ];
        });

        return response()->json($agendamentos);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required',
            'hour' => 'required|date_format:Y-m-d H:i:s|after:today|before:18:00',
            'address' => 'string|required',
        ]);

        $hour = Carbon::parse($request->hour);
        if ($hour->isWeekend() || $hour->hour < 8 || $hour->hour >= 18) {
            return response()->json(['error' => 'Agendamento apenas em dias uteis']);
        }

        if (!Schedule::scopeAvailable($request->hour)) {
            return response()->json(['error' => 'Horario indisponivel'], 422);
        }

        Schedule::create($request->validated());
        return response()->json(['message' => 'Agendamento realizado com sucesso'], 201);
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
        //
    }
}

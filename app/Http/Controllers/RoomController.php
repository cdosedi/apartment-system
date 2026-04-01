<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::with('activeLeases.tenant')
            ->orderByRoomNumber()
            ->paginate(24);

        return view('rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('rooms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'room_number' => ['required', 'string', 'max:10', 'unique:rooms'],
            'bed_capacity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        Room::create([
            'room_number' => $request->room_number,
            'bed_capacity' => $request->bed_capacity,
            'status' => 'available',
        ]);

        return redirect()->route('rooms.index')
            ->with('success', 'Room added successfully.');
    }

    public function addBed(Room $room): RedirectResponse
    {
        $room->update([
            'bed_capacity' => $room->bed_capacity + 1,
        ]);

        return back()->with('success', "Bed added to Room {$room->room_number}. New capacity: {$room->bed_capacity} beds.");
    }

    public function removeBed(Room $room): RedirectResponse
    {
        if ($room->bed_capacity > 1 && $room->bed_capacity > $room->activeLeases()->count()) {
            $room->update([
                'bed_capacity' => $room->bed_capacity - 1,
            ]);

            return back()->with('success', "Bed removed from Room {$room->room_number}. New capacity: {$room->bed_capacity} beds.");
        }

        return back()->withErrors('Cannot remove bed: room would be over capacity or have less than 1 bed.');
    }
}

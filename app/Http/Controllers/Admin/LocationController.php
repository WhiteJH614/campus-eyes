<?php

// Author: Ivan Goh Shern Rune

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Room;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return view('admin.locations.index', [
            'blocks' => Block::with('rooms')->get(),
            'allBlocks' => Block::all(),
        ]);
    }

    public function storeBlock(Request $request)
    {
        $request->validate([
            'block_name' => 'required'
        ]);

        Block::create([
            'block_name' => $request->block_name
        ]);

        return redirect()->back()->with('success', 'Block added');
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'block_id' => 'required',
            'floor_number' => 'required|integer',
            'room_name' => 'required',
        ]);

        Room::create([
            'block_id' => $request->block_id,
            'floor_number' => $request->floor_number,
            'room_name' => $request->room_name,
        ]);

        return redirect()->back()->with('success', 'Room added');
    }

    public function editRoom($id)
    {
        $room = Room::findOrFail($id);
        $blocks = Block::all();

        return view('admin.locations.edit-room', compact('room', 'blocks'));
    }

    public function updateRoom(Request $request, $id)
    {
        $request->validate([
            'block_id' => 'required',
            'floor_number' => 'required|integer',
            'room_name' => 'required',
        ]);

        $room = Room::findOrFail($id);

        $room->update([
            'block_id' => $request->block_id,
            'floor_number' => $request->floor_number,
            'room_name' => $request->room_name,
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Room updated');
    }

    public function deleteRoom($id)
    {
        $room = Room::findOrFail($id);

        // ✅ 检查是否有报告使用这个房间
        $hasReports = \DB::table('reports')
            ->where('room_id', $id)
            ->exists();

        if ($hasReports) {
            return redirect()->back()
                ->with('error', 'Cannot delete room. There are reports associated with this room.');
        }

        $room->delete();

        return redirect()->back()->with('success', 'Room deleted successfully');
    }


}

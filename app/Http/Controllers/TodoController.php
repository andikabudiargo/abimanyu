<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Models\User;

class TodoController extends Controller
{
    public function index()
    {
    $todos = Todo::with('users')->orderBy('agenda_time')->get();
    $users = User::all();
    return view('todo.index', compact('todos', 'users'));
    }

    public function store(Request $request)
{
    $request->validate([
        'task' => 'required|string|max:255',
        'agenda_time' => 'required|date',
        'user_ids' => 'array'
    ]);

   $todo = Todo::create([
    'task' => $request->task,
    'agenda_time' => $request->agenda_time,
    'done' => false,
    'user_id' => auth()->id(), // simpan pembuat
]);

$todo->users()->sync($request->user_ids); // user yang diajak


    // Tambahkan user terkait, kecuali pembuat
if ($request->has('user_ids')) {
    // Hapus user pembuat dari list jika ada di request
    $userIds = collect($request->user_ids)->reject(fn($id) => $id == auth()->id());
    $todo->users()->sync($userIds);
}

    return redirect()->back()->with('success', 'Agenda berhasil ditambahkan');
}

    public function toggle(Request $request, $id)
{
    $todo = Todo::findOrFail($id);
    $todo->done = !$todo->done;
    $todo->save();

    return response()->json(['done' => $todo->done]);
}

public function destroy($id)
{
    // Cari todo yang user terkait atau pembuat
    $todo = Todo::where('id', $id)
        ->where(function($q){
            $q->whereHas('users', fn($q2) => $q2->where('users.id', auth()->id()))
              ->orWhere('user_id', auth()->id());
        })->first();

    if(!$todo){
        return response()->json([
            'success' => false,
            'message' => 'Agenda tidak ditemukan atau Anda tidak memiliki akses.'
        ]);
    }

    $todo->delete();

    return response()->json([
        'success' => true,
        'message' => 'Agenda berhasil dihapus.'
    ]);
}

public function reschedule(Request $request, $id)
{
    $request->validate([
        'agenda_time' => 'required|date',
    ]);

    // Pastikan pembuat atau user yang diinvite bisa reschedule
    $todo = Todo::where('id', $id)
        ->where(function($q){
            $q->whereHas('users', fn($q2) => $q2->where('users.id', auth()->id()))
              ->orWhere('user_id', auth()->id());
        })->first();

    if(!$todo){
        return response()->json([
            'success' => false,
            'message' => 'Agenda tidak ditemukan atau Anda tidak memiliki akses.'
        ]);
    }

    $todo->agenda_time = $request->agenda_time;
    $todo->save();

    return response()->json([
        'success' => true,
        'message' => 'Agenda berhasil direschedule.'
    ]);
}


}

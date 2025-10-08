<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function create()
    {
        $users = User::all();
        return view('announcements.create', compact('users'));
    }

   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category' => 'required|string|max:100',
        'display_date' => 'required|string', // format: YYYY-MM-DD to YYYY-MM-DD
        'recipients' => 'required|array|min:1',
        'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xlsx,doc,docx|max:5120', // max 5MB
    ]);

    DB::beginTransaction();

    try {
        // Pisahkan range date
        $dates = explode(' to ', $request->display_date);
        if(count($dates) !== 2) {
            throw new \Exception('Invalid display date format.');
        }

        [$start, $end] = $dates;

        // Simpan announcement
        $announcement = Announcement::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'display_start' => $start,
            'display_end' => $end,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        // Simpan recipients
        $announcement->recipients()->sync($request->recipients);

        // Upload multiple attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('public/announcements'); // storage/app/public/announcements
                $announcement->attachments()->create([
                    'path' => $path,
                ]);
            }
        }

        DB::commit();

        // **Kembalikan JSON untuk Ajax**
        return response()->json([
            'success' => true,
            'message' => 'Announcement saved successfully.'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function index()
    {
        $announcements = Announcement::with('attachments', 'recipients')->latest()->get();
        return view('announcements.index', compact('announcements'));
    }

    public function show($id)
    {
        $announcement = Announcement::with('attachments', 'recipients')->findOrFail($id);
        return view('announcements.show', compact('announcement'));
    }
}

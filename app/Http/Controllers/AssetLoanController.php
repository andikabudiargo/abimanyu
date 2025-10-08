<?php

namespace App\Http\Controllers;

use App\Models\ITAsset;
use Illuminate\Http\Request;
use App\Models\AssetLoan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AssetLoanController extends Controller
{
    public function index(Request $request)
{
    $categories = ITAsset::select('asset_type')->distinct()->pluck('asset_type');
    $assets = ITAsset::where('assignment_type', 'Spare')->get();

    $user = auth()->user();
    $isIT = $user->roles()->where('name', 'IT Special Access')->exists();

   if($isIT) {
    // User IT melihat:
    // - Semua pinjaman mereka sendiri (Pending / Approved)
    // - Semua pinjaman Pending dari user lain (untuk approve/reject)
   $loans = AssetLoan::with(['asset', 'user'])
    ->where(function($q) use ($user) {
        // Pinjaman user sendiri (Pending / Approved / Returned)
        $q->where('user_id', $user->id)
          ->whereNotIn('status', ['Rejected'])
                  ->whereNull('condition_return'); // hanya tampil kalau belum dikonfirmasi;
                  
         // Pinjaman dari user lain
        $q->orWhere(function($query) {
            $query->where('status', 'Pending'); // semua pending dari user lain
        })
        ->orWhere(function($query) {
            $query->where('status', 'Approved'); // semua approved dari user lain
        })
        ->orWhere(function($query) {
            $query->where('status', 'Returned')
                  ->whereNull('condition_return'); // hanya returned belum dikonfirmasi
        });
    })
    ->get();


} else {
   // User biasa hanya lihat pinjaman dirinya sendiri, kecuali Rejected/Returned
$loans = AssetLoan::with(['asset', 'user'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['Rejected', 'Returned'])
            ->get();

}



    return view('facility.asset-loan', compact('assets', 'user', 'categories', 'loans', 'isIT'));
}


    // Simpan peminjaman baru (JSON)
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'asset_id' => 'required|exists:it_assets,id',
        'purpose' => 'required',
        'date_loan' => 'required|date',
        'return_estimation' => 'required|date|after_or_equal:date_loan',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $loan = AssetLoan::create([
        'asset_id' => $request->asset_id,
        'user_id' => auth()->id(),  // otomatis ambil user yang login
        'purpose' => $request->purpose,
        'date_loan' => $request->date_loan,
        'return_estimation' => $request->return_estimation,
        'status' => 'Pending',
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Asset loan created successfully.',
        'data' => $loan
    ]);
}

public function approve(Request $request)
{
    $request->validate([
        'loan_id' => 'required|exists:assets_loans,id',
    ]);

    $loan = AssetLoan::findOrFail($request->loan_id);

    if ($loan->status !== 'Pending') {
        return response()->json([
            'status' => 'error',
            'message' => 'Loan has already been processed.'
        ], 400);
    }

    // Update status loan
    $loan->update([
        'status' => 'Approved',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    // Update status asset (misal jadi "Loaned")
    $asset = $loan->asset;
    $asset->update([
        'status' => 'Loaned',
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Loan approved successfully.',
    ]);
}

public function reject(Request $request)
{
    $request->validate([
        'loan_id' => 'required|exists:assets_loans,id',
        'reason'  => 'required|string',
    ]);

    $loan = AssetLoan::with('asset')->findOrFail($request->loan_id);

    // Update loan status
    $loan->update([
        'status' => 'Rejected',
        'rejected_by' => auth()->id(),
        'rejected_at' => now(),
        'rejected_reason' => $request->reason
    ]);

    // Update asset status kembali menjadi Available
    if($loan->asset) {
        $loan->asset->update([
            'status' => 'Available'
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Loan rejected successfully and asset is now available'
    ]);
}

public function returnLoan(Request $request)
{
    $request->validate([
        'loan_id' => 'required|exists:assets_loans,id',
    ]);

    $loan = AssetLoan::with('asset')->findOrFail($request->loan_id);

    // Pastikan user yang meminjam yang melakukan return
    if($loan->user_id !== auth()->id()) {
        return response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to return this loan.'
        ], 403);
    }

    // Update loan status
    $loan->update([
        'status' => 'Returned',
        'date_return' => now(),
    ]);

    // Update asset status kembali menjadi Available
    if($loan->asset) {
        $loan->asset->update([
            'status' => 'Available'
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Asset has been successfully returned.'
    ]);
}

public function cancel(AssetLoan $loan)
{
    // Hanya peminjam yang bisa cancel
    if ($loan->user_id !== auth()->id()) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }

    if ($loan->status !== 'Pending') {
        return response()->json(['status' => 'error', 'message' => 'Cannot cancel processed loan'], 400);
    }

    // Hapus data loan
    $loan->delete();

    // Update status asset kembali ke Available
    if ($loan->asset) {
        $loan->asset->update(['status' => 'Available']);
    }

    return response()->json(['status' => 'success', 'message' => 'Loan canceled successfully']);
}


public function confirmCondition(Request $request)
{
    $request->validate([
        'loan_id' => 'required|exists:assets_loans,id',
        'condition' => 'required|string',
        'notes' => 'nullable|string',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $loan = AssetLoan::findOrFail($request->loan_id);
    $loan->condition_return = $request->condition;
    $loan->condition_note = $request->notes;

    // Simpan foto hanya jika ada upload
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $originalName = $file->getClientOriginalName(); // ambil nama asli file
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        // Hindari tabrakan nama file
        $safeFilename = Str::slug($filename) . '-' . time() . '.' . $extension;

        $path = $file->storeAs('uploads/asset-conditions', $safeFilename, 'public');
        $loan->photo_after_return = 'storage/' . $path;
    }

    $loan->save();

      // 🔹 Update status aset jika rusak
    if ($request->condition === 'Broken' && $loan->asset_id) {
        $asset = ITAsset::find($loan->asset_id);
        if ($asset) {
            $asset->conditions = 'Broken'; // ubah sesuai field status di tabel kamu
            $asset->save();
        }
    }

    return response()->json(['message' => 'Condition after return successfully confirmed.']);
}




}
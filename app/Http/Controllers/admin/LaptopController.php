<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laptop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaptopController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->input('q', '');
        
        $query = Laptop::query();
        
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('device_name', 'like', "%{$q}%")
                   ->orWhere('status', 'like', "%{$q}%")
                   ->orWhere('notes', 'like', "%{$q}%");
            });
        }
        
        $laptops = $query->orderByDesc('id')->paginate(12)->withQueryString();
        return view('admin.laptop', compact('laptops', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'device_name' => ['required', 'string', 'max:120'],
            'status'      => ['required', Rule::in(['available','reserved','out','maintenance'])],
            'notes'       => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('laptops', 'public'); // storage/app/public/laptops/...
            \Log::info('Image uploaded successfully', ['path' => $path]);
        } else {
            \Log::info('No image file uploaded');
        }

        $laptop = Laptop::create([
            'device_name' => $data['device_name'],
            'status'      => $data['status'],
            'notes'       => $data['notes'] ?? null,
            'image_path'  => $path,
        ]);

        \Log::info('Laptop created', ['id' => $laptop->id, 'image_path' => $laptop->image_path]);

        return redirect()->route('admin.laptop')->with('success', 'Laptop added successfully.');
    }

    public function update(Request $request, Laptop $laptop)
    {
        $data = $request->validate([
            'device_name'  => ['required', 'string', 'max:120'],
            'status'       => ['required', Rule::in(['available','reserved','out','maintenance'])],
            'notes'        => ['nullable', 'string', 'max:1000'],
            'image'        => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        try {
            // Handle image removal
            if ($request->boolean('remove_image') && $laptop->image_path) {
                if (Storage::disk('public')->exists($laptop->image_path)) {
                    Storage::disk('public')->delete($laptop->image_path);
                }
                $laptop->image_path = null;
            }

            // Handle new image upload
            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($laptop->image_path && Storage::disk('public')->exists($laptop->image_path)) {
                    Storage::disk('public')->delete($laptop->image_path);
                }
                $laptop->image_path = $request->file('image')->store('laptops', 'public');
            }

            $laptop->device_name = $data['device_name'];
            $laptop->status      = $data['status'];
            $laptop->notes       = $data['notes'] ?? null;
            $laptop->save();

            return redirect()->route('admin.laptop')->with('success', 'Laptop updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.laptop')->with('error', 'Failed to update laptop: ' . $e->getMessage());
        }
    }

    public function destroy(Laptop $laptop)
    {
        try {
            // Check if laptop is currently checked out or has pending requests
            $activeBorrowings = $laptop->borrowings()->where('status', '!=', 'returned')->count();
            
            if ($activeBorrowings > 0) {
                return redirect()->route('admin.laptop')->with('error', 
                    "Cannot delete laptop '{$laptop->device_name}' because it is currently checked out or has pending requests. " .
                    "Please check in the laptop and resolve all pending requests before deleting."
                );
            }

            // Check if laptop has any completed borrowings
            $completedBorrowingsCount = $laptop->borrowings()->where('status', 'returned')->count();
            
            if ($completedBorrowingsCount > 0) {
                // Delete all completed borrowing records first
                $laptop->borrowings()->where('status', 'returned')->delete();
            }

            // Delete the image file if it exists
            if ($laptop->image_path && Storage::disk('public')->exists($laptop->image_path)) {
                Storage::disk('public')->delete($laptop->image_path);
            }

            $laptop->delete();

            $message = 'Laptop deleted successfully.';
            if ($completedBorrowingsCount > 0) {
                $message .= " Also deleted {$completedBorrowingsCount} completed borrowing record(s).";
            }

            return redirect()->route('admin.laptop')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.laptop')->with('error', 'Failed to delete laptop: ' . $e->getMessage());
        }
    }
}

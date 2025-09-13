<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpAsset;
use Illuminate\Http\Request;

class IpAssetController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->input('status', 'all');
        $q      = (string) $request->input('q', '');

        $query = IpAsset::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        $ipAssets = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.ip', compact('ipAssets', 'status', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required','string','max:120'],
            'ip_address' => ['required','string','max:45','unique:ip_assets,ip_address'],
            'status'     => ['required','in:free,assigned,blocked'],
            'notes'      => ['nullable','string','max:255'],
            'latitude'   => ['nullable','numeric','between:-90,90'],
            'longitude'  => ['nullable','numeric','between:-180,180'],
            'address'    => ['nullable','string','max:255'],
        ]);

        IpAsset::create($data);

        return back()->with('success', 'IP created.');
    }

    public function update(Request $request, IpAsset $ip_asset)
    {
        $data = $request->validate([
            'name'       => ['required','string','max:120'],
            'ip_address' => ['required','string','max:45',"unique:ip_assets,ip_address,{$ip_asset->id}"],
            'status'     => ['required','in:free,assigned,blocked'],
            'notes'      => ['nullable','string','max:255'],
            'latitude'   => ['nullable','numeric','between:-90,90'],
            'longitude'  => ['nullable','numeric','between:-180,180'],
            'address'    => ['nullable','string','max:255'],
        ]);

        $ip_asset->update($data);

        return back()->with('success', 'IP updated.');
    }

    public function destroy(IpAsset $ip_asset)
    {
        $ip_asset->delete();
        return back()->with('success', 'IP deleted.');
    }
}

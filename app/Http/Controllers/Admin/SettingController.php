<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private SettingService $settings)
    {
    }

    public function edit(): View
    {
        $this->authorize('view', \App\Models\Setting::class);

        return view('admin.settings.edit', [
            'settings' => Setting::pluck('value', 'name'),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated(), $request->file('logo'), $request->user()->id);

        return redirect()->route('admin.settings.edit')->with('status', 'Identitas madrasah berhasil diperbarui.');
    }
}

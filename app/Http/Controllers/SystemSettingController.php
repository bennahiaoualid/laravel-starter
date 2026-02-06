<?php

namespace App\Http\Controllers;

use App\Contracts\FlasherInterface;
use App\Services\System\SystemSettingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    public function __construct(
        private SystemSettingService $systemSettingService,
        private FlasherInterface $flasher
    ) {
        //
    }

    public function companyInfo(): View
    {
        $allSettings = $this->systemSettingService->getAllSettings();
        $companyInfoSettings = $this->systemSettingService->filterSettingsByCategory($allSettings, 'company_info');
        $invoiceSettings = $this->systemSettingService->filterSettingsByCategory($allSettings, 'invoice');
        $data = [
            'company_info' => $companyInfoSettings,
            'invoice' => $invoiceSettings,
        ];

        return view('pages.settings.index', compact('data'));
    }

    /**
     * Update a system setting
     */
    public function update(Request $request, string $category, string $key)
    {
        // Handle logo upload separately
        if ($key === 'company_logo' && $request->hasFile('logo')) {
            return $this->uploadLogo($request);
        }
        $defaultValidation = config("settings.validation.{$key}");
        $attributes = __("settings.{$category}.{$key}.name"); // friendly name

        if ($defaultValidation !== null) {
            // Ensure the 'attributes' key is provided
            $request->validateWithBag(
                $key,
                $defaultValidation['rules'],
                [], // custom messages, optional
                $defaultValidation['attributes'] ?? ['value' => $attributes]
            );
        } else {
            $request->validateWithBag(
                $key,
                [
                    'value' => 'required|string|max:255',
                ],
                [], // custom messages
                ['value' => $attributes] // set friendly field name
            );
        }

        $value = $request->input('value');

        if (! $this->systemSettingService->setValue($key, $value)) {
            $this->flasher->error(__('messages.validation.fail.updated'));

            return back()->withErrors([$key => $this->systemSettingService->getValidationMessage($key)]);
        }
        $this->flasher->crudSuccess('updated');

        return redirect()->back();
    }

    /**
     * Upload company logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $file = $request->file('logo');

        if (! $this->systemSettingService->uploadLogo($file)) {
            $this->flasher->error(__('messages.validation.fail.updated'));

            return back()->withErrors(['logo' => 'Failed to upload logo']);
        }

        $this->flasher->crudSuccess('updated');

        return redirect()->back();
    }

    /**
     * Delete company logo
     */
    public function deleteLogo()
    {
        if (! $this->systemSettingService->deleteLogo()) {
            $this->flasher->error('Failed to delete logo');

            return back();
        }

        $this->flasher->crudSuccess('deleted');

        return redirect()->back();
    }

    /**
     * Refresh system settings cache
     */
    public function refreshCache()
    {
        $this->systemSettingService->refreshCache();

        return back()->with('success', 'Settings cache refreshed successfully');
    }
}

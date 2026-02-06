<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Company Info
            [
                'setting_key' => 'company_name',
                'setting_value' => '',
                'setting_trans_key' => 'settings.company_info.company_name',
            ],
            [
                'setting_key' => 'address',
                'setting_value' => '',
                'setting_trans_key' => 'settings.company_info.address',
            ],
            [
                'setting_key' => 'field',
                'setting_value' => '',
                'setting_trans_key' => 'settings.company_info.field',
            ],
            // Invoice Settings
            [
                'setting_key' => 'invoice_default_language',
                'setting_value' => 'ar',
                'setting_trans_key' => 'settings.invoice.invoice_default_language',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                [
                    'setting_value' => $setting['setting_value'],
                    'setting_trans_key' => $setting['setting_trans_key'],
                ]
            );
        }

        $this->command->info('System settings seeded successfully!');
    }
}

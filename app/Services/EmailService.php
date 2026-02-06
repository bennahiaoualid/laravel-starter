<?php

namespace App\Services;

use App\Mail\GenericEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function __construct() {}

    /**
     * Send miss calculation notification email to owner
     *
     * @param  array  $data  Array of invoice data: [
     *                       ['invoice_num' => '123', 'total' => [100, 150] or null, 'purchases_total' => [50, 75] or null],
     *                       ...
     *                       ]
     */
    public function missCalculation(array $data): bool
    {
        try {
            $owner = User::role('owner')->first();

            if (! $owner || ! $owner->email) {
                Log::error('EmailService: Owner user not found or has no email');

                return false;
            }

            // Prepare email data
            $emailData = [
                'invoices' => $data,
            ];

            // Send email
            Mail::to($owner->email)
                ->queue(new GenericEmail(
                    emailView: 'emails.miss-calculation',
                    data: $emailData,
                    emailSubject: __('emails.miss_calculation.subject')
                ));

            return true;
        } catch (\Exception $e) {
            Log::error('EmailService::missCalculation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send party balance mismatch notification email to owner
     *
     * @param  array  $data  Array of party balance data: [
     *                       ['partie_name' => 'Company Name', 'old_balance' => '100.00', 'correct_balance' => '150.00'],
     *                       ...
     *                       ]
     */
    public function partyBalanceMismatch(array $data): bool
    {
        try {
            $owner = User::role('owner')->first();

            if (! $owner || ! $owner->email) {
                Log::error('EmailService: Owner user not found or has no email');

                return false;
            }

            // Prepare email data
            $emailData = [
                'parties' => $data,
            ];

            // Send email
            Mail::to($owner->email)
                ->queue(new GenericEmail(
                    emailView: 'emails.party-balance-mismatch',
                    data: $emailData,
                    emailSubject: __('emails.party_balance_mismatch.subject')
                ));

            return true;
        } catch (\Exception $e) {
            Log::error('EmailService::partyBalanceMismatch failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send investor balance mismatch notification email to owner
     *
     * @param  array  $data  Array of investor balance data: [
     *                       ['investor_name' => 'Investor Name', 'old_balance' => '100.00', 'correct_balance' => '150.00'],
     *                       ...
     *                       ]
     */
    public function investorBalanceMismatch(array $data): bool
    {
        try {
            $owner = User::role('owner')->first();

            if (! $owner || ! $owner->email) {
                Log::error('EmailService: Owner user not found or has no email');

                return false;
            }

            // Prepare email data
            $emailData = [
                'investors' => $data,
            ];

            // Send email
            Mail::to($owner->email)
                ->queue(new GenericEmail(
                    emailView: 'emails.investor-balance-mismatch',
                    data: $emailData,
                    emailSubject: __('emails.investor_balance_mismatch.subject')
                ));

            return true;
        } catch (\Exception $e) {
            Log::error('EmailService::investorBalanceMismatch failed: '.$e->getMessage());

            return false;
        }
    }
}

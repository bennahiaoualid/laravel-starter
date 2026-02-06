<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.party_balance_mismatch.title') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            direction: {{ app()->isLocale('ar') ? 'rtl' : 'ltr' }};
        }
        .header {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .list {
            list-style-type: none;
            padding: 0;
        }
        .party-item {
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .party-name {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .balance-detail {
            margin: 8px 0;
            padding: 8px;
            background-color: #fff;
            {{ app()->isLocale('ar') ? 'border-right' : 'border-left' }}: 3px solid #e74c3c;
            border-radius: 3px;
        }
        .balance-label {
            font-weight: bold;
            color: #555;
        }
        .balance-value {
            color: #e74c3c;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('emails.party_balance_mismatch.title') }}</h1>
    </div>

    <div class="content">
        <p>{{ __('emails.party_balance_mismatch.intro') }}</p>

        @if(!empty($parties))
        <div class="section">
            <div class="section-title">{{ __('emails.party_balance_mismatch.parties') }}:</div>
            @foreach($parties as $party)
                <div class="party-item">
                    <div class="party-name">{{ __('emails.party_balance_mismatch.party_name') }}: {{ $party['partie_name'] }}</div>
                    
                    <div class="balance-detail">
                        <span class="balance-label">{{ __('emails.party_balance_mismatch.old_balance') }}: </span>
                        <span class="balance-value">{{ $party['old_balance'] }}</span>
                    </div>

                    <div class="balance-detail">
                        <span class="balance-label">{{ __('emails.party_balance_mismatch.correct_balance') }}: </span>
                        <span class="balance-value">{{ $party['correct_balance'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="footer">
            <p>{{ __('emails.party_balance_mismatch.footer') }}</p>
        </div>
    </div>
</body>
</html>


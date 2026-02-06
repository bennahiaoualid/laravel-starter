<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.miss_calculation.title') }}</title>
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
        .invoice-item {
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .invoice-number {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .calculation-detail {
            margin: 8px 0;
            padding: 8px;
            background-color: #fff;
            {{ app()->isLocale('ar') ? 'border-right' : 'border-left' }}: 3px solid #e74c3c;
            border-radius: 3px;
        }
        .calculation-label {
            font-weight: bold;
            color: #555;
        }
        .calculation-value {
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
        <h1>{{ __('emails.miss_calculation.title') }}</h1>
    </div>

    <div class="content">
        <p>{{ __('emails.miss_calculation.intro') }}</p>

        @if(!empty($invoices))
        <div class="section">
            <div class="section-title">{{ __('emails.miss_calculation.invoices') }}:</div>
            @foreach($invoices as $invoice)
                <div class="invoice-item">
                    <div class="invoice-number">{{ __('emails.miss_calculation.invoice_number') }}: {{ $invoice['invoice_num'] }}</div>
                    
                    @if(!empty($invoice['total']))
                    <div class="calculation-detail">
                        <span class="calculation-label">{{ __('emails.miss_calculation.total') }}: </span>
                        <span class="calculation-value">
                            {{ __('emails.miss_calculation.old_value') }}: {{ $invoice['total'][0] }} → 
                            {{ __('emails.miss_calculation.incorrect_value') }}: {{ $invoice['total'][1] }}
                        </span>
                    </div>
                    @endif

                    @if(!empty($invoice['purchases_total']))
                    <div class="calculation-detail">
                        <span class="calculation-label">{{ __('emails.miss_calculation.purchases_total') }}: </span>
                        <span class="calculation-value">
                            {{ __('emails.miss_calculation.old_value') }}: {{ $invoice['purchases_total'][0] }} → 
                            {{ __('emails.miss_calculation.incorrect_value') }}: {{ $invoice['purchases_total'][1] }}
                        </span>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <div class="footer">
            <p>{{ __('emails.miss_calculation.footer') }}</p>
        </div>
    </div>
</body>
</html>


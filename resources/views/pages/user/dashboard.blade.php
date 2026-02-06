@extends('layouts.user.master')
@section('css')

    @section('title')
        {{ __('dashboard.title') }}
    @stop
@endsection

@section('page_title')
    {{ __('dashboard.title') }}
@endsection

@section('content')
    @php
        $salesPurchases = $dashboardStats['sales_purchases'] ?? [];
        $lastInvoices = $dashboardStats['last_invoices'] ?? collect();
        $dailySalesBenefits = $dashboardStats['daily_sales_benefits'] ?? [];
        $moneyTransactions = $dashboardStats['money_transactions'] ?? [];
        $weeklyData = $dashboardStats['weekly_data'] ?? [];
    @endphp

    <!-- Sales and Purchases Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-green-500 rounded-lg shadow-lg flex flex-col">
            <div class="p-4 space-y-2 relative">
                <div class="text-white text-3xl">{{ number_format($salesPurchases['sales']['total'] ?? 0, 2) }} DA</div>
                <div class="text-white capitalize">{{ __('dashboard.sales.title') }}</div>
                <div class="text-white text-sm mt-2">
                    {{ __('dashboard.sales.invoices') }}: {{ number_format($salesPurchases['sales']['count'] ?? 0) }}
                </div>
                @if(($salesPurchases['sales']['total_purchases'] ?? 0) > 0)
                <div class="text-white text-xs mt-1 opacity-90">
                    {{ __('dashboard.sales.based_on_purchases') }}: {{ number_format($salesPurchases['sales']['total_purchases'] ?? 0, 2) }} DA
                </div>
                @endif
                <div class="text-green-600 text-5xl absolute top-1/2 end-2 -translate-y-1/2 opacity-20">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-yellow-500 rounded-lg shadow-lg flex flex-col">
            <div class="p-4 space-y-2 relative">
                <div class="text-white text-3xl">{{ number_format($salesPurchases['purchases']['total'] ?? 0, 2) }} DA</div>
                <div class="text-white capitalize">{{ __('dashboard.purchases.title') }}</div>
                <div class="text-white text-sm mt-2">
                    {{ __('dashboard.purchases.orders') }}: {{ number_format($salesPurchases['purchases']['count'] ?? 0) }}
                </div>
                <div class="text-yellow-600 text-5xl absolute top-1/2 end-2 -translate-y-1/2 opacity-20">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Last 3 Invoices Section -->
    <x-ui_widgets.collapsible-card 
        title="{{ __('dashboard.last_invoices.title') }}" 
        isopen="true"
        headerClass="bg-blue-600"
        contentClass="bg-white"
    >
        <div class="space-y-2">
            @if($lastInvoices->count() > 0)
                @foreach($lastInvoices as $invoice)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">#{{ $invoice->number }}</div>
                            <div class="text-sm text-gray-600 mt-1">
                                {{ $invoice->client_name }} → {{ $invoice->supplier_name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('Y-m-d') : $invoice->created_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-lg text-gray-800">{{ number_format($invoice->total, 2) }} DA</div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 text-center py-4">{{ __('dashboard.last_invoices.no_invoices') }}</p>
            @endif
        </div>
    </x-ui_widgets.collapsible-card>

    <!-- Daily Sales and Benefits Section -->
    <x-ui_widgets.collapsible-card 
        title="{{ __('dashboard.daily_sales_benefits.title') }}" 
        isopen="true"
        headerClass="bg-green-600"
        contentClass="bg-white"
        class="max-w-full"
    >
        <div class="relative h-64 w-[90%] mx-auto">
            <canvas id="dailySalesBenefitsChart"></canvas>
        </div>
    </x-ui_widgets.collapsible-card>
    <!-- Money Transactions Section -->
    <x-ui_widgets.collapsible-card 
        title="{{ __('dashboard.money_transactions.title') }}" 
        isopen="true"
        headerClass="bg-purple-600"
        contentClass="bg-white"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Money In -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('dashboard.money_transactions.in.title') }}</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('dashboard.money_transactions.in.debt') }}:</span>
                        <span class="font-semibold text-orange-600">{{ number_format($moneyTransactions['in']['debt'] ?? 0, 2) }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('dashboard.money_transactions.in.not_debt') }}:</span>
                        <span class="font-semibold text-green-600">{{ number_format($moneyTransactions['in']['not_debt'] ?? 0, 2) }} DA</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                        <span class="font-semibold text-gray-800">{{ __('dashboard.money_transactions.in.total') }}:</span>
                        <span class="font-bold text-lg text-gray-800">{{ number_format($moneyTransactions['in']['total'] ?? 0, 2) }} DA</span>
                    </div>
                </div>
            </div>

            <!-- Money Out -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-800 mb-3">{{ __('dashboard.money_transactions.out.title') }}</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('dashboard.money_transactions.out.debt') }}:</span>
                        <span class="font-semibold text-orange-600">{{ number_format($moneyTransactions['out']['debt'] ?? 0, 2) }} DA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('dashboard.money_transactions.out.not_debt') }}:</span>
                        <span class="font-semibold text-red-600">{{ number_format($moneyTransactions['out']['not_debt'] ?? 0, 2) }} DA</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                        <span class="font-semibold text-gray-800">{{ __('dashboard.money_transactions.out.total') }}:</span>
                        <span class="font-bold text-lg text-gray-800">{{ number_format($moneyTransactions['out']['total'] ?? 0, 2) }} DA</span>
                    </div>
                </div>
            </div>
        </div>
    </x-ui_widgets.collapsible-card>

    <!-- Weekly Data Section -->
    <x-ui_widgets.collapsible-card 
        title="{{ __('dashboard.weekly_data.title') }}" 
        isopen="true"
        headerClass="bg-indigo-600"
        contentClass="bg-white"
        class="max-w-full"
    >
        <div class="relative h-80 w-[90%] mx-auto">
            <canvas id="weeklySalesBenefitsChart"></canvas>
        </div>
    </x-ui_widgets.collapsible-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store chart instances for resizing
            let dailyChart = null;
            let weeklyChart = null;

            // Function to resize all charts
            function resizeCharts() {
                if (dailyChart) {
                    dailyChart.resize();
                }
                if (weeklyChart) {
                    weeklyChart.resize();
                }
            }

            // Listen for sidebar toggle events
            const sidebar = document.getElementById('sidebar');
            const toggleSidebarDesktopBtn = document.getElementById('toggleSidebarDesktopBtn');
            
            if (sidebar) {
                // Method 1: Listen to button click
                if (toggleSidebarDesktopBtn) {
                    toggleSidebarDesktopBtn.addEventListener('click', function() {
                        // Wait for sidebar animation to complete (300ms based on CSS transition)
                        setTimeout(resizeCharts, 350);
                    });
                }

                // Method 2: Watch for class changes on sidebar (more reliable)
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            // Wait for CSS transition to complete
                            setTimeout(resizeCharts, 350);
                        }
                    });
                });

                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            // Also listen for window resize events
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(resizeCharts, 250);
            });

            // Daily Sales and Benefits Chart
            const dailyCtx = document.getElementById('dailySalesBenefitsChart');
            if (dailyCtx) {
                const dailyData = @json($dailySalesBenefits);
                dailyChart = new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: dailyData.map(item => {
                            const date = new Date(item.date);
                            return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                        }),
                        datasets: [
                            {
                                label: '{{ __('dashboard.daily_sales_benefits.sales') }}',
                                data: dailyData.map(item => item.sales),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: '{{ __('dashboard.daily_sales_benefits.benefits') }}',
                                data: dailyData.map(item => item.benefits),
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + 
                                            new Intl.NumberFormat('fr-FR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }).format(context.parsed.y) + ' DA';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('fr-FR', {
                                            notation: 'compact',
                                            maximumFractionDigits: 1
                                        }).format(value) + ' DA';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Weekly Sales and Benefits Chart
            const weeklyCtx = document.getElementById('weeklySalesBenefitsChart');
            if (weeklyCtx) {
                const weeklyData = @json($weeklyData);
                weeklyChart = new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: (weeklyData.labels || []).map(date => {
                            const d = new Date(date);
                            return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                        }),
                        datasets: [
                            {
                                label: '{{ __('dashboard.weekly_data.sales') }}',
                                data: weeklyData.sales || [],
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1
                            },
                            {
                                label: '{{ __('dashboard.weekly_data.benefits') }}',
                                data: weeklyData.benefits || [],
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + 
                                            new Intl.NumberFormat('fr-FR', {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }).format(context.parsed.y) + ' DA';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('fr-FR', {
                                            notation: 'compact',
                                            maximumFractionDigits: 1
                                        }).format(value) + ' DA';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
@endsection

# Laravel 12 Task Scheduling Guide

## Overview

Laravel 12 provides a powerful task scheduling system that allows you to define scheduled tasks directly in your codebase using the `Schedule` facade. This eliminates the need for multiple server-side cron jobs.

## How It Works

### 1. Setup - Configure the Cron Job

To enable Laravel's scheduler, you need to add a single cron entry to your server's crontab that runs every minute:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**For Windows (using Task Scheduler):**
- Create a scheduled task that runs every minute
- Command: `php artisan schedule:run`
- Working directory: Your project path

**For Shared Hosting (cPanel):**
- Navigate to "Cron Jobs" in cPanel
- Set time interval to every minute (`* * * * *`)
- Command: `/usr/bin/php /home/username/your-project/artisan schedule:run >> /dev/null 2>&1`

### 2. Define Scheduled Tasks

In Laravel 12, scheduled tasks are defined in the `routes/console.php` file using the `Schedule` facade.

## Current Project Setup

Your project currently has:
- **Laravel 12** (`laravel/framework: ^12.0`)
- **Console routes file**: `routes/console.php` (currently only has the `inspire` command)
- **Bootstrap configuration**: `bootstrap/app.php` properly configured with console routes

## Examples for Your Project

Based on your existing jobs and structure, here are practical examples:

### Example 1: Schedule a Report Generation Job

```php
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateReportJob;
use App\Models\ReportJob;

Schedule::call(function () {
    // Get pending report jobs and dispatch them
    $pendingReports = ReportJob::where('status', 'pending')
        ->where('scheduled_at', '<=', now())
        ->get();
    
    foreach ($pendingReports as $reportJob) {
        GenerateReportJob::dispatch($reportJob);
    }
})->everyFiveMinutes();
```

### Example 2: Schedule PDF Cleanup

```php
use Illuminate\Support\Facades\Schedule;
use App\Helpers\PdfCleanupHelper;

Schedule::call(function () {
    PdfCleanupHelper::cleanupOldPdfs();
})->dailyAt('02:00');
```

### Example 3: Schedule Invoice Recalculation

```php
use Illuminate\Support\Facades\Schedule;
use App\Jobs\RecalculateInvoiceTotals;

Schedule::command('invoice:recalculate')
    ->hourly()
    ->withoutOverlapping();
```

### Example 4: Schedule Queue Processing Check

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping();
```

## Common Scheduling Methods

### Time-based Scheduling

```php
// Every minute
Schedule::call(fn() => /* ... */)->everyMinute();

// Every 5 minutes
Schedule::call(fn() => /* ... */)->everyFiveMinutes();

// Every 10 minutes
Schedule::call(fn() => /* ... */)->everyTenMinutes();

// Every 15 minutes
Schedule::call(fn() => /* ... */)->everyFifteenMinutes();

// Every 30 minutes
Schedule::call(fn() => /* ... */)->everyThirtyMinutes();

// Hourly
Schedule::call(fn() => /* ... */)->hourly();

// Daily at specific time
Schedule::call(fn() => /* ... */)->dailyAt('13:00');

// Weekly on specific day and time
Schedule::call(fn() => /* ... */)->weeklyOn(1, '08:00'); // Monday at 8 AM

// Monthly
Schedule::call(fn() => /* ... */)->monthly();

// Yearly
Schedule::call(fn() => /* ... */)->yearly();
```

### Advanced Scheduling Options

```php
// Prevent overlapping (useful for long-running tasks)
Schedule::call(fn() => /* ... */)
    ->hourly()
    ->withoutOverlapping();

// Run on one server only (for multi-server environments)
Schedule::call(fn() => /* ... */)
    ->daily()
    ->onOneServer();

// Set timezone
Schedule::call(fn() => /* ... */)
    ->dailyAt('09:00')
    ->timezone('America/New_York');

// Run only in specific environments
Schedule::call(fn() => /* ... */)
    ->daily()
    ->environments(['production']);

// Run only on weekdays
Schedule::call(fn() => /* ... */)
    ->weekdays()
    ->at('09:00');

// Run only on weekends
Schedule::call(fn() => /* ... */)
    ->weekends()
    ->at('10:00');

// Run between specific hours
Schedule::call(fn() => /* ... */)
    ->hourly()
    ->between('8:00', '17:00');

// Run unless between specific hours
Schedule::call(fn() => /* ... */)
    ->hourly()
    ->unlessBetween('23:00', '6:00');
```

## Scheduling Artisan Commands

```php
// Schedule an Artisan command
Schedule::command('cache:clear')
    ->daily();

// Schedule with parameters
Schedule::command('invoice:generate --type=monthly')
    ->monthly();

// Schedule a queued command
Schedule::command('reports:generate')
    ->daily()
    ->onQueue('reports');
```

## Scheduling Jobs

```php
use App\Jobs\GenerateReportJob;

// Dispatch a job
Schedule::call(function () {
    GenerateReportJob::dispatch($reportJob);
})->daily();

// Or use the job method (Laravel 11+)
Schedule::job(new GenerateReportJob($reportJob))
    ->daily();
```

## Scheduling Shell Commands

```php
// Execute shell commands
Schedule::exec('node /path/to/script.js')
    ->hourly();

// Execute with output
Schedule::exec('php artisan backup:run')
    ->daily()
    ->sendOutputTo('/path/to/logs/backup.log');
```

## Testing Scheduled Tasks

### List all scheduled tasks
```bash
php artisan schedule:list
```

### Run the scheduler manually (for testing)
```bash
php artisan schedule:run
```

### Test a specific task
```bash
php artisan schedule:test
```

## Best Practices

1. **Use `withoutOverlapping()`** for tasks that might take longer than the interval
2. **Use `onOneServer()`** in multi-server environments to prevent duplicate executions
3. **Set appropriate timezones** using `timezone()` method
4. **Log outputs** for debugging using `sendOutputTo()` or `emailOutputTo()`
5. **Use environment checks** to prevent running tasks in wrong environments
6. **Group related tasks** in the same file for better organization

## Example: Complete routes/console.php

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateReportJob;
use App\Models\ReportJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule report generation every 5 minutes
Schedule::call(function () {
    $pendingReports = ReportJob::where('status', 'pending')
        ->where('scheduled_at', '<=', now())
        ->get();
    
    foreach ($pendingReports as $reportJob) {
        GenerateReportJob::dispatch($reportJob);
    }
})->everyFiveMinutes()
  ->withoutOverlapping()
  ->name('process-pending-reports');

// Daily cleanup at 2 AM
Schedule::call(function () {
    // Add your cleanup logic here
})->dailyAt('02:00')
  ->name('daily-cleanup')
  ->onOneServer();

// Hourly invoice recalculation
Schedule::command('invoice:recalculate')
    ->hourly()
    ->withoutOverlapping()
    ->name('recalculate-invoices');
```

## Monitoring Scheduled Tasks

Laravel provides several ways to monitor scheduled tasks:

1. **Check task execution**: Use `schedule:list` to see all scheduled tasks
2. **Log outputs**: Use `sendOutputTo()` to log task outputs
3. **Email notifications**: Use `emailOutputTo()` to get email notifications
4. **Telescope**: Your project has Telescope configured, which can track scheduled task executions

## Troubleshooting

1. **Tasks not running**: Ensure the cron job is properly configured
2. **Overlapping tasks**: Use `withoutOverlapping()` to prevent conflicts
3. **Timezone issues**: Use `timezone()` method to set correct timezone
4. **Permission issues**: Ensure the cron user has proper permissions

## References

- [Laravel 12 Scheduling Documentation](https://readouble.com/laravel/12.x/en/scheduling.html)
- [Laravel Official Documentation](https://laravel.com/docs/12.x/scheduling)


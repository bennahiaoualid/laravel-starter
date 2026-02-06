<?php

namespace App\Providers;

use App\Contracts\FlasherInterface;
use App\Contracts\ReportRepositoryInterface;
use App\Contracts\TransactionManagerInterface;
use App\Interface\Dashboard\DashboardRepositoryInterface;
use App\Models\Bank;
use App\Models\Document\Box;
use App\Models\Document\Document;
use App\Models\Document\FileType;
use App\Models\Field;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceCost;
use App\Models\Invoice\InvoiceDetail;
use App\Models\Invoice\InvoiceInvestor;
use App\Models\Invoice\InvoiceTemplate;
use App\Models\Person\Investor;
use App\Models\Person\Partie;
use App\Models\Product\Product;
use App\Models\PurchaseOrder\PurchaseOrder;
use App\Models\PurchaseOrder\PurchaseOrderDetail;
use App\Models\ReportJob;
use App\Models\Transaction\MoneyTransaction;
use App\Observers\BankObserver;
use App\Observers\BoxObserver;
use App\Observers\DocumentObserver;
use App\Observers\FieldObserver;
use App\Observers\FileTypeObserver;
use App\Observers\InvestorObserver;
use App\Observers\InvoiceCostObserver;
use App\Observers\InvoiceDetailObserver;
use App\Observers\InvoiceInvestorObserver;
use App\Observers\InvoiceObserver;
use App\Observers\InvoiceTemplateObserver;
use App\Observers\MoneyTransactionObserver;
use App\Observers\PartieObserver;
use App\Observers\ProductObserver;
use App\Observers\PurchaseOrderDetailObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\ReportJobObserver;
use App\Repository\Dashboard\DashboardRepository;
use App\Repository\Report\ReportRepository;
use App\Services\Cache\CacheManagementService;
use App\Services\Dashboard\DashboardService;
use App\Services\Database\TransactionManager;
use App\Services\Document\DocumentService;
use App\Services\Invoice\InvoiceConvertService;
use App\Services\Invoice\InvoiceInvestorBenefitService;
use App\Services\Invoice\InvoiceService;
use App\Services\Invoice\InvoiceServiceDetail;
use App\Services\Invoice\InvoiceTemplateService;
use App\Services\MoneyTransaction\MoneyTransactionService;
use App\Services\Notification\Flasher;
use App\Services\Partie\PartieService;
use App\Services\PdfGeneration\PdfGenerator;
use App\Services\Print\DocumentCalculationService;
use App\Services\Print\DocumentPrintService;
use App\Services\Print\IdentifierVisibilityService;
use App\Services\Print\PdfGenerationService;
use App\Services\PurchaseOrder\PurchaseOrderService;
use App\Services\PurchaseOrder\PurchaseOrderServiceDetail;
use App\Services\System\SystemSettingService;
use App\Services\User\UserService;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // transaction manager
        $this->app->bind(TransactionManagerInterface::class, TransactionManager::class);

        // flasher
        $this->app->bind(FlasherInterface::class, Flasher::class);

        // system settings service (singleton for performance)
        $this->app->singleton(SystemSettingService::class, function ($app) {
            return new SystemSettingService;
        });

        // Dashboard repository
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);

        // Report repository
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);

        // Cache management service (singleton for performance)
        $this->app->singleton(CacheManagementService::class);

        // Register all services
        $this->app->bind(InvoiceService::class, function ($app) {
            return new InvoiceService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class),
                $app->make(InvoiceInvestorBenefitService::class),
                $app->make(CacheManagementService::class)
            );
        });

        $this->app->bind(PartieService::class, function ($app) {
            return new PartieService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(\App\Services\File\FileService::class, function ($app) {
            return new \App\Services\File\FileService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(\App\Services\Investor\InvestorService::class, function ($app) {
            return new \App\Services\Investor\InvestorService(
                $app->make(FlasherInterface::class)
            );
        });

        $this->app->bind(PurchaseOrderService::class, function ($app) {
            return new PurchaseOrderService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class),
                $app->make(CacheManagementService::class)
            );
        });

        $this->app->bind(InvoiceConvertService::class, function ($app) {
            return new InvoiceConvertService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class),
                $app->make(CacheManagementService::class)
            );
        });

        $this->app->bind(InvoiceServiceDetail::class, function ($app) {
            return new InvoiceServiceDetail(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(PurchaseOrderServiceDetail::class, function ($app) {
            return new PurchaseOrderServiceDetail(
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(InvoiceInvestorBenefitService::class, function ($app) {
            return new InvoiceInvestorBenefitService(
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class),

            );
        });

        $this->app->bind(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(DashboardRepositoryInterface::class),
                $app->make(CacheManagementService::class)
            );
        });

        $this->app->bind(MoneyTransactionService::class, function ($app) {
            return new MoneyTransactionService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(DocumentService::class, function ($app) {
            return new DocumentService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class)
            );
        });

        $this->app->bind(InvoiceTemplateService::class, function ($app) {
            return new InvoiceTemplateService(
                $app->make(FlasherInterface::class),
                $app->make(TransactionManagerInterface::class)
            );
        });

        // Print services
        $this->app->bind(DocumentPrintService::class, function ($app) {
            return new DocumentPrintService(
                $app->make(SystemSettingService::class),
                $app->make(DocumentCalculationService::class),
                $app->make(IdentifierVisibilityService::class)
            );
        });

        $this->app->bind(PdfGenerationService::class, function ($app) {
            return new PdfGenerationService(
                $app->make(PdfGenerator::class)
            );
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Translatable::fallback(
            fallbackLocale: 'fr',
        );

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {

            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

            $this->app->register(TelescopeServiceProvider::class);

        }

        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());

        // Register model observers for cache invalidation and PDF cleanup
        Partie::observe(PartieObserver::class);
        Investor::observe(InvestorObserver::class);
        Box::observe(BoxObserver::class);
        Bank::observe(BankObserver::class);
        Field::observe(FieldObserver::class);
        FileType::observe(FileTypeObserver::class);
        Product::observe(ProductObserver::class);
        Invoice::observe(InvoiceObserver::class);
        InvoiceDetail::observe(InvoiceDetailObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        PurchaseOrderDetail::observe(PurchaseOrderDetailObserver::class);
        InvoiceInvestor::observe(InvoiceInvestorObserver::class);
        InvoiceCost::observe(InvoiceCostObserver::class);
        InvoiceTemplate::observe(InvoiceTemplateObserver::class);
        MoneyTransaction::observe(MoneyTransactionObserver::class);
        Document::observe(DocumentObserver::class);
        ReportJob::observe(ReportJobObserver::class);
    }
}

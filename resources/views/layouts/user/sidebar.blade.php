<div id="sidebar" class="h-auto min-h-screen z-40 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 md:relative bg-gray-800 text-white w-72 py-4 px-2 md:shrink-0 transition-all duration-300 ease-in-out">
    <a href="{{ route('index') }}" class="block">
        <h2 class="text-2xl font-bold sidebar-text hover:text-blue-400 transition-colors">{{ __('dashboard.title') }}</h2>
    </a>
    <hr class="h-px my-4 bg-gray-700 border-0 dark:bg-gray-700">
    <nav>
        <ul>
            @php 
                /** @var User $user */
                $user = Auth::user();
                $canManageRoles = $user->can('manage roles');
                $canViewUser = $user->can('view user');
                $canViewPartie = $user->can('view partie');
                $canViewInvestor = $user->can('view investor');
                $canViewMyCompanies = $user->can('view my_companies');
                $canViewMoneyTransaction = $user->can('view money_transaction');
                $canViewBank = $user->can('view bank');
                $canViewInvoice = $user->can('view invoice');
                $canViewPurchaseOrder = $user->can('view purchase_order');
                $canViewProduct = $user->can('view product');
                $canViewField = $user->can('view field');
                $canViewCost = $user->can('view cost');
                $canViewFile = $user->can('view file');
                $canManageDocuments = $user->can('manage documents');
                $canManageReport = $user->can('manage report');
                @endphp
            <x-nav-dropdown :title="__('links.system')"
                :active="request()->routeIs('settings.*')" :sub="false"
                :links="[
                    [
                        'url' => route('settings.company'), 
                        'title' => __('links.settings.company_info') , 
                        'active' => request()->routeIs('settings.company'), 
                        'subnav' => true,
                        'icon' => 'fas fa-cog',
                        'render' => true
                    ],
                    [
                        'url' => route('settings.permissions.index'), 
                        'title' => __('links.permissions') , 
                        'active' => request()->routeIs('settings.permissions.index'), 
                        'subnav' => true,
                        'icon' => 'fas fa-shield-alt',
                        'render' => $canManageRoles
                    ],
                    [
                        'url' => route('settings.users.index'), 
                        'title' => __('links.user.list') , 
                        'active' => request()->routeIs('settings.users.index'), 
                        'subnav' => true,
                        'icon' => 'fas fa-users',
                        'render' => $canViewUser
                    ],
                ]">
                <x-slot:icon>
                    <i class="fas fa-users-cog me-3"></i>
                </x-slot:icon>
                <x-slot:titleUi>
                    <span class="sidebar-text">{{__('links.system')}}</span>
                </x-slot:titleUi>
            </x-nav-dropdown>

            <x-nav-dropdown :title="__('links.partners')"
                :active="request()->routeIs('partners.*') || request()->routeIs('banks.*')" :sub="false"
                :links="[
                    [
                        'url' => route('partners.parties.index'), 
                        'title' => __('links.partie.list') , 
                        'active' => request()->routeIs('partners.parties.*'), 
                        'subnav' => true,
                        'icon' => 'fas fa-users',
                        'render' => $canViewPartie
                    ],
                    [
                        'url' => route('partners.investors.index'), 
                        'title' => __('links.investor.list') , 
                        'active' => request()->routeIs('partners.investors.*'), 
                        'subnav' => true,
                        'icon' => 'fas fa-hand-holding-usd',
                        'render' => $canViewInvestor
                    ],
                    [
                        'url' => route('partners.my-companies.index'), 
                        'title' => __('links.my_companies.list') , 
                        'active' => request()->routeIs('partners.my-companies.*'), 
                        'subnav' => true,
                        'icon' => 'fas fa-building',
                        'render' => $canViewMyCompanies
                    ],
                    [
                        'url' => route('partners.money-transactions.index'), 
                        'title' => __('links.money_transaction.list') , 
                        'active' => request()->routeIs('partners.money-transactions.index'), 
                        'subnav' => true,
                        'icon' => 'fas fa-dollar-sign',
                        'render' => $canViewMoneyTransaction
                    ],
                    [
                        'url' => route('banks.index'), 
                        'title' => __('links.bank.list') , 
                        'active' => request()->routeIs('banks.*'), 
                        'subnav' => true,
                        'icon' => 'fas fa-university',
                        'render' => $canViewBank
                    ],
                ]">
                <x-slot:icon>
                    <i class="fas fa-handshake me-3"></i>
                </x-slot:icon>
                <x-slot:titleUi>
                    <span class="sidebar-text">{{__('links.partners')}}</span>
                </x-slot:titleUi>
            </x-nav-dropdown>

            @if($canViewInvoice)
            <li class="mb-2">
                <x-nav-link href="{{route('invoice-manage.invoice.list')}}" :active="request()->routeIs('invoice-manage.invoice.list')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-file-invoice me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.invoice.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canViewInvoice)
            <li class="mb-2">
                <x-nav-link href="{{route('invoice-templates.list')}}" :active="request()->routeIs('invoice-templates.*') || request()->routeIs('invoices.template-builder')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-paint-brush me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.invoice.template_builder")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canViewInvoice)
            <li class="mb-2">
                <x-nav-link href="{{route('invoice-investor-benefits.list')}}" :active="request()->routeIs('invoice-investor-benefits.*')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-chart-line me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.invoice_investor_benefit.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canViewPurchaseOrder)
            <li class="mb-2">
                <x-nav-link href="{{route('purchase-order-manage.purchase-order.list')}}" :active="request()->routeIs('purchase-order-manage.*')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-shopping-cart me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.purchase_order.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canViewProduct)
            <x-nav-dropdown :title="__('links.products')"
                :active="request()->routeIs('manage_products.*')" :sub="false"
                :links="[
                    [
                        'url' => route('manage_products.products.list'), 
                        'title' => __('links.product.list') , 
                        'active' => request()->routeIs('manage_products.products.*'), 
                        'subnav' => true,
                        'icon' => 'fas fa-box',
                        'render' => true
                    ],
                    [
                        'url' => route('manage_products.product_units.list'), 
                        'title' => __('links.product_unit.list') , 
                        'active' => request()->routeIs('manage_products.product_units.*'), 
                        'subnav' => true,
                        'icon' => 'fas fa-ruler',
                        'render' => true
                    ],
                ]">
                <x-slot:icon>
                    <i class="fas fa-cubes me-3"></i>
                </x-slot:icon>
                <x-slot:titleUi>
                    <span class="sidebar-text">{{__('links.products')}}</span>
                </x-slot:titleUi>
            </x-nav-dropdown>
            @endif


            @if($canViewField)
            <li class="mb-2">
                <x-nav-link href="{{route('fields')}}" :active="request()->routeIs('fields*')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-tags me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.field.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canViewCost)
            <li class="mb-2">
                <x-nav-link href="{{route('costs.index')}}" :active="request()->routeIs('costs.*')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-money-bill-wave me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.cost.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canViewFile)
            <li class="mb-2">
                <x-nav-link href="{{route('files.index')}}" :active="request()->routeIs('files.*')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-file me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.file.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canManageReport)
            <li class="mb-2">
                <x-nav-link href="{{route('reports.index')}}" :active="request()->routeIs('reports.*')" :sub="false">
                    <x-slot:icon>
                        <i class="fas fa-chart-bar me-3"></i>
                    </x-slot:icon>
                    <span class="sidebar-text">{{__("links.report.list")}}</span>
                </x-nav-link>
            </li>
            @endif

            @if($canManageDocuments)
                <x-nav-dropdown :title="__('links.documents')"
                    :active="request()->routeIs('boxes.*') || request()->routeIs('file-types.*') || request()->routeIs('documents.*') || request()->routeIs('document-audits.*')" :sub="false"
                    :links="[
                        [
                            'url' => route('boxes.index'), 
                            'title' => __('links.box.list') , 
                            'active' => request()->routeIs('boxes.*'), 
                            'subnav' => true,
                            'icon' => 'fas fa-archive',
                            'render' => true
                        ],
                        [
                            'url' => route('file-types.index'), 
                            'title' => __('links.file_type.list') , 
                            'active' => request()->routeIs('file-types.*'), 
                            'subnav' => true,
                            'icon' => 'fas fa-file-alt',
                            'render' => true
                        ],
                        [
                            'url' => route('documents.index'), 
                            'title' => __('links.document.list') , 
                            'active' => request()->routeIs('documents.*'), 
                            'subnav' => true,
                            'icon' => 'fas fa-folder',
                            'render' => true
                        ],
                        [
                            'url' => route('document-audits.index'), 
                            'title' => __('links.document_audit.list') , 
                            'active' => request()->routeIs('document-audits.*'), 
                            'subnav' => true,
                            'icon' => 'fas fa-history',
                            'render' => true
                        ],
                    ]">
                    <x-slot:icon>
                        <i class="fas fa-folder-open me-3"></i>
                    </x-slot:icon>
                    <x-slot:titleUi>
                        <span class="sidebar-text">{{__('links.documents')}}</span>
                    </x-slot:titleUi>
                </x-nav-dropdown>
            @endif
        </ul>
    </nav>
</div>


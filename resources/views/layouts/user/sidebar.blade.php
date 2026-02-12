<div id="sidebar"
    class="h-auto min-h-screen z-40 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 md:relative bg-gray-800 text-white w-72 py-4 px-2 md:shrink-0 transition-all duration-300 ease-in-out">
    <a href="{{ route('index') }}" class="block">
        <h2 class="text-2xl font-bold sidebar-text hover:text-blue-400 transition-colors">{{ __('dashboard.title') }}
        </h2>
    </a>
    <hr class="h-px my-4 bg-gray-700 border-0 dark:bg-gray-700">
    <nav>
        <ul>
            @php
                /** @var User $user */
                $user = Auth::user();
                $canManageRoles = $user->can('manage roles');
                $canViewUser = $user->can('view user');

            @endphp
            <x-nav-dropdown :title="__('links.system')" :active="request()->routeIs('settings.*')" :sub="false" :links="[
                [
                    'url' => route('settings.company'),
                    'title' => __('links.settings.company_info'),
                    'active' => request()->routeIs('settings.company'),
                    'subnav' => true,
                    'icon' => 'fas fa-cog',
                    'render' => true,
                ],
                [
                    'url' => route('settings.permissions.index'),
                    'title' => __('links.permissions'),
                    'active' => request()->routeIs('settings.permissions.index'),
                    'subnav' => true,
                    'icon' => 'fas fa-shield-alt',
                    'render' => $canManageRoles,
                ],
                [
                    'url' => route('settings.users.index'),
                    'title' => __('links.user.list'),
                    'active' => request()->routeIs('settings.users.index'),
                    'subnav' => true,
                    'icon' => 'fas fa-users',
                    'render' => $canViewUser,
                ],
            ]">
                <x-slot:icon>
                    <i class="fas fa-users-cog me-3"></i>
                </x-slot:icon>
                <x-slot:titleUi>
                    <span class="sidebar-text">{{ __('links.system') }}</span>
                </x-slot:titleUi>
            </x-nav-dropdown>

        </ul>
    </nav>
</div>

@extends('layouts.user.master')
@section('css')

    @section('title')
        {{__('links.user.list')}}
    @stop
@endsection

@section('page_title')
    {{ __('links.user.list') }}
@endsection

@section('content')
    <div class="flex justify-between items-center my-2 p-4 shadow-sm" >
        <h1 class="text-xl font-bold">{{__('links.user.list')}}</h1>
       @can("add user")
        <div x-data>
            <x-button
                name="myModal"
                x-on:click="$dispatch('open-modal', { detail: 'myModal' })">
                <x-slot:icon>
                    <i class="fa-solid fa-plus me-2"></i>
                </x-slot:icon>
               {{__("form.actions.add")}}
            </x-button>
        </div>
        @endcan
    </div>

    @can("add user")
    <x-modal name="myModal" title="My Modal" :show="$errors->hasBag('createUser')">
        <x-slot:modalhead>
            {{__("form.user.add")}}
        </x-slot>
        <form id="add-form" method="post" action="{{ route('settings.users.store') }}" class="space-y-2">
            @csrf
            @method('post')

            <div>
                <x-input-label for="add_user_name" :value=" ucwords(__('user.profile.name'))" />
                <x-text-input id="add_user_name" name="name" type="text" value="{{ old('name') }}" class="mt-1 block w-full"  />
                <x-input-error :messages="$errors->createUser->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="add_user_email" :value=" ucwords(__('user.profile.email'))" />
                <x-text-input id="add_user_email" name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full"  />
                <x-input-error :messages="$errors->createUser->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="add_user_password" :value=" ucwords(__('user.profile.password.password'))" />
                <x-text-input id="add_user_password" name="password" type="password" class="mt-1 block w-full"  />
                <x-input-error :messages="$errors->createUser->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="" :value=" ucwords(__('roles.role'))" />
                @php
                    $options = [] ;
                @endphp
                @foreach($roles as $role)
                    @php $options[] = ['value' => $role->id, 'text' => __('roles.'.$role->name), 'selected' => old('role') == $role->id] @endphp
                @endforeach
                <x-form.select-box id="" name="role"  :options="$options">
                </x-form.select-box>
                <x-input-error :messages="$errors->createUser->get('role')" class="mt-2" />
            </div>
        </form>
        <x-slot:modalfooter>
            <div class="flex justify-end">
                <x-button form="add-form" color_type="success" >{{ __('form.actions.save') }}</x-button>
            </div>
        </x-slot>
    </x-modal>
    @endcan
{{--
    <x-modal name="delete" title="My Modal" :show="$errors->hasBag('deleteUser')" :inputValue="old('id')">
        <x-slot:modalhead>
            {{__("form.user.delete")}}
        </x-slot>
        <form id="delete-form" method="post" action="{{route("users.delete")}}" class="space-y-2">
            @csrf
            @method('post')

            <div>
                <input type="hidden" name="id" x-model="inputValue"/>
                <p class="my-1">
                    {{__("form.actions.confirm_delete")}}
                    <span class="text-danger" x-text="payload.userName"></span>
                </p>
            </div>

            <div>
                <x-input-label for="delete_user_reason" :value=" ucwords(__('messages.global.reason'))" />
                <x-text-input id="delete_user_reason" name="reason" type="text" class="mt-1 block w-full" min="5" max="100"  />
                <x-input-error :messages="$errors->deleteUser->get('reason')" class="mt-2" />
            </div>

        </form>
        <x-slot:modalfooter>
            <div class="flex justify-end">
                <x-button form="delete-form" color_type="danger" >{{ __('form.actions.delete') }}</x-button>
            </div>
        </x-slot>
    </x-modal>--}}

    @can("delete user")
    <!-- Deactivate User Modal -->
    <x-modal name="deactivate-user" title="My Modal" :show="false" :inputValue="old('id')">
        <x-slot:modalhead>
            {{__("form.user.deactivate")}}
        </x-slot>
        <form id="deactivate-form" method="post" action="{{route('settings.users.toggle-status')}}" class="space-y-2">
            @csrf
            @method('post')

            <div>
                <input type="hidden" name="id" x-model="inputValue"/>
                <p class="my-1">
                    {{__("form.actions.confirm_deactivate")}}
                    <span class="text-warning font-semibold" x-text="payload.userName"></span>
                </p>
            </div>
        </form>
        <x-slot:modalfooter>
            <div class="flex justify-end gap-2">
                <x-button x-on:click="$dispatch('close-modal', { detail: 'deactivate-user' })" color_type="secondary" :outline="true">{{ __('form.actions.cancel') }}</x-button>
                <x-button form="deactivate-form" color_type="warning">{{ __('form.actions.deactivate') }}</x-button>
            </div>
        </x-slot>
    </x-modal>

    <!-- Activate User Modal -->
    <x-modal name="activate-user" title="My Modal" :show="false" :inputValue="old('id')">
        <x-slot:modalhead>
            {{__("form.user.activate")}}
        </x-slot>
        <form id="activate-form" method="post" action="{{route('settings.users.toggle-status')}}" class="space-y-2">
            @csrf
            @method('post')

            <div>
                <input type="hidden" name="id" x-model="inputValue"/>
                <p class="my-1">
                    {{__("form.actions.confirm_activate")}}
                    <span class="text-success font-semibold" x-text="payload.userName"></span>
                </p>
            </div>
        </form>
        <x-slot:modalfooter>
            <div class="flex justify-end gap-2">
                <x-button x-on:click="$dispatch('close-modal', { detail: 'activate-user' })" color_type="secondary" :outline="true">{{ __('form.actions.cancel') }}</x-button>
                <x-button form="activate-form" color_type="success">{{ __('form.actions.activate') }}</x-button>
            </div>
        </x-slot>
    </x-modal>
    @endcan

    <div class="mb-4 flex items-center gap-4">
        <div class="flex items-center gap-2">
            <div x-data="{ showInactive: @js($showInactive) }">
                <input type="checkbox" 
                       x-model="showInactive"
                       id="show-inactive"
                       class="rounded border-gray-300 text-primary focus:ring-primary"
                       x-on:change="
                           const url = new URL(window.location.href);
                           if (showInactive) {
                               url.searchParams.set('showInactive', '1');
                           } else {
                               url.searchParams.delete('showInactive');
                           }
                           window.location.href = url.toString();
                       ">
                <label for="show-inactive" class="text-sm font-medium text-gray-700">
                    {{__("user.show_inactive")}}
                </label>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto max-w-[95vw] md:max-w-full pt-2">
        <livewire:user-table :showInactive="$showInactive"/>
    </div>
@endsection

@extends('layouts.user.master')
@section('css')
    @section('title')
        {{$user->name}}
    @stop
@endsection

@section('page_title')
    {{ __('links.user.list') }}
@endsection

@section('content')
<div class=" p-4 shadow-md">
    <section class="max-w-xl">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ ucwords($user->name) }}
            </h2>
        </header>

        <form class="mt-6 space-y-6" action="{{route('settings.users.update',['user' => $user])}}" method="post">
            @csrf
            @method("patch")
            <div>
                <x-input-label for="name" :value="ucwords(__('user.profile.name'))" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full outline-none" :icon="false" :value="$user->name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->updateUser->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="ucwords(__('user.profile.email'))" />
                <x-text-input id="email" name="email" type="text" class="mt-1 block w-full outline-none" :value="$user->email"  autocomplete="username" />
                <x-input-error :messages="$errors->updateUser->get('email')" class="mt-2" />
            </div>
            

            <div>
                <x-input-label for="" :value=" ucwords(__('roles.role'))" />
                @php
                    $options = [] ;
                @endphp
                @foreach($roles as $role)
                    @php $options[] = ['value' => $role->id, 'text' => __('roles.'.$role->name), 'selected' => $userRole == $role->name] @endphp
                @endforeach
                <x-form.select-box id="" name="role"  :options="$options">
                </x-form.select-box>
                <x-input-error :messages="$errors->createUser->get('role')" class="mt-2" />
            </div>

            <div>
                <x-button color_type="success" >{{ __('form.actions.update') }}</x-button>
            </div>
        </form>
    </section>
</div>
@endsection

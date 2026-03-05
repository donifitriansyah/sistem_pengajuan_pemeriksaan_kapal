@extends('layouts.app')

@section('title')
    Ganti Password
@endsection()

@section('content')
    <div class="content-card">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Ganti Password') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
            </p>
        </header>

        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('put')

            <div>
                <x-input-label for="update_password_current_password" class="form-label" :value="__('Password Lama')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password"
                    class="form-control" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" class="form-label mt-2" :value="__('Password Baru')" />
                <x-text-input id="update_password_password" name="password" type="password" class="form-control"
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" class="form-label mt-2" :value="__('Konfirmasi Password Baru')" />
                <x-text-input id="update_password_password_confirmation" class="form-control" name="password_confirmation" type="password"
                    class="form-control" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button class="btn btn-primary mt-3">{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'password-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600">{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div>
@endsection

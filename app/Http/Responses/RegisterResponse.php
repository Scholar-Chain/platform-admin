<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\RegistrationResponse as BaseRegisterResponse;

class RegisterResponse extends BaseRegisterResponse
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect(filament()->getDefaultPanel()->getLoginUrl());
    }
}

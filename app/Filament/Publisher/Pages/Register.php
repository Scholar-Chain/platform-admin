<?php

namespace App\Filament\Publisher\Pages;

use App\Enums\UserRole;
use Filament\Forms\Set;
use Filament\Pages\Page;
use App\Models\Publisher;
use Filament\Support\RawJs;
use App\Events\PublisherRegistered;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Pages\Auth\Register as AuthRegister;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Register extends AuthRegister
{
    public $isWhatsappVerified = false;
    public $otp = null;

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new PublisherRegistered($user));

        // filament()->auth()->login($user);

        // session()->regenerate();

        return app(RegistrationResponse::class);
    }

    protected function handleRegistration(array $data): Model
    {
        $publisher = $data['publisher'];
        unset($data['publisher']);

        $user = $this->getUserModel()::create($data);
        $user->assignRole(UserRole::PUBLISHER->value);

        Publisher::create([
            'user_id' => $user->id,
            ...$publisher
        ]);

        Notification::make()
            ->title('Registrasi anda berhasil, silahkan cek notifikasi Whatsapp yang kami berikan untuk info lebih lanjut!')
            ->success()
            ->send();

        return $user;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getPublishingNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getWebsiteFormComponent(),
                        $this->getOjsDriverUrlFormComponent(),
                        $this->getAdressFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getOjsDriverUrlFormComponent(): Component
    {
        return TextInput::make('publisher.ojs_driver_url')
            ->url()
            ->required()
            ->placeholder('https://connector.ojs.com')
            ->label("Link Website Konektor")
            ->helperText("");
    }

    protected function getWebsiteFormComponent(): Component
    {
        return TextInput::make('publisher.website')
            ->url()
            ->label("Link Website Publisher")
            ->placeholder('https://ojs.com')
            ->required();
    }

    protected function getAdressFormComponent(): Component
    {
        return Textarea::make('publisher.address')
            ->label("Alamat");
    }

    protected function getPublishingNameFormComponent(): Component
    {
        return TextInput::make('publisher.publishing_name')
            ->label("Nama Publishing")
            ->required()
            ->maxLength(255);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label("Nama Lengkap")
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getPhoneFormComponent(): Component
    {
        return PhoneInput::make('phone')
            ->unique(ignoreRecord: true)
            ->validateFor(
                country: 'ID',
                lenient: true,
            )
            ->displayNumberFormat(PhoneInputNumberType::NATIONAL)
            ->disallowDropdown()
            ->initialCountry('id')
            ->placeholderNumberType('FIXED_LINE')
            ->onlyCountries(['id'])
            ->defaultCountry('ID')
            ->placeholder('08XX-XXXX-XXXX')
            ->label('No. Whatsapp')
            ->required();
    }
}

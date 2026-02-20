<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
  protected function getForms(): array
  {
    return [
      'form'  =>  $this->form(
        $this->makeForm()
          ->schema([
            $this->getLogoFormComponent(),
            $this->getNameFormComponent(),
            $this->getUsernameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent()
          ])
          ->statePath('data'),
      ),
    ];
  }

  protected function getLogoFormComponent(): Component
  {
      return FileUpload::make('logo')
        ->label('Logo Toko')
        ->image()
        ->required();
  }

  protected function getUsernameFormComponent(): Component
  {
    return TextInput::make('username')
      ->label('Username')
      ->hint('Minimal 5 karakter, tidak boleh pakai spasi')
      ->required()
      ->minLength(5)
      ->unique($this->getUserModel());
  }

  protected function getPasswordFormComponent(): Component
  {
    return TextInput::make('password')
      ->password()
      ->required()
      ->rule(Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised()
      )
      ->label('Password')
      ->revealable()
      ->hint('Password min 8 dan kombinasi');
  }
}

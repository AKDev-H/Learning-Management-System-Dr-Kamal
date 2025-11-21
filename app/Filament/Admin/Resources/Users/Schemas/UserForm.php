<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),
                Grid::make(2)
                    ->schema([
                        Select::make('role_id')
                            ->label('Role')
                            ->relationship('role', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Role::class, 'slug'),
                            ]),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->minLength(8),
                    ]),
                DateTimePicker::make('email_verified_at'),
                Grid::make(2)
                    ->schema([
                        Toggle::make('is_banned')
                            ->label('Banned')
                            ->default(false),
                        DateTimePicker::make('banned_at')
                            ->visible(fn ($get): bool => $get('is_banned')),
                    ]),
                Textarea::make('ban_reason')
                    ->label('Ban Reason')
                    ->rows(3)
                    ->visible(fn ($get): bool => $get('is_banned'))
                    ->columnSpanFull(),
            ]);
    }
}

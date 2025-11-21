<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email')
                                    ->label('Email address'),
                                TextEntry::make('role.name')
                                    ->label('Role')
                                    ->placeholder('-'),
                                TextEntry::make('email_verified_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),
                Section::make('Ban Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                IconEntry::make('is_banned')
                                    ->label('Banned')
                                    ->boolean(),
                                TextEntry::make('banned_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                        TextEntry::make('ban_reason')
                            ->label('Ban Reason')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record): bool => $record->is_banned),
                Section::make('Two Factor Authentication')
                    ->schema([
                        TextEntry::make('two_factor_confirmed_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->collapsible()
                    ->collapsed(),
                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

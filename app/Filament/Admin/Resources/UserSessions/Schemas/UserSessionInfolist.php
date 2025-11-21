<?php

namespace App\Filament\Admin\Resources\UserSessions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserSessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name'),
                                TextEntry::make('user.email')
                                    ->label('Email'),
                            ]),
                    ]),
                Section::make('Session Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('session_id')
                                    ->label('Session ID')
                                    ->copyable(),
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->copyable(),
                                TextEntry::make('device_name')
                                    ->label('Device Name')
                                    ->placeholder('-'),
                                TextEntry::make('device_type')
                                    ->label('Device Type')
                                    ->placeholder('-'),
                                TextEntry::make('platform')
                                    ->placeholder('-'),
                                TextEntry::make('browser')
                                    ->placeholder('-'),
                                IconEntry::make('is_active')
                                    ->label('Active')
                                    ->boolean(),
                            ]),
                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->columnSpanFull()
                            ->placeholder('-'),
                    ]),
                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('last_activity')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),
            ]);
    }
}

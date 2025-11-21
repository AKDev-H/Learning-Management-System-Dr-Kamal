<?php

namespace App\Filament\Admin\Resources\UserSessions\Tables;

use App\Models\UserSession;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UserSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('device_name')
                    ->label('Device')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('device_type')
                    ->label('Device Type')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('platform')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('browser')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('last_activity')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('banUser')
                    ->label('Ban User')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('ban_reason')
                            ->label('Ban Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (UserSession $record, array $data): void {
                        $user = $record->user;

                        if ($user) {
                            $user->update([
                                'is_banned' => true,
                                'banned_at' => now(),
                                'ban_reason' => $data['ban_reason'],
                            ]);

                            Notification::make()
                                ->title('User banned successfully')
                                ->body("User {$user->name} has been banned.")
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn(UserSession $record): bool => $record->user && ! $record->user->is_banned),
            ])
            ->defaultSort('last_activity', 'desc');
    }
}

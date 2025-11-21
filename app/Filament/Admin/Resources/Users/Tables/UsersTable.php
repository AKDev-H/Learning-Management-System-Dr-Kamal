<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->label('Role')
                    ->sortable(),
                IconColumn::make('is_banned')
                    ->label('Banned')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('banned_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('ban')
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
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'is_banned' => true,
                                'banned_at' => now(),
                                'ban_reason' => $data['ban_reason'],
                            ]);

                            Notification::make()
                                ->title('User banned successfully')
                                ->success()
                                ->send();
                        })
                        ->visible(fn(User $record): bool => ! $record->is_banned),
                    Action::make('unban')
                        ->label('Unban User')
                        ->icon('heroicon-o-shield-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (User $record): void {
                            $record->update([
                                'is_banned' => false,
                                'banned_at' => null,
                                'ban_reason' => null,
                            ]);

                            Notification::make()
                                ->title('User unbanned successfully')
                                ->success()
                                ->send();
                        })
                        ->visible(fn(User $record): bool => $record->is_banned),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

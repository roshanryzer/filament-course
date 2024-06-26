<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\RelationManagers;
use App\Models\Talk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->description(function (Talk $record) {
                        return Str::of($record->abstract)->limit(40);
                    }),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($record->speaker->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('new_talk')
                    ->label('New'),
//                Tables\Columns\TextColumn::make('status')
//                    ->label('Status')
//                    ->badge()
//                    ->sortable()
//                    ->color(function ($state) {
//                        return $state->getColor();
//                    }),
                Tables\Columns\SelectColumn::make('status')
                    ->options(TalkStatus::class)
                    ->label('Status'),
                Tables\Columns\IconColumn::make('length')
                    ->icon(function ($state) {
                        return match ($state) {
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    })

            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->relationship('speaker', 'name'),
                Tables\Filters\Filter::make('has_avatar')
                    ->label('Only Avatar')
                    ->toggle()
                    ->query(function ($query) {
                        return $query->whereHas('speaker', function (Builder $query) {
                            $query->whereNotNull('avatar');
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->visible(function($record){
                            return $record->status === (TalkStatus::SUBMITTED);
                        })
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($record) {
                            $record->approve();
                        })
                        ->after(function () {
                            Notification::make()->success()->title('This talk has been Approved')
                                ->duration(1000)
                                ->body('This speaker has been notified and the talk has been added to the conference schedule')
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->visible(function($record){
                            return $record->status === (TalkStatus::SUBMITTED);
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('danger')
                        ->action(function ($record) {
                            $record->reject();
                        })
                        ->after(function () {
                            Notification::make()->success()->title('This talk has been Rejected')
                                ->duration(1000)
                                ->body('This speaker has been Rejected')
                                ->send();
                        })
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                    ->action(function(Collection $records){
                        $records->each->approve();
                    }),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make()
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                ->tooltip('This will export all the records visible in the table')
                ->action(function($livewire){
                    $livewire->getFilteredTableQuery();
                })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
//            'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}

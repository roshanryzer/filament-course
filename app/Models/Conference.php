<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'region',
        'status',
        'venue_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'venue_id' => 'integer',
        'region' => Region::class

    ];

    public static function getForm(): array
    {

        return
            [
                Section::make('Conference Details')
                    ->collapsible()
                    ->description('Description lorem ipsum')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->columnSpanFull()
                            ->label('Conference Name')
                            ->required()
                            ->maxLength(60),
                        MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->label('Conference Description')
                            ->helperText('This is the name of the conference')
                            ->hint('This is the name of the conference')
                            ->hintIcon('heroicon-o-rectangle-stack')
                            ->required(),
                        DatePicker::make('start_date')
                            ->native(false),
                        DatePicker::make('end_date'),
                        Fieldset::make('status')
                            ->columns(1)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                    ])
                                    ->required(),
                                Toggle::make('is_published')
                                    ->default(true)
                                    ->required()
                            ]),

                    ]),
                Section::make('Location')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Select::make('region')
                            ->live()
                            ->enum(Region::class)
                            ->options(Region::class),
                        Select::make('venue_id')
                            ->searchable()
                            ->preload()
                            ->editOptionForm(Venue::getForm())
                            ->createOptionForm(Venue::getForm())
                            ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                return $query->where('region', $get('region'));
                            })
                    ]),

                CheckboxList::make('speakers')
                    ->columnSpanFull()
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3)
                    ->relationship('speakers', 'name')
                    ->options(
                        Speaker::all()->pluck('name', 'id')
                    )->required(),

                Actions::make([
                    Action::make('star')
                        ->label('Fill with Factory Data')
                        ->icon('heroicon-m-star')
                        ->visible(function (string $operation){
                            if($operation !== 'create'){
                                return false;
                            }
                            if(!app()->environment('local')){
                                return false;
                            }
                            return true;
                        })
                        ->action(function ($livewire) {
                             $data = Conference::factory()->make()->toArray();
                              unset($data['venue_id']);
                             $livewire->form->fill($data);
                        }),
                ]),
            ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }
}

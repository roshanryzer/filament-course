<?php

namespace App\Models;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Talk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abstract',
        'speaker_id',
        'length',
        'status',
        'new_talk',
    ];

    protected $casts = [
        'id' => 'integer',
        'speaker_id' => 'integer',
        'status'   => TalkStatus::class,
        'length'   => TalkLength::class
    ];

    public static function getForm($speakerId = null): array{
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            RichEditor::make('abstract')
                ->required()
                ->maxLength(65535)
                ->columnSpanFull(),
            Select::make('speaker_id')
                ->hidden(function() use ($speakerId){
                    return $speakerId !== null;
                })
                ->relationship('speaker', 'name')
                ->required(),
        ];
    }

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function approve(): void
    {
        $this->status = TalkStatus::APPROVED;
        $this->save();
    }
    public function reject(): void
    {
        $this->status = TalkStatus::REJECTED;
        $this->save();
    }
}

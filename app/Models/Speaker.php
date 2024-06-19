<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;
    const QUALIFICATIONS =[
        'industry-expert' => 'Industry Expert',
        'innovator' => 'Innovator',
        'thought-leader' => 'Thought Leader',
        'entrepreneur' => 'Entrepreneur',
        'technology-specialist' => 'Technology Specialist',
        'strategic-planner' => 'Strategic Planner',
        'financial-analyst' => 'Financial Analyst',
        'marketing-guru' => 'Marketing Guru',
        'operations-manager' => 'Operations Manager',
        'human-resources-leader' => 'Human Resources Leader',
        'sustainability-advocate' => 'Sustainability Advocate',
        'customer-experience-expert' => 'Customer Experience Expert'
    ];

    protected $fillable = [
        'name',
        'email',
        'bio',
        'twitter_handle',
        'qualifications',
        'avatar',
    ];

    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array'
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }
    public function talks(): HasMany
    {
        return $this->hasMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            FileUpload::make('avatar')
            ->label('Avatar')
                ->imageEditor()
            ->avatar()
            ->image()
            ->maxSize(1024*1024*10),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            RichEditor::make('bio')
                ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->required()
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options(self::QUALIFICATIONS)->descriptions([
                    'business-leader' => 'A person who holds a senior position in a company and is responsible for guiding the business to achieve its goals.',
                    'industry-expert' => 'An individual with deep knowledge and expertise in a specific industry.',
                    'innovator' => 'A person known for their innovative ideas and contributions to their field.',
                    'thought-leader' => 'An individual recognized for their influential ideas and opinions in their industry.',
                    'entrepreneur' => 'A successful business owner or founder with a track record of starting and growing businesses.',
                    'technology-specialist' => 'An expert in the latest technologies and their applications in business.',
                    'strategic-planner' => 'A person skilled in creating and implementing long-term business strategies.',
                    'financial-analyst' => 'An expert in financial markets, investments, and economic trends.',
                    'marketing-guru' => 'A specialist in modern marketing techniques and strategies.',
                    'operations-manager' => 'A leader in optimizing business operations and efficiency.',
                    'human-resources-leader' => 'An expert in managing and developing human capital.',
                    'sustainability-advocate' => 'A professional focused on promoting sustainable business practices.',
                    'customer-experience-expert' => 'A specialist in enhancing customer satisfaction and loyalty.'
                ])
                ->columns(3)
        ];
    }
}

<?php

namespace App\Filament\Resources\ProposalRequests\Schemas;

use App\Models\ProposalRequest;
use App\Support\Localization\Locale;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Number;

class ProposalRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Request')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('reference')
                            ->weight('bold')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime(),
                        TextEntry::make('assignee.name')
                            ->label('Assigned to')
                            ->placeholder('Unassigned'),
                        TextEntry::make('locale')
                            ->label('Submitted in')
                            ->formatStateUsing(fn (string $state): string => Locale::resolve($state)->englishName()),
                        TextEntry::make('status_changed_at')
                            ->label('Status last changed')
                            ->since()
                            ->placeholder('Not changed yet'),
                    ]),

                Section::make('Client')
                    ->columns(2)
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name')
                            ->extraAttributes(['dir' => 'auto']),
                        TextEntry::make('company')
                            ->placeholder('Not provided')
                            ->extraAttributes(['dir' => 'auto']),
                        TextEntry::make('email')
                            ->copyable()
                            ->url(fn (ProposalRequest $record): string => "mailto:{$record->email}"),
                        TextEntry::make('phone')
                            ->copyable()
                            ->url(fn (ProposalRequest $record): string => 'tel:'.preg_replace('/[^0-9+]/', '', $record->phone)),
                    ]),

                Section::make('Project')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('sector')
                            ->state(fn (ProposalRequest $record): ?string => $record->sector?->translate('name', 'en'))
                            ->placeholder('Not specified'),
                        TextEntry::make('project_location')
                            ->placeholder('Not specified')
                            ->extraAttributes(['dir' => 'auto']),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html()
                            ->extraAttributes(['dir' => 'auto']),
                    ]),

                Section::make('Attachment')
                    ->columns(3)
                    ->columnSpanFull()
                    ->visible(fn (ProposalRequest $record): bool => $record->hasAttachment())
                    ->schema([
                        TextEntry::make('attachment_original_name')
                            ->label('File')
                            ->icon(Heroicon::OutlinedPaperClip)
                            ->url(fn (ProposalRequest $record): ?string => auth()->user()?->can('downloadAttachment', $record)
                                ? route('admin.proposal-requests.attachment', $record)
                                : null),
                        TextEntry::make('attachment_size')
                            ->label('Size')
                            ->formatStateUsing(fn (?int $state): string => Number::fileSize((int) $state, maxPrecision: 1)),
                        TextEntry::make('attachment_mime')
                            ->label('Type'),
                    ]),

                Section::make('Internal notes')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('internal_notes')
                            ->hiddenLabel()
                            ->placeholder('No notes yet.')
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html(),
                    ]),
            ]);
    }
}

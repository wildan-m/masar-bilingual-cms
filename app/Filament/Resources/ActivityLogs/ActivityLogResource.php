<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Enums\ActivityEvent;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * Read-only audit trail of changes made in the CMS and on RFPs.
 */
class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $modelLabel = 'activity';

    protected static ?string $pluralModelLabel = 'activity log';

    protected static ?string $navigationLabel = 'Activity log';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 20;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextEntry::make('created_at')
                    ->label('When')
                    ->dateTime(),
                TextEntry::make('user.name')
                    ->label('By')
                    ->placeholder(fn (ActivityLog $record): string => self::anonymousCauser($record)),
                TextEntry::make('event')
                    ->badge(),
                TextEntry::make('subject_type')
                    ->label('Record type')
                    ->formatStateUsing(fn (?string $state): string => Str::headline((string) $state)),
                TextEntry::make('subject_label')
                    ->label('Record')
                    ->placeholder('Several records'),
                TextEntry::make('ip_address')
                    ->label('IP address')
                    ->placeholder('Not recorded'),
                Section::make('Changes')
                    ->columnSpanFull()
                    ->schema([
                        ViewEntry::make('properties')
                            ->hiddenLabel()
                            ->view('filament.activity-log.changes'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder(fn (ActivityLog $record): string => self::anonymousCauser($record)),
                TextColumn::make('event')
                    ->badge(),
                TextColumn::make('subject_type')
                    ->label('Record type')
                    ->formatStateUsing(fn (?string $state): string => Str::headline((string) $state)),
                TextColumn::make('subject_label')
                    ->label('Record')
                    ->searchable()
                    ->placeholder('Several records'),
                TextColumn::make('attributes')
                    ->label('Fields')
                    ->state(fn (ActivityLog $record): string => collect($record->attributeNames())->map(Str::headline(...))->join(', '))
                    ->limit(60)
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->options(ActivityEvent::class)
                    ->multiple(),
                SelectFilter::make('subject_type')
                    ->label('Record type')
                    ->options(fn (): array => collect(Relation::morphMap())
                        ->keys()
                        ->mapWithKeys(fn (string $alias): array => [$alias => Str::headline($alias)])
                        ->all()),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }

    private static function anonymousCauser(ActivityLog $record): string
    {
        return $record->subject_type === 'proposal_request' && $record->event === ActivityEvent::Created
            ? 'Website visitor'
            : 'System';
    }
}

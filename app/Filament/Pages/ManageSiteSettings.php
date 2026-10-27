<?php

namespace App\Filament\Pages;

use App\Filament\Support\LocaleTabs;
use App\Models\Setting;
use App\Support\Localization\Locale;
use App\Support\Settings\SiteSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

/**
 * Company details, contact information, home page copy and social links.
 *
 * @property-read Schema $form
 */
class ManageSiteSettings extends Page
{
    protected static ?string $title = 'Site settings';

    protected static ?string $slug = 'site-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Website content';

    protected static ?int $navigationSort = 90;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Gate::allows('manage', Setting::class);
    }

    public function mount(SiteSettings $settings): void
    {
        $this->form->fill($settings->formState());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('Company')
                    ->schema([
                        LocaleTabs::make(fn (Locale $locale): array => [
                            TextInput::make("company_name.{$locale->value}")
                                ->label('Company name')
                                ->required($locale->isFallback())
                                ->maxLength(120),
                            TextInput::make("tagline.{$locale->value}")
                                ->label('Tagline')
                                ->maxLength(120),
                            Textarea::make("address.{$locale->value}")
                                ->label('Address')
                                ->rows(2)
                                ->maxLength(300),
                            TextInput::make("office_hours.{$locale->value}")
                                ->label('Office hours')
                                ->maxLength(120),
                        ], 'Company details'),
                    ]),

                Section::make('Home page and search engines')
                    ->schema([
                        LocaleTabs::make(fn (Locale $locale): array => [
                            TextInput::make("hero_title.{$locale->value}")
                                ->label('Headline')
                                ->required($locale->isFallback())
                                ->maxLength(120),
                            Textarea::make("hero_subtitle.{$locale->value}")
                                ->label('Introduction')
                                ->rows(3)
                                ->maxLength(400),
                            Textarea::make("default_meta_description.{$locale->value}")
                                ->label('Default meta description')
                                ->helperText('Used by pages without their own description.')
                                ->rows(2)
                                ->maxLength(160),
                        ], 'Home page'),
                    ]),

                Section::make('Contact details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(40),
                        TextInput::make('email')
                            ->label('General email')
                            ->email()
                            ->maxLength(190),
                        TextInput::make('proposals_email')
                            ->label('Proposals email')
                            ->email()
                            ->maxLength(190),
                        TextInput::make('founded_year')
                            ->label('Year founded')
                            ->integer()
                            ->minValue(1950)
                            ->maxValue((int) now()->format('Y')),
                        TextInput::make('map_url')
                            ->label('Map link')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Social links')
                    ->columns(2)
                    ->schema([
                        TextInput::make('linkedin_url')->label('LinkedIn')->url()->maxLength(500),
                        TextInput::make('instagram_url')->label('Instagram')->url()->maxLength(500),
                        TextInput::make('x_url')->label('X')->url()->maxLength(500),
                        TextInput::make('youtube_url')->label('YouTube')->url()->maxLength(500),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Save settings')
                            ->submit('save'),
                    ])->key('form-actions'),
                ]),
        ]);
    }

    public function save(SiteSettings $settings): void
    {
        $settings->save($this->form->getState());

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}

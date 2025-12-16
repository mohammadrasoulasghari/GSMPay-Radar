<?php

namespace App\Filament\Resources\Developers;

use App\Filament\Resources\Developers\Pages\CreateDeveloper;
use App\Filament\Resources\Developers\Pages\EditDeveloper;
use App\Filament\Resources\Developers\Pages\ListDevelopers;
use App\Filament\Resources\Developers\Pages\ViewDeveloper;
use App\Filament\Resources\Developers\RelationManagers\PrReportsRelationManager;
use App\Models\Developer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeveloperResource extends Resource
{
    protected static ?string $model = Developer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'توسعه‌دهندگان';

    protected static ?string $modelLabel = 'توسعه‌دهنده';

    protected static ?string $pluralModelLabel = 'توسعه‌دهندگان';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label(__('developer.username'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('name')
                    ->label(__('developer.name'))
                    ->maxLength(255),
                TextInput::make('avatar_url')
                    ->label(__('developer.avatar_url'))
                    ->url()
                    ->maxLength(500),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? $record->username)),
                TextColumn::make('username')
                    ->label(__('developer.username'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('developer.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pr_reports_count')
                    ->label(__('developer.total_reports'))
                    ->counts('prReports')
                    ->sortable(),
                TextColumn::make('prReports.created_at')
                    ->label(__('developer.last_activity'))
                    ->getStateUsing(fn ($record) => $record->prReports()->latest()->first()?->created_at)
                    ->dateTime('M d, Y')
                    ->sortable(query: function ($query, $direction) {
                        return $query->withMax('prReports', 'created_at')
                            ->orderBy('pr_reports_max_created_at', $direction);
                    }),
                TextColumn::make('created_at')
                    ->label(__('developer.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('پروفایل توسعه‌دهنده')
                    ->schema([
                        TextEntry::make('name')
                            ->label('نام')
                            ->default(fn ($record) => $record->name ?? $record->username)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('username')
                            ->label('نام کاربری GitHub')
                            ->icon('heroicon-m-at-symbol')
                            ->iconColor('gray')
                            ->prefix('@')
                            ->url(fn ($record) => "https://github.com/{$record->username}")
                            ->openUrlInNewTab()
                            ->color('primary'),

                        TextEntry::make('created_at')
                            ->label('عضو از')
                            ->dateTime('F Y')
                            ->icon('heroicon-m-calendar')
                            ->iconColor('gray'),

                        TextEntry::make('pr_reports_count')
                            ->label('تعداد گزارش‌های PR')
                            ->state(fn ($record) => $record->prReports()->count())
                            ->icon('heroicon-m-document-text')
                            ->iconColor('primary'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 4
                    ])
                    ->compact()
                    ->extraAttributes(['class' => 'bg-white dark:bg-gray-900 shadow-sm']),

                Section::make('آمار و تحلیل')
                    ->schema([
                        TextEntry::make('latest_pr')
                            ->label('آخرین PR')
                            ->state(fn ($record) => $record->prReports()->latest()->first()?->created_at?->diffForHumans() ?? 'فعالیتی موجود نیست')
                            ->icon('heroicon-m-clock')
                            ->iconColor('green'),

                        TextEntry::make('avg_tone_score')
                            ->label('میانگین امتیاز لحن')
                            ->state(fn ($record) => $record->getAverageToneScore() ? number_format($record->getAverageToneScore(), 1) : 'محاسبه نشده')
                            ->icon('heroicon-m-chat-bubble-left-ellipsis')
                            ->iconColor('blue'),

                        TextEntry::make('compliance_rate')
                            ->label('نرخ انطباق')
                            ->state(fn ($record) => $record->getAverageComplianceRate() ? number_format($record->getAverageComplianceRate(), 1) . '%' : 'محاسبه نشده')
                            ->icon('heroicon-m-check-badge')
                            ->iconColor('emerald'),

                        TextEntry::make('health_status')
                            ->label('وضعیت سلامت')
                            ->state(fn ($record) => match($record->getOverallHealthStatus()) {
                                'healthy' => 'سالم',
                                'warning' => 'هشدار', 
                                'critical' => 'بحرانی',
                                default => 'نامشخص'
                            })
                            ->icon(fn ($record) => match($record->getOverallHealthStatus()) {
                                'healthy' => 'heroicon-m-heart',
                                'warning' => 'heroicon-m-exclamation-triangle',
                                'critical' => 'heroicon-m-x-circle',
                                default => 'heroicon-m-question-mark-circle'
                            })
                            ->color(fn ($record) => match($record->getOverallHealthStatus()) {
                                'healthy' => 'success',
                                'warning' => 'warning',
                                'critical' => 'danger',
                                default => 'gray'
                            }),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 4
                    ])
                    ->compact()
                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 shadow-sm']),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PrReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevelopers::route('/'),
            'create' => CreateDeveloper::route('/create'),
            'view' => ViewDeveloper::route('/{record}'),
            'edit' => EditDeveloper::route('/{record}/edit'),
        ];
    }
}

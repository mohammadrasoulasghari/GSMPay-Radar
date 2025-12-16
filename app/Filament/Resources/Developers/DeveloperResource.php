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
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
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
                Forms\Components\TextInput::make('username')
                    ->label(__('developer.username'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label(__('developer.name'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('avatar_url')
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
                Section::make('پروفایل')
                    ->schema([
                        ImageEntry::make('avatar_url')
                            ->label('آواتار')
                            ->circular()
                            ->size(100)
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? $record->username) . '&background=6366f1&color=fff&size=200'),

                        TextEntry::make('name')
                            ->label('نام')
                            ->default(fn ($record) => $record->name ?? '-'),

                        TextEntry::make('username')
                            ->label('نام کاربری')
                            ->icon('heroicon-m-at-symbol')
                            ->iconColor('gray')
                            ->url(fn ($record) => "https://github.com/{$record->username}")
                            ->openUrlInNewTab(),

                        TextEntry::make('created_at')
                            ->label('عضو از')
                            ->dateTime('F Y'),
                    ])->columns(1),
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

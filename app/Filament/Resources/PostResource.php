<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\Widgets\post as WidgetsPost;
use App\Filament\Resources\PostResource\Widgets\postStat;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Post Management';

    protected static ?string $navigationBadgeTooltip = 'Number of Posts';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post Creating')->description('Add Post Details')->schema([
                    TextInput::make('title')->string()->required(),
                    TextInput::make('description')->string()->required(),
                    FileUpload::make('image')->directory('posts_images')
                        ->disk('public')
                        ->preserveFilenames()
                        ->image()
                        ->required(),
                ]),
                Section::make('More Details')
                    ->description('Add Categories & User')
                    ->schema([
                        Select::make('user_id')
                            ->label("Owned")
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),

                        Select::make('categories')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('description')->limit(30),
                TextColumn::make('user.name')->label('Owner')->searchable(),
                TextColumn::make('categories.name')->label('Categories'),
                BooleanColumn::make('approved')
                    ->label('Approved')
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Approve')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Post $record) {
                        $record->update(['approved' => true]);
                    })
                    ->successNotificationTitle('Post Approved!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\PostResource\Widgets\PostStat::class,
        ];
    }
}

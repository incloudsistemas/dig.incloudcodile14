<?php

namespace App\Filament\Resources\Cms;

use App\Filament\Resources\Cms\MainPostSliderResource\Pages;
use App\Filament\Resources\Cms\MainPostSliderResource\RelationManagers;
use App\Models\Cms\PostSlider;
use App\Services\Cms\PostSliderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MainPostSliderResource extends Resource
{
    protected static ?string $model = PostSlider::class;

    protected static ?string $service = PostSliderService::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'Slider';

    protected static ?string $pluralModelLabel = 'Sliders';

    protected static ?string $navigationGroup = 'CMS & Marketing';

    protected static ?int $navigationSort = 98;

    protected static ?string $navigationLabel = 'Sliders';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return static::$service::getForm(form: $form);
    }

    public static function table(Table $table): Table
    {
        return static::$service::getTable(table: $table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return static::$service::getInfolist(infolist: $infolist);
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
            'index'  => Pages\ListMainPostSliders::route('/'),
            'create' => Pages\CreateMainPostSlider::route('/create'),
            'edit'   => Pages\EditMainPostSlider::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->bySlideableTypesAndIds(slideableTypes: ['cms_pages',], slideableIds: [1,]); // 1 - index
    }
}

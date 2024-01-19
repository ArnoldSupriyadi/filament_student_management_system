<?php

namespace App\Filament\Resources;

use App\Exports\StudentExports;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = "Academics Management";

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->live()
                    ->relationship(name: 'class', titleAttribute: 'name'),
                Select::make('section_id')
                    ->label('Section')
                    ->options(function (Get $get){
                        $classId = $get('class_id');
                        
                        if($classId) {
                            return Section::where('class_id', $classId)->get()->pluck('name', 'id')->toArray();
                        }
                    }),
                TextInput::make('name')
                    ->autofocus()
                    ->required(),
                TextInput::make('email')
                    ->unique()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('class.name')
                    ->badge()
                    ->searchable(),
                TextColumn::make('section.name')
                    ->badge(),
            ])
            ->filters([
                Filter::make('class-section-filter')
                    ->form([
                        Select::make('class_id')
                            ->label('Filter By Class')
                            ->placeholder('Select a Class')
                            ->options(
                                Classes::pluck('name', 'id')->toArray()
                            ),
                        Select::make('section_id')
                            ->label('Filter By Section')
                            ->placeholder('Select a Section')
                            ->options(function (Get $get) {
                                $classId = $get('class_id');
                                if ($classId) {
                                    return Section::where('class_id', $classId)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['class_id'], function ($query) use ($data) {
                            return $query->where('class_id', $data['class_id']);
                        })->when($data['section_id'], function ($query) use ($data) {
                            return $query->where('section_id', $data['section_id']);
                        });
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('exports')
                        ->label('Export Records')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function(Collection $records){
                           return (new StudentExports($records))
                            ->download('student.xlsx');
                        })
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Companies\Tables\CompaniesTable;
use App\Filament\Resources\Users\UserResource;
use App\Models\Company;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Form;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'company';

    protected static ?string $relatedResource = CompanyResource::class;

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('name')
            // ->columns([
            //     TextColumn::make('name')
            //         ->label('Nombre'),
            // ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DetachAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}

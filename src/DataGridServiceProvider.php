<?php

namespace Eawardie\DataGrid;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DataGridServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('data-grid')
            ->hasRoute('DataGridRoutes')
            ->hasMigration('create_datagrid_table');
    }
}

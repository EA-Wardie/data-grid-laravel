<?php

namespace Eawardie\DataGrid;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DataGridServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('data-grid')
            ->hasRoute('web')
            ->hasMigration('create_datagrid_table');
    }

    public function packageRegistered()
    {
        $this->app->bind('data-grid', function() {
            return new DataGridService();
        });
    }
}

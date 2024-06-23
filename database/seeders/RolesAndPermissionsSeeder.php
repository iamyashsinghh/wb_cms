<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // blog permissions
        Permission::create(['name' => 'create blog']);
        Permission::create(['name' => 'edit blog']);
        Permission::create(['name' => 'publish blog']);
        Permission::create(['name' => 'delete blog']);

        // venue _ vendor permissions
        Permission::create(['name' => 'create venue_vendor']);
        Permission::create(['name' => 'edit venue_vendor']);
        Permission::create(['name' => 'publish venue_vendor']);
        Permission::create(['name' => 'delete venue_vendor']);

        // venue _ vendor listing meta permissions
        Permission::create(['name' => 'create venue_vendor_list']);
        Permission::create(['name' => 'edit venue_vendor_list']);
        Permission::create(['name' => 'publish venue_vendor_list']);
        Permission::create(['name' => 'delete venue_vendor_list']);

        // review permissions
        Permission::create(['name' => 'create review']);
        Permission::create(['name' => 'edit review']);
        Permission::create(['name' => 'publish review']);
        Permission::create(['name' => 'delete review']);

        // page listing meta permissions
        Permission::create(['name' => 'create page_listing_meta']);
        Permission::create(['name' => 'edit page_listing_meta']);
        Permission::create(['name' => 'publish page_listing_meta']);
        Permission::create(['name' => 'delete page_listing_meta']);

        // page listing meta permissions
        Permission::create(['name' => 'create author']);
        Permission::create(['name' => 'edit author']);
        Permission::create(['name' => 'delete author']);

         // create permissions Business Users
         Permission::create(['name' => 'create business users']);
         Permission::create(['name' => 'edit business users']);
         Permission::create(['name' => 'publish business users']);
         Permission::create(['name' => 'delete business users']);


        // create permissions super power
        Permission::create(['name' => 'super power']);

        //// Create roles and assign created permissions

        // Create admin role and assign all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create writer role and assign specific permissions
        $writerRole = Role::create(['name' => 'author']);
        $writerRole->givePermissionTo(['create blog', 'edit blog']);

        // Create seo manager role and assign specific permissions
        $seoManagerRole = Role::create(['name' => 'seomanager']);
        $seoManagerRole->givePermissionTo([
            'create blog',
            'edit blog',
            'publish blog',
            'create author',
            'edit author',
            'create venue_vendor',
            'edit venue_vendor',
            'publish venue_vendor',
            'create venue_vendor_list',
            'edit venue_vendor_list',
            'publish venue_vendor_list',
            'create review',
            'edit review',
            'publish review',
            'create page_listing_meta',
            'edit page_listing_meta',
            'publish page_listing_meta',
            'create business users',
            'edit business users',
            'publish business users',
        ]);

        // Create seo Executive role and assign specific permissions
        $seoExecutiveRole = Role::create(['name' => 'seoxecutive']);
        $seoExecutiveRole->givePermissionTo([
            'create blog',
            'edit blog',
            'edit venue_vendor',
            'edit venue_vendor_list',
            'create page_listing_meta',
            'edit page_listing_meta',
        ]);

         // Create seo Executive role and assign specific permissions
         $seoExecutiveRole = Role::create(['name' => 'listing']);
         $seoExecutiveRole->givePermissionTo([
             'create venue_vendor',
             'edit venue_vendor',
             'create review',
             'edit review',
             'publish review',
             'create business users',
             'edit business users',
             'publish business users',
         ]);
    }
}

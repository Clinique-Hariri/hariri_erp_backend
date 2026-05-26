<?php

namespace App\Providers;

use App\Services\MenuBuilder;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   * @throws BindingResolutionException
   */
  public function boot(): void
  {
//    $this->addSidebarMenuItems();
//    $menuData = json_decode(json_encode([
//      'verticalMenu' => MenuBuilder::get(),
//      'horizontalMenu' => MenuBuilder::get(),
//    ]));
//    $this->app->make('view')->share('menuData', $menuData);
  }


  /**
   * Add sidebar menu items.
   */
  protected function addSidebarMenuItems(): void
  {
    MenuBuilder::add(
      name: 'dashboard',
      slug: 'dashboard',
      route: 'dashboard',
      icon: 'bx bxs-home-circle',
    );

    MenuBuilder::header('general-management');
    MenuBuilder::add(
      name: 'ads',
      slug: 'ads',
      route: 'ads.index',
      icon: 'bx bxs-tag',
      permission: ['manage_ads'],
    );

    MenuBuilder::header('clinic-management');
    MenuBuilder::add(
      name: 'patients',
      slug: 'patients',
      route: 'patients.index',
      icon: 'bx bxs-user-detail',
      permission: ['manage_patients'],
    );
    MenuBuilder::add(
      name: 'appointments',
      slug: 'appointments',
      route: 'appointments.index',
      icon: 'bx bxs-calendar-check',
      permission: ['manage_appointments'],
    );
    MenuBuilder::add(
      name: 'doctors',
      slug: 'doctors',
      route: 'doctors.index',
      icon: 'bx bxs-user-voice',
      permission: ['manage_doctors'],
    );
    MenuBuilder::add(
      name: 'specialities',
      slug: 'specialities',
      route: 'specialities.index',
      icon: 'bx bxs-briefcase',
      permission: ['manage_specialities'],
    );
    MenuBuilder::add(
      name: 'departments',
      slug: 'departments',
      route: 'departments.index',
      icon: 'bx bxs-building',
      permission: ['manage_departments'],
    );
    MenuBuilder::add(
      name: 'services',
      slug: 'services',
      route: 'services.index',
      icon: 'bx bxs-first-aid',
      permission: ['manage_services']
    );

    MenuBuilder::header('pharmacy-management');
    MenuBuilder::add(
      name: 'coupons',
      slug: 'coupons',
      route: 'coupons.index',
      icon: 'bx bxs-coupon',
      permission: ['manage_coupons'],
    );
    MenuBuilder::add(
      name: 'categories',
      slug: 'categories',
      route: 'categories.index',
      icon: 'bx bxs-category',
      permission: ['manage_categories'],
    );
    MenuBuilder::add(
      name: 'units',
      slug: 'units',
      route: 'units.index',
      icon: 'bx bxs-ruler',
      permission: ['manage_products'],
    );
    MenuBuilder::add(
      name: 'products',
      slug: 'products',
      route: 'products.index',
      icon: 'bx bxs-capsule',
      permission: ['manage_products'],
    );
    MenuBuilder::add(
      name: 'drivers',
      slug: 'drivers',
      route: 'drivers.index',
      icon: 'bx bxs-truck',
      permission: ['manage_orders'],
    );
    MenuBuilder::add(
      name: 'orders',
      slug: 'orders',
      route: 'orders.index',
      icon: 'bx bxs-spreadsheet',
      permission: ['manage_orders'],
    );

    MenuBuilder::add(
      name: 'suppliers',
      slug: 'suppliers',
      route: 'suppliers.index',
      icon: 'bx bxs-factory',
      permission: ['manage_purchases'],
    );

    MenuBuilder::add(
      name: 'purchases',
      slug: 'purchases',
      route: 'purchases.index',
      icon: 'bx bxs-file',
      permission: ['manage_purchases'],
    );

  }
}

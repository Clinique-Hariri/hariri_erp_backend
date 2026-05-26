<?php
namespace App\Support\Enum;
class UserRoles
{
  const SUPER_ADMIN = 'super_admin';
  const ADMIN = 'admin';
  const RECEPTIONIST = 'receptionist';
  const DOCTOR = 'doctor';
  const RADIOLOGY_MANAGER = 'radiology_manager';
  const LABORATORY_MANAGER = 'laboratory_manager';
  const OPERATIONS_MANAGER = 'operations_manager';
  const ACCOUNTANT = 'accountant';
  const HUMAN_RESOURCES = 'human_resources';
  const INVENTORY_MANAGER = 'inventory_manager';
  const INSURANCE_SOCIETY_MANAGER = 'insurance_society_manager';

  public static function all($translated = false):array
  {
    return [
      self::SUPER_ADMIN => $translated ? __('user.roles.super_admin') : self::SUPER_ADMIN,
      self::ADMIN => $translated ? __('user.roles.admin') : self::ADMIN,
      self::RECEPTIONIST => $translated ? __('user.roles.receptionist') : self::RECEPTIONIST,
      self::DOCTOR => $translated ? __('user.roles.doctor') : self::DOCTOR,
      self::RADIOLOGY_MANAGER => $translated ? __('user.roles.radiology_manager') : self::RADIOLOGY_MANAGER,
      self::LABORATORY_MANAGER => $translated ? __('user.roles.laboratory_manager') : self::LABORATORY_MANAGER,
      self::OPERATIONS_MANAGER => $translated ? __('user.roles.operations_manager') : self::OPERATIONS_MANAGER,
      self::ACCOUNTANT => $translated ? __('user.roles.accountant') : self::ACCOUNTANT,
      self::HUMAN_RESOURCES => $translated ? __('user.roles.human_resources') : self::HUMAN_RESOURCES,
      self::INVENTORY_MANAGER => $translated ? __('user.roles.inventory_manager') : self::INVENTORY_MANAGER,
      self::INSURANCE_SOCIETY_MANAGER => $translated ? __('user.roles.insurance_society_manager') : self::INSURANCE_SOCIETY_MANAGER,
    ];
  }

  public static function get_name(string $role):string
  {
    return self::all(true)[$role];
  }
}

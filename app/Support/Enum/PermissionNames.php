<?php
namespace App\Support\Enum;

class PermissionNames
{
  //roles
  const string ROLES_VIEW = 'roles.view';
  const string ROLES_CREATE = 'roles.create';
  const string ROLES_UPDATE = 'roles.update';
  const string ROLES_DELETE = 'roles.delete';

  //permissions
  const string PERMISSIONS_VIEW = 'permissions.view';
  const string PERMISSIONS_UPDATE = 'permissions.update';

  //users
  const string USERS_VIEW = 'users.view';
  const string USERS_CREATE = 'users.create';
  const string USERS_UPDATE = 'users.update';
  const string USERS_DELETE = 'users.delete';

  //settings
  const string SETTINGS_VIEW = 'settings.view';
  const string SETTINGS_UPDATE = 'settings.update';

  //departments
  const string DEPARTMENTS_VIEW = 'departments.view';
  const string DEPARTMENTS_CREATE = 'departments.create';
  const string DEPARTMENTS_UPDATE = 'departments.update';
  const string DEPARTMENTS_DELETE = 'departments.delete';

  //specialities
  const string SPECIALITIES_VIEW = 'specialities.view';
  const string SPECIALITIES_CREATE = 'specialities.create';
  const string SPECIALITIES_UPDATE = 'specialities.update';
  const string SPECIALITIES_DELETE = 'specialities.delete';

  //doctors
  const string DOCTORS_VIEW = 'doctors.view';
  const string DOCTORS_CREATE = 'doctors.create';
  const string DOCTORS_UPDATE = 'doctors.update';
  const string DOCTORS_DELETE = 'doctors.delete';

  //patients
  const string PATIENTS_VIEW = 'patients.view';
  const string PATIENTS_CREATE = 'patients.create';
  const string PATIENTS_UPDATE = 'patients.update';
  const string PATIENTS_DELETE = 'patients.delete';

  //checkups
  const string CHECKUPS_VIEW = 'checkups.view';
  const string CHECKUPS_CREATE = 'checkups.create';
  const string CHECKUPS_UPDATE = 'checkups.update';
  const string CHECKUPS_DELETE = 'checkups.delete';
  //checkups for doctor
  const string CHECKUPS_DOCTOR_VIEW = 'checkups.doctor.view';
  const string CHECKUPS_DOCTOR_UPDATE = 'checkups.doctor.update';
  const string CHECKUPS_VITAL_SIGNS_VIEW = 'checkups.vital_signs.view';
  const string CHECKUPS_VITAL_SIGNS_UPDATE = 'checkups.vital_signs.update';

  // checkups statuses
  public const string CHECKUPS_UPDATE_TO_PENDING = 'checkups.update.to_pending';
  public const string CHECKUPS_UPDATE_TO_IN_CONSULTATION = 'checkups.update.to_in_consultation';
  public const string CHECKUPS_UPDATE_TO_COMPLETED = 'checkups.update.to_completed';

  // checkup services
  public const string CHECKUP_SERVICES_VIEW = 'checkup_services.view';
  public const string CHECKUP_SERVICES_CREATE = 'checkup_services.create';
  public const string CHECKUP_SERVICES_UPDATE = 'checkup_services.update';
  public const string CHECKUP_SERVICES_DELETE = 'checkup_services.delete';

  // checkup services statuses
  public const string CHECKUP_SERVICES_UPDATE_TO_PENDING = 'checkup_services.update.to_pending';
  public const string CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS = 'checkup_services.update.to_in_progress';
  public const string CHECKUP_SERVICES_UPDATE_TO_COMPLETED = 'checkup_services.update.to_completed';
  public const string ANALYSES_SEND_RESULT_NOTIFICATION = 'analyses.send_result_notification';

  public const string CHECKUP_RADIOLIGY_VIEW = 'checkup_radiology.view';
  public const string CHECKUP_RADIOLIGY_CREATE = 'checkup_radiology.create';
  public const string CHECKUP_RADIOLIGY_UPDATE = 'checkup_radiology.update';
  public const string CHECKUP_RADIOLIGY_DELETE = 'checkup_radiology.delete';

  // checkup radiology statuses
  public const string CHECKUP_RADIOLIGY_UPDATE_TO_PENDING = 'checkup_radiology.update.to_pending';
  public const string CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS = 'checkup_radiology.update.to_in_progress';
  public const string CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED = 'checkup_radiology.update.to_completed';

  //hospitalizations
  const string HOSPITALIZATIONS_VIEW = 'hospitalizations.view';
  const string HOSPITALIZATIONS_CREATE = 'hospitalizations.create';
  const string HOSPITALIZATIONS_UPDATE = 'hospitalizations.update';
  const string HOSPITALIZATIONS_DELETE = 'hospitalizations.delete';

  // hospitalization statuses
  public const string HOSPITALIZATIONS_UPDATE_TO_ACCEPTED = 'hospitalizations.update.to_accepted';
  public const string HOSPITALIZATIONS_UPDATE_TO_ADMITTED = 'hospitalizations.update.to_admitted';
  public const string HOSPITALIZATIONS_UPDATE_TO_DISCHARGED = 'hospitalizations.update.to_discharged';

  // Add these constants after the existing permissions
  const string OPERATIONS_VIEW = 'operations.view';
  const string OPERATIONS_CREATE = 'operations.create';
  const string OPERATIONS_UPDATE = 'operations.update';
  const string OPERATIONS_DELETE = 'operations.delete';

  // Add these status update constants
  public const string OPERATIONS_UPDATE_TO_PENDING = 'operations.update.to_pending';
  public const string OPERATIONS_UPDATE_TO_SCHEDULED = 'operations.update.to_scheduled';
  public const string OPERATIONS_UPDATE_TO_COMPLETED = 'operations.update.to_completed';

  //medical services
  const string MEDICAL_SERVICES_VIEW = 'medical_services.view';
  const string MEDICAL_SERVICES_CREATE = 'medical_services.create';
  const string MEDICAL_SERVICES_UPDATE = 'medical_services.update';
  const string MEDICAL_SERVICES_DELETE = 'medical_services.delete';

  const string MEDICINES_VIEW = 'medicines.view';
  const string MEDICINES_CREATE = 'medicines.create';
  const string MEDICINES_UPDATE = 'medicines.update';
  const string MEDICINES_DELETE = 'medicines.delete';

  const string CHRONIC_DISEASES_VIEW = 'chronic_diseases.view';
  const string CHRONIC_DISEASES_CREATE = 'chronic_diseases.create';
  const string CHRONIC_DISEASES_UPDATE = 'chronic_diseases.update';
  const string CHRONIC_DISEASES_DELETE = 'chronic_diseases.delete';
  const string CHRONIC_DISEASES_PATIENT_VIEW = 'chronic_diseases_patient.view';
  const string CHRONIC_DISEASES_PATIENT_CREATE = 'chronic_diseases_patient.create';
  const string CHRONIC_DISEASES_PATIENT_UPDATE = 'chronic_diseases_patient.update';
  const string CHRONIC_DISEASES_PATIENT_DELETE = 'chronic_diseases_patient.delete';

  const string PRESCRIPTIONS_VIEW = 'prescriptions.view';
  const string PRESCRIPTIONS_CREATE = 'prescriptions.create';
  const string PRESCRIPTIONS_UPDATE = 'prescriptions.update';
  const string PRESCRIPTIONS_DELETE = 'prescriptions.delete';

  //suppliers
  const string SUPPLIERS_VIEW = 'suppliers.view';
  const string SUPPLIERS_CREATE = 'suppliers.create';
  const string SUPPLIERS_UPDATE = 'suppliers.update';
  const string SUPPLIERS_DELETE = 'suppliers.delete';

  //itemscategory // inventory
  const string ITEMSCATEGORY_VIEW = 'items_category.view';
  const string ITEMSCATEGORY_CREATE = 'items_category.create';
  const string ITEMSCATEGORY_UPDATE = 'items_category.update';
  const string ITEMSCATEGORY_DELETE = 'items_category.delete';

  //supply_request // inventory
  const string SUPPLYREQUEST_VIEW = 'supply_request.view';
  const string SUPPLYREQUEST_CREATE = 'supply_request.create';
  const string SUPPLYREQUEST_UPDATE = 'supply_request.update';
  const string SUPPLYREQUEST_DELETE = 'supply_request.delete';

  //inventory_items // inventory
  const string INVENTORYITEM_VIEW = 'inventory_items.view';
  const string INVENTORYITEM_CREATE = 'inventory_items.create';
  const string INVENTORYITEM_UPDATE = 'inventory_items.update';
  const string INVENTORYITEM_DELETE = 'inventory_items.delete';

  //inventory_transactions // inventory
  const string INVENTORYTRANSACTIONS_VIEW = 'inventory_transactions.view';
  const string INVENTORYTRANSACTIONS_CREATE = 'inventory_transactions.create';
  const string INVENTORYTRANSACTIONS_UPDATE = 'inventory_transactions.update';
  const string INVENTORYTRANSACTIONS_DELETE = 'inventory_transactions.delete';

  //insurance societies
  const string INSURANCE_SOCIETIES_VIEW = 'insurance_societies.view';
  const string INSURANCE_SOCIETIES_CREATE = 'insurance_societies.create';
  const string INSURANCE_SOCIETIES_UPDATE = 'insurance_societies.update';
  const string INSURANCE_SOCIETIES_DELETE = 'insurance_societies.delete';
  const string ASSURANCE_VIEW = 'assurance.view';


  //designations
  const string DESIGNATIONS_VIEW = 'designations.view';
  const string DESIGNATIONS_CREATE = 'designations.create';
  const string DESIGNATIONS_UPDATE = 'designations.update';
  const string DESIGNATIONS_DELETE = 'designations.delete';

  //bonuses
  const string BONUSES_VIEW = 'bonuses.view';
  const string BONUSES_CREATE = 'bonuses.create';
  const string BONUSES_UPDATE = 'bonuses.update';
  const string BONUSES_DELETE = 'bonuses.delete';

  //employees
  const string EMPLOYEES_VIEW = 'employees.view';
  const string EMPLOYEES_CREATE = 'employees.create';
  const string EMPLOYEES_UPDATE = 'employees.update';
  const string EMPLOYEES_DELETE = 'employees.delete';

  //contracts
  const string CONTRACTS_VIEW = 'contracts.view';
  const string CONTRACTS_CREATE = 'contracts.create';
  const string CONTRACTS_UPDATE = 'contracts.update';
  const string CONTRACTS_DELETE = 'contracts.delete';


  //attendances
  const string ATTENDANCES_VIEW = 'attendances.view';
  const string ATTENDANCES_CREATE = 'attendances.create';
  const string ATTENDANCES_UPDATE = 'attendances.update';
  const string ATTENDANCES_DELETE = 'attendances.delete';

  //loans
  const string LOANS_VIEW = 'loans.view';
  const string LOANS_CREATE = 'loans.create';
  const string LOANS_UPDATE = 'loans.update';
  const string LOANS_DELETE = 'loans.delete';

  //salaries
  const string SALARIES_VIEW = 'salaries.view';
  const string SALARIES_CREATE = 'salaries.create';
  const string SALARIES_UPDATE = 'salaries.update';
  const string SALARIES_DELETE = 'salaries.delete';

  // salaries statuses
  public const string SALARIES_UPDATE_TO_PROCESSED = 'salaries.update.to_processed';
  public const string SALARIES_UPDATE_TO_PAID = 'salaries.update.to_paid';

  //career changes
  const string CAREER_CHANGES_VIEW = 'career_changes.view';
  const string CAREER_CHANGES_CREATE = 'career_changes.create';
  const string CAREER_CHANGES_UPDATE = 'career_changes.update';
  const string CAREER_CHANGES_DELETE = 'career_changes.delete';

  //transactions
  const string TRANSACTIONS_VIEW = 'transactions.view';
  const string TRANSACTIONS_CREATE = 'transactions.create';
  const string TRANSACTIONS_UPDATE = 'transactions.update';
  const string TRANSACTIONS_DELETE = 'transactions.delete';

  const string ACCOUNTING_GENERAL_VIEW = 'accounting.general_view';
  const string ACCOUNTING_DOCTORS_VIEW = 'accounting.doctors_view';
  const string ACCOUNTING_CREATE = 'accounting.create';
  const string ACCOUNTING_UPDATE = 'accounting.update';
  const string ACCOUNTING_PRINT_A_REPORT = 'accounting.print_a_report';
  const string ACCOUNTING_PRINT_A_DOCTOR_REPORT = 'accounting.print_a_doctor_report';
  const string ACCOUNTING_VIEW_DOCTORS_INCOME = 'accounting.view_doctors_income';
  const string ACCOUNTING_PAYMENT_TO_THE_DOCTOR = 'accounting.payment_to_the_doctor';
  public static function all($lang = null):array
  {
    return [
      self::ROLES_VIEW                  => $lang ? __('permissions.'.self::ROLES_VIEW) : self::ROLES_VIEW,
      self::ROLES_CREATE                => $lang ? __('permissions.'.self::ROLES_CREATE) : self::ROLES_CREATE,
      self::ROLES_UPDATE                => $lang ? __('permissions.'.self::ROLES_UPDATE) : self::ROLES_UPDATE,
      self::ROLES_DELETE                => $lang ? __('permissions.'.self::ROLES_DELETE) : self::ROLES_DELETE,

      self::PERMISSIONS_VIEW            => $lang ? __('permissions.'.self::PERMISSIONS_VIEW) : self::PERMISSIONS_VIEW,
      self::PERMISSIONS_UPDATE          => $lang ? __('permissions.'.self::PERMISSIONS_UPDATE) : self::PERMISSIONS_UPDATE,

      self::USERS_VIEW                  => $lang ? __('permissions.'.self::USERS_VIEW) : self::USERS_VIEW,
      self::USERS_CREATE                => $lang ? __('permissions.'.self::USERS_CREATE) : self::USERS_CREATE,
      self::USERS_UPDATE                => $lang ? __('permissions.'.self::USERS_UPDATE) : self::USERS_UPDATE,
      self::USERS_DELETE                => $lang ? __('permissions.'.self::USERS_DELETE) : self::USERS_DELETE,

      self::SETTINGS_VIEW               => $lang ? __('permissions.'.self::SETTINGS_VIEW) : self::SETTINGS_VIEW,
      self::SETTINGS_UPDATE             => $lang ? __('permissions.'.self::SETTINGS_UPDATE) : self::SETTINGS_UPDATE,

      self::DEPARTMENTS_VIEW            => $lang ? __('permissions.'.self::DEPARTMENTS_VIEW) : self::DEPARTMENTS_VIEW,
      self::DEPARTMENTS_CREATE          => $lang ? __('permissions.'.self::DEPARTMENTS_CREATE) : self::DEPARTMENTS_CREATE,
      self::DEPARTMENTS_UPDATE          => $lang ? __('permissions.'.self::DEPARTMENTS_UPDATE) : self::DEPARTMENTS_UPDATE,
      self::DEPARTMENTS_DELETE          => $lang ? __('permissions.'.self::DEPARTMENTS_DELETE) : self::DEPARTMENTS_DELETE,

      self::SPECIALITIES_VIEW           => $lang ? __('permissions.'.self::SPECIALITIES_VIEW) : self::SPECIALITIES_VIEW,
      self::SPECIALITIES_CREATE         => $lang ? __('permissions.'.self::SPECIALITIES_CREATE) : self::SPECIALITIES_CREATE,
      self::SPECIALITIES_UPDATE         => $lang ? __('permissions.'.self::SPECIALITIES_UPDATE) : self::SPECIALITIES_UPDATE,
      self::SPECIALITIES_DELETE         => $lang ? __('permissions.'.self::SPECIALITIES_DELETE) : self::SPECIALITIES_DELETE,

      self::DOCTORS_VIEW                => $lang ? __('permissions.'.self::DOCTORS_VIEW) : self::DOCTORS_VIEW,
      self::DOCTORS_CREATE              => $lang ? __('permissions.'.self::DOCTORS_CREATE) : self::DOCTORS_CREATE,
      self::DOCTORS_UPDATE              => $lang ? __('permissions.'.self::DOCTORS_UPDATE) : self::DOCTORS_UPDATE,
      self::DOCTORS_DELETE              => $lang ? __('permissions.'.self::DOCTORS_DELETE) : self::DOCTORS_DELETE,

      self::PATIENTS_VIEW               => $lang ? __('permissions.'.self::PATIENTS_VIEW) : self::PATIENTS_VIEW,
      self::PATIENTS_CREATE             => $lang ? __('permissions.'.self::PATIENTS_CREATE) : self::PATIENTS_CREATE,
      self::PATIENTS_UPDATE             => $lang ? __('permissions.'.self::PATIENTS_UPDATE) : self::PATIENTS_UPDATE,
      self::PATIENTS_DELETE             => $lang ? __('permissions.'.self::PATIENTS_DELETE) : self::PATIENTS_DELETE,

      self::CHECKUPS_VIEW               => $lang ? __('permissions.'.self::CHECKUPS_VIEW) : self::CHECKUPS_VIEW,
      self::CHECKUPS_CREATE             => $lang ? __('permissions.'.self::CHECKUPS_CREATE) : self::CHECKUPS_CREATE,
      self::CHECKUPS_UPDATE             => $lang ? __('permissions.'.self::CHECKUPS_UPDATE) : self::CHECKUPS_UPDATE,
      self::CHECKUPS_DELETE             => $lang ? __('permissions.'.self::CHECKUPS_DELETE) : self::CHECKUPS_DELETE,

      self::CHECKUPS_DOCTOR_VIEW        => $lang ? __('permissions.'.self::CHECKUPS_DOCTOR_VIEW) : self::CHECKUPS_DOCTOR_VIEW,
      self::CHECKUPS_DOCTOR_UPDATE      => $lang ? __('permissions.'.self::CHECKUPS_DOCTOR_UPDATE) : self::CHECKUPS_DOCTOR_UPDATE,

      self::CHECKUPS_VITAL_SIGNS_VIEW    => $lang ? __('permissions.'.self::CHECKUPS_VITAL_SIGNS_VIEW) : self::CHECKUPS_VITAL_SIGNS_VIEW,
      self::CHECKUPS_VITAL_SIGNS_UPDATE  => $lang ? __('permissions.'.self::CHECKUPS_VITAL_SIGNS_UPDATE) : self::CHECKUPS_VITAL_SIGNS_UPDATE,

      self::CHECKUPS_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_PENDING) : self::CHECKUPS_UPDATE_TO_PENDING,
      self::CHECKUPS_UPDATE_TO_IN_CONSULTATION  => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_IN_CONSULTATION) : self::CHECKUPS_UPDATE_TO_IN_CONSULTATION,
      self::CHECKUPS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_COMPLETED) : self::CHECKUPS_UPDATE_TO_COMPLETED,

      self::CHECKUP_SERVICES_VIEW       => $lang ? __('permissions.'.self::CHECKUP_SERVICES_VIEW) : self::CHECKUP_SERVICES_VIEW,
      self::CHECKUP_SERVICES_CREATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_CREATE) : self::CHECKUP_SERVICES_CREATE,
      self::CHECKUP_SERVICES_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE) : self::CHECKUP_SERVICES_UPDATE,
      self::CHECKUP_SERVICES_DELETE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_DELETE) : self::CHECKUP_SERVICES_DELETE,

      self::CHECKUP_SERVICES_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_PENDING) : self::CHECKUP_SERVICES_UPDATE_TO_PENDING,
      self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED) : self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED,
      self::ANALYSES_SEND_RESULT_NOTIFICATION       => $lang ? __('permissions.'.self::ANALYSES_SEND_RESULT_NOTIFICATION) : self::ANALYSES_SEND_RESULT_NOTIFICATION,

      self::CHECKUP_RADIOLIGY_VIEW       => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_VIEW) : self::CHECKUP_RADIOLIGY_VIEW,
      self::CHECKUP_RADIOLIGY_CREATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_CREATE) : self::CHECKUP_RADIOLIGY_CREATE,
      self::CHECKUP_RADIOLIGY_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE) : self::CHECKUP_RADIOLIGY_UPDATE,
      self::CHECKUP_RADIOLIGY_DELETE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_DELETE) : self::CHECKUP_RADIOLIGY_DELETE,

      self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING) : self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED) : self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED,

      self::HOSPITALIZATIONS_VIEW              => $lang ? __('permissions.'.self::HOSPITALIZATIONS_VIEW) : self::HOSPITALIZATIONS_VIEW,
      self::HOSPITALIZATIONS_CREATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_CREATE) : self::HOSPITALIZATIONS_CREATE,
      self::HOSPITALIZATIONS_UPDATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE) : self::HOSPITALIZATIONS_UPDATE,
      self::HOSPITALIZATIONS_DELETE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_DELETE) : self::HOSPITALIZATIONS_DELETE,

      self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED          => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED) : self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED,
      self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED         => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED) : self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED,
      self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED       => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED) : self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED,

      self::OPERATIONS_VIEW              => $lang ? __('permissions.'.self::OPERATIONS_VIEW) : self::OPERATIONS_VIEW,
      self::OPERATIONS_CREATE            => $lang ? __('permissions.'.self::OPERATIONS_CREATE) : self::OPERATIONS_CREATE,
      self::OPERATIONS_UPDATE            => $lang ? __('permissions.'.self::OPERATIONS_UPDATE) : self::OPERATIONS_UPDATE,
      self::OPERATIONS_DELETE            => $lang ? __('permissions.'.self::OPERATIONS_DELETE) : self::OPERATIONS_DELETE,

      self::OPERATIONS_UPDATE_TO_PENDING        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_PENDING) : self::OPERATIONS_UPDATE_TO_PENDING,
      self::OPERATIONS_UPDATE_TO_SCHEDULED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_SCHEDULED) : self::OPERATIONS_UPDATE_TO_SCHEDULED,
      self::OPERATIONS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_COMPLETED) : self::OPERATIONS_UPDATE_TO_COMPLETED,

      self::MEDICAL_SERVICES_VIEW       => $lang ? __('permissions.'.self::MEDICAL_SERVICES_VIEW) : self::MEDICAL_SERVICES_VIEW,
      self::MEDICAL_SERVICES_CREATE     => $lang ? __('permissions.'.self::MEDICAL_SERVICES_CREATE) : self::MEDICAL_SERVICES_CREATE,
      self::MEDICAL_SERVICES_UPDATE     => $lang ? __('permissions.'.self::MEDICAL_SERVICES_UPDATE) : self::MEDICAL_SERVICES_UPDATE,
      self::MEDICAL_SERVICES_DELETE     => $lang ? __('permissions.'.self::MEDICAL_SERVICES_DELETE) : self::MEDICAL_SERVICES_DELETE,

      self::MEDICINES_VIEW              => $lang ? __('permissions.'.self::MEDICINES_VIEW) : self::MEDICINES_VIEW,
      self::MEDICINES_CREATE            => $lang ? __('permissions.'.self::MEDICINES_CREATE) : self::MEDICINES_CREATE,
      self::MEDICINES_UPDATE            => $lang ? __('permissions.'.self::MEDICINES_UPDATE) : self::MEDICINES_UPDATE,
      self::MEDICINES_DELETE            => $lang ? __('permissions.'.self::MEDICINES_DELETE) : self::MEDICINES_DELETE,

      self::CHRONIC_DISEASES_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_VIEW) : self::CHRONIC_DISEASES_VIEW,
      self::CHRONIC_DISEASES_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_CREATE) : self::CHRONIC_DISEASES_CREATE,
      self::CHRONIC_DISEASES_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_UPDATE) : self::CHRONIC_DISEASES_UPDATE,
      self::CHRONIC_DISEASES_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_DELETE) : self::CHRONIC_DISEASES_DELETE,

      self::CHRONIC_DISEASES_PATIENT_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_VIEW) : self::CHRONIC_DISEASES_PATIENT_VIEW,
      self::CHRONIC_DISEASES_PATIENT_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_CREATE) : self::CHRONIC_DISEASES_PATIENT_CREATE,
      self::CHRONIC_DISEASES_PATIENT_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_UPDATE) : self::CHRONIC_DISEASES_PATIENT_UPDATE,
      self::CHRONIC_DISEASES_PATIENT_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_DELETE) : self::CHRONIC_DISEASES_PATIENT_DELETE,

      self::PRESCRIPTIONS_VIEW          => $lang ? __('permissions.'.self::PRESCRIPTIONS_VIEW) : self::PRESCRIPTIONS_VIEW,
      self::PRESCRIPTIONS_CREATE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_CREATE) : self::PRESCRIPTIONS_CREATE,
      self::PRESCRIPTIONS_UPDATE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_UPDATE) : self::PRESCRIPTIONS_UPDATE,
      self::PRESCRIPTIONS_DELETE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_DELETE) : self::PRESCRIPTIONS_DELETE,

      self::SUPPLIERS_VIEW              => $lang ? __('permissions.'.self::SUPPLIERS_VIEW) : self::SUPPLIERS_VIEW,
      self::SUPPLIERS_CREATE            => $lang ? __('permissions.'.self::SUPPLIERS_CREATE) : self::SUPPLIERS_CREATE,
      self::SUPPLIERS_UPDATE            => $lang ? __('permissions.'.self::SUPPLIERS_UPDATE) : self::SUPPLIERS_UPDATE,
      self::SUPPLIERS_DELETE            => $lang ? __('permissions.'.self::SUPPLIERS_DELETE) : self::SUPPLIERS_DELETE,

      self::ITEMSCATEGORY_VIEW          => $lang ? __('permissions.'.self::ITEMSCATEGORY_VIEW) : self::ITEMSCATEGORY_VIEW,
      self::ITEMSCATEGORY_CREATE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_CREATE) : self::ITEMSCATEGORY_CREATE,
      self::ITEMSCATEGORY_UPDATE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_UPDATE) : self::ITEMSCATEGORY_UPDATE,
      self::ITEMSCATEGORY_DELETE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_DELETE) : self::ITEMSCATEGORY_DELETE,

      self::INSURANCE_SOCIETIES_VIEW    => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_VIEW) : self::INSURANCE_SOCIETIES_VIEW,
      self::INSURANCE_SOCIETIES_CREATE  => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_CREATE) : self::INSURANCE_SOCIETIES_CREATE,
      self::INSURANCE_SOCIETIES_UPDATE  => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_UPDATE) : self::INSURANCE_SOCIETIES_UPDATE,
      self::INSURANCE_SOCIETIES_DELETE  => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_DELETE) : self::INSURANCE_SOCIETIES_DELETE,
      self::ASSURANCE_VIEW              => $lang ? __('permissions.'.self::ASSURANCE_VIEW) : self::ASSURANCE_VIEW,

      self::SUPPLYREQUEST_VIEW          => $lang ? __('permissions.'.self::SUPPLYREQUEST_VIEW) : self::SUPPLYREQUEST_VIEW,
      self::SUPPLYREQUEST_CREATE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_CREATE) : self::SUPPLYREQUEST_CREATE,
      self::SUPPLYREQUEST_UPDATE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_UPDATE) : self::SUPPLYREQUEST_UPDATE,
      self::SUPPLYREQUEST_DELETE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_DELETE) : self::SUPPLYREQUEST_DELETE,

      self::INVENTORYITEM_VIEW          => $lang ? __('permissions.'.self::INVENTORYITEM_VIEW) : self::INVENTORYITEM_VIEW,
      self::INVENTORYITEM_CREATE        => $lang ? __('permissions.'.self::INVENTORYITEM_CREATE) : self::INVENTORYITEM_CREATE,
      self::INVENTORYITEM_UPDATE        => $lang ? __('permissions.'.self::INVENTORYITEM_UPDATE) : self::INVENTORYITEM_UPDATE,
      self::INVENTORYITEM_DELETE        => $lang ? __('permissions.'.self::INVENTORYITEM_DELETE) : self::INVENTORYITEM_DELETE,

      self::INVENTORYTRANSACTIONS_VIEW   => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_VIEW) : self::INVENTORYTRANSACTIONS_VIEW,
      self::INVENTORYTRANSACTIONS_CREATE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_CREATE) : self::INVENTORYTRANSACTIONS_CREATE,
      self::INVENTORYTRANSACTIONS_UPDATE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_UPDATE) : self::INVENTORYTRANSACTIONS_UPDATE,
      self::INVENTORYTRANSACTIONS_DELETE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_DELETE) : self::INVENTORYTRANSACTIONS_DELETE,

      self::DESIGNATIONS_VIEW           => $lang ? __('permissions.'.self::DESIGNATIONS_VIEW) : self::DESIGNATIONS_VIEW,
      self::DESIGNATIONS_CREATE         => $lang ? __('permissions.'.self::DESIGNATIONS_CREATE) : self::DESIGNATIONS_CREATE,
      self::DESIGNATIONS_UPDATE         => $lang ? __('permissions.'.self::DESIGNATIONS_UPDATE) : self::DESIGNATIONS_UPDATE,
      self::DESIGNATIONS_DELETE         => $lang ? __('permissions.'.self::DESIGNATIONS_DELETE) : self::DESIGNATIONS_DELETE,

      self::BONUSES_VIEW                => $lang ? __('permissions.'.self::BONUSES_VIEW) : self::BONUSES_VIEW,
      self::BONUSES_CREATE              => $lang ? __('permissions.'.self::BONUSES_CREATE) : self::BONUSES_CREATE,
      self::BONUSES_UPDATE              => $lang ? __('permissions.'.self::BONUSES_UPDATE) : self::BONUSES_UPDATE,
      self::BONUSES_DELETE              => $lang ? __('permissions.'.self::BONUSES_DELETE) : self::BONUSES_DELETE,

      self::EMPLOYEES_VIEW              => $lang ? __('permissions.'.self::EMPLOYEES_VIEW) : self::EMPLOYEES_VIEW,
      self::EMPLOYEES_CREATE            => $lang ? __('permissions.'.self::EMPLOYEES_CREATE) : self::EMPLOYEES_CREATE,
      self::EMPLOYEES_UPDATE            => $lang ? __('permissions.'.self::EMPLOYEES_UPDATE) : self::EMPLOYEES_UPDATE,
      self::EMPLOYEES_DELETE            => $lang ? __('permissions.'.self::EMPLOYEES_DELETE) : self::EMPLOYEES_DELETE,

      self::CONTRACTS_VIEW              => $lang ? __('permissions.'.self::CONTRACTS_VIEW) : self::CONTRACTS_VIEW,
      self::CONTRACTS_CREATE            => $lang ? __('permissions.'.self::CONTRACTS_CREATE) : self::CONTRACTS_CREATE,
      self::CONTRACTS_UPDATE            => $lang ? __('permissions.'.self::CONTRACTS_UPDATE) : self::CONTRACTS_UPDATE,
      self::CONTRACTS_DELETE            => $lang ? __('permissions.'.self::CONTRACTS_DELETE) : self::CONTRACTS_DELETE,

      self::ATTENDANCES_VIEW            => $lang ? __('permissions.'.self::ATTENDANCES_VIEW) : self::ATTENDANCES_VIEW,
      self::ATTENDANCES_CREATE          => $lang ? __('permissions.'.self::ATTENDANCES_CREATE) : self::ATTENDANCES_CREATE,
      self::ATTENDANCES_UPDATE          => $lang ? __('permissions.'.self::ATTENDANCES_UPDATE) : self::ATTENDANCES_UPDATE,
      self::ATTENDANCES_DELETE          => $lang ? __('permissions.'.self::ATTENDANCES_DELETE) : self::ATTENDANCES_DELETE,

      self::LOANS_VIEW                  => $lang ? __('permissions.'.self::LOANS_VIEW) : self::LOANS_VIEW,
      self::LOANS_CREATE                => $lang ? __('permissions.'.self::LOANS_CREATE) : self::LOANS_CREATE,
      self::LOANS_UPDATE                => $lang ? __('permissions.'.self::LOANS_UPDATE) : self::LOANS_UPDATE,
      self::LOANS_DELETE                => $lang ? __('permissions.'.self::LOANS_DELETE) : self::LOANS_DELETE,

      self::CAREER_CHANGES_VIEW         => $lang ? __('permissions.'.self::CAREER_CHANGES_VIEW) : self::CAREER_CHANGES_VIEW,
      self::CAREER_CHANGES_CREATE       => $lang ? __('permissions.'.self::CAREER_CHANGES_CREATE) : self::CAREER_CHANGES_CREATE,
      self::CAREER_CHANGES_UPDATE       => $lang ? __('permissions.'.self::CAREER_CHANGES_UPDATE) : self::CAREER_CHANGES_UPDATE,
      self::CAREER_CHANGES_DELETE       => $lang ? __('permissions.'.self::CAREER_CHANGES_DELETE) : self::CAREER_CHANGES_DELETE,

      self::TRANSACTIONS_VIEW           => $lang ? __('permissions.'.self::TRANSACTIONS_VIEW) : self::TRANSACTIONS_VIEW,
      self::TRANSACTIONS_CREATE         => $lang ? __('permissions.'.self::TRANSACTIONS_CREATE) : self::TRANSACTIONS_CREATE,
      self::TRANSACTIONS_UPDATE         => $lang ? __('permissions.'.self::TRANSACTIONS_UPDATE) : self::TRANSACTIONS_UPDATE,
      self::TRANSACTIONS_DELETE         => $lang ? __('permissions.'.self::TRANSACTIONS_DELETE) : self::TRANSACTIONS_DELETE,

      self::SALARIES_VIEW              => $lang ? __('permissions.'.self::SALARIES_VIEW) : self::SALARIES_VIEW,
      self::SALARIES_CREATE            => $lang ? __('permissions.'.self::SALARIES_CREATE) : self::SALARIES_CREATE,
      self::SALARIES_UPDATE            => $lang ? __('permissions.'.self::SALARIES_UPDATE) : self::SALARIES_UPDATE,
      self::SALARIES_DELETE            => $lang ? __('permissions.'.self::SALARIES_DELETE) : self::SALARIES_DELETE,
      self::SALARIES_UPDATE_TO_PROCESSED => $lang ? __('permissions.'.self::SALARIES_UPDATE_TO_PROCESSED) : self::SALARIES_UPDATE_TO_PROCESSED,
      self::SALARIES_UPDATE_TO_PAID      => $lang ? __('permissions.'.self::SALARIES_UPDATE_TO_PAID) : self::SALARIES_UPDATE_TO_PAID,

      self::ACCOUNTING_GENERAL_VIEW    => $lang ? __('permissions.'.self::ACCOUNTING_GENERAL_VIEW) : self::ACCOUNTING_GENERAL_VIEW,
      self::ACCOUNTING_DOCTORS_VIEW    => $lang ? __('permissions.'.self::ACCOUNTING_DOCTORS_VIEW) : self::ACCOUNTING_DOCTORS_VIEW,
      self::ACCOUNTING_CREATE            => $lang ? __('permissions.'.self::ACCOUNTING_CREATE) : self::ACCOUNTING_CREATE,
      self::ACCOUNTING_UPDATE            => $lang ? __('permissions.'.self::ACCOUNTING_UPDATE) : self::ACCOUNTING_UPDATE,
      self::ACCOUNTING_PRINT_A_REPORT     => $lang ? __('permissions.'.self::ACCOUNTING_PRINT_A_REPORT) : self::ACCOUNTING_PRINT_A_REPORT,
      self::ACCOUNTING_PRINT_A_DOCTOR_REPORT     => $lang ? __('permissions.'.self::ACCOUNTING_PRINT_A_DOCTOR_REPORT) : self::ACCOUNTING_PRINT_A_DOCTOR_REPORT,
      self::ACCOUNTING_VIEW_DOCTORS_INCOME     => $lang ? __('permissions.'.self::ACCOUNTING_VIEW_DOCTORS_INCOME) : self::ACCOUNTING_VIEW_DOCTORS_INCOME,
      self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR     => $lang ? __('permissions.'.self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR) : self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR,

    ];
  }
  public static function admin($lang = null):array
  {
    return [
      self::USERS_VIEW                  => $lang ? __('permissions.'.self::USERS_VIEW) : self::USERS_VIEW,
      self::USERS_CREATE                => $lang ? __('permissions.'.self::USERS_CREATE) : self::USERS_CREATE,
      self::USERS_UPDATE                => $lang ? __('permissions.'.self::USERS_UPDATE) : self::USERS_UPDATE,
      self::USERS_DELETE                => $lang ? __('permissions.'.self::USERS_DELETE) : self::USERS_DELETE,

      self::SETTINGS_VIEW               => $lang ? __('permissions.'.self::SETTINGS_VIEW) : self::SETTINGS_VIEW,
      self::SETTINGS_UPDATE             => $lang ? __('permissions.'.self::SETTINGS_UPDATE) : self::SETTINGS_UPDATE,

      self::DEPARTMENTS_VIEW            => $lang ? __('permissions.'.self::DEPARTMENTS_VIEW) : self::DEPARTMENTS_VIEW,
      self::DEPARTMENTS_CREATE          => $lang ? __('permissions.'.self::DEPARTMENTS_CREATE) : self::DEPARTMENTS_CREATE,
      self::DEPARTMENTS_UPDATE          => $lang ? __('permissions.'.self::DEPARTMENTS_UPDATE) : self::DEPARTMENTS_UPDATE,
      self::DEPARTMENTS_DELETE          => $lang ? __('permissions.'.self::DEPARTMENTS_DELETE) : self::DEPARTMENTS_DELETE,

      self::SPECIALITIES_VIEW           => $lang ? __('permissions.'.self::SPECIALITIES_VIEW) : self::SPECIALITIES_VIEW,
      self::SPECIALITIES_CREATE         => $lang ? __('permissions.'.self::SPECIALITIES_CREATE) : self::SPECIALITIES_CREATE,
      self::SPECIALITIES_UPDATE         => $lang ? __('permissions.'.self::SPECIALITIES_UPDATE) : self::SPECIALITIES_UPDATE,
      self::SPECIALITIES_DELETE         => $lang ? __('permissions.'.self::SPECIALITIES_DELETE) : self::SPECIALITIES_DELETE,

      self::DOCTORS_VIEW                => $lang ? __('permissions.'.self::DOCTORS_VIEW) : self::DOCTORS_VIEW,
      self::DOCTORS_CREATE              => $lang ? __('permissions.'.self::DOCTORS_CREATE) : self::DOCTORS_CREATE,
      self::DOCTORS_UPDATE              => $lang ? __('permissions.'.self::DOCTORS_UPDATE) : self::DOCTORS_UPDATE,
      self::DOCTORS_DELETE              => $lang ? __('permissions.'.self::DOCTORS_DELETE) : self::DOCTORS_DELETE,

      self::PATIENTS_VIEW               => $lang ? __('permissions.'.self::PATIENTS_VIEW) : self::PATIENTS_VIEW,
      self::PATIENTS_CREATE             => $lang ? __('permissions.'.self::PATIENTS_CREATE) : self::PATIENTS_CREATE,
      self::PATIENTS_UPDATE             => $lang ? __('permissions.'.self::PATIENTS_UPDATE) : self::PATIENTS_UPDATE,
      self::PATIENTS_DELETE             => $lang ? __('permissions.'.self::PATIENTS_DELETE) : self::PATIENTS_DELETE,

      self::CHECKUPS_VIEW               => $lang ? __('permissions.'.self::CHECKUPS_VIEW) : self::CHECKUPS_VIEW,
      self::CHECKUPS_CREATE             => $lang ? __('permissions.'.self::CHECKUPS_CREATE) : self::CHECKUPS_CREATE,
      self::CHECKUPS_UPDATE             => $lang ? __('permissions.'.self::CHECKUPS_UPDATE) : self::CHECKUPS_UPDATE,
      self::CHECKUPS_DELETE             => $lang ? __('permissions.'.self::CHECKUPS_DELETE) : self::CHECKUPS_DELETE,

      self::CHECKUPS_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_PENDING) : self::CHECKUPS_UPDATE_TO_PENDING,
      self::CHECKUPS_UPDATE_TO_IN_CONSULTATION  => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_IN_CONSULTATION) : self::CHECKUPS_UPDATE_TO_IN_CONSULTATION,
      self::CHECKUPS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_COMPLETED) : self::CHECKUPS_UPDATE_TO_COMPLETED,

      self::CHECKUP_SERVICES_VIEW       => $lang ? __('permissions.'.self::CHECKUP_SERVICES_VIEW) : self::CHECKUP_SERVICES_VIEW,
      self::CHECKUP_SERVICES_CREATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_CREATE) : self::CHECKUP_SERVICES_CREATE,
      self::CHECKUP_SERVICES_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE) : self::CHECKUP_SERVICES_UPDATE,
      self::CHECKUP_SERVICES_DELETE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_DELETE) : self::CHECKUP_SERVICES_DELETE,

      self::CHECKUP_SERVICES_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_PENDING) : self::CHECKUP_SERVICES_UPDATE_TO_PENDING,
      self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED) : self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED,
      self::ANALYSES_SEND_RESULT_NOTIFICATION       => $lang ? __('permissions.'.self::ANALYSES_SEND_RESULT_NOTIFICATION) : self::ANALYSES_SEND_RESULT_NOTIFICATION,

      self::CHECKUP_RADIOLIGY_VIEW       => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_VIEW) : self::CHECKUP_RADIOLIGY_VIEW,
      self::CHECKUP_RADIOLIGY_CREATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_CREATE) : self::CHECKUP_RADIOLIGY_CREATE,
      self::CHECKUP_RADIOLIGY_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE) : self::CHECKUP_RADIOLIGY_UPDATE,
      self::CHECKUP_RADIOLIGY_DELETE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_DELETE) : self::CHECKUP_RADIOLIGY_DELETE,

      self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING) : self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED) : self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED,

      self::HOSPITALIZATIONS_VIEW              => $lang ? __('permissions.'.self::HOSPITALIZATIONS_VIEW) : self::HOSPITALIZATIONS_VIEW,
      self::HOSPITALIZATIONS_CREATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_CREATE) : self::HOSPITALIZATIONS_CREATE,
      self::HOSPITALIZATIONS_UPDATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE) : self::HOSPITALIZATIONS_UPDATE,
      self::HOSPITALIZATIONS_DELETE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_DELETE) : self::HOSPITALIZATIONS_DELETE,

      self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED          => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED) : self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED,
      self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED         => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED) : self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED,
      self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED       => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED) : self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED,

      self::OPERATIONS_VIEW              => $lang ? __('permissions.'.self::OPERATIONS_VIEW) : self::OPERATIONS_VIEW,
      self::OPERATIONS_CREATE            => $lang ? __('permissions.'.self::OPERATIONS_CREATE) : self::OPERATIONS_CREATE,
      self::OPERATIONS_UPDATE            => $lang ? __('permissions.'.self::OPERATIONS_UPDATE) : self::OPERATIONS_UPDATE,
      self::OPERATIONS_DELETE            => $lang ? __('permissions.'.self::OPERATIONS_DELETE) : self::OPERATIONS_DELETE,

      self::OPERATIONS_UPDATE_TO_PENDING        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_PENDING) : self::OPERATIONS_UPDATE_TO_PENDING,
      self::OPERATIONS_UPDATE_TO_SCHEDULED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_SCHEDULED) : self::OPERATIONS_UPDATE_TO_SCHEDULED,
      self::OPERATIONS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_COMPLETED) : self::OPERATIONS_UPDATE_TO_COMPLETED,

      self::MEDICAL_SERVICES_VIEW       => $lang ? __('permissions.'.self::MEDICAL_SERVICES_VIEW) : self::MEDICAL_SERVICES_VIEW,
      self::MEDICAL_SERVICES_CREATE     => $lang ? __('permissions.'.self::MEDICAL_SERVICES_CREATE) : self::MEDICAL_SERVICES_CREATE,
      self::MEDICAL_SERVICES_UPDATE     => $lang ? __('permissions.'.self::MEDICAL_SERVICES_UPDATE) : self::MEDICAL_SERVICES_UPDATE,
      self::MEDICAL_SERVICES_DELETE     => $lang ? __('permissions.'.self::MEDICAL_SERVICES_DELETE) : self::MEDICAL_SERVICES_DELETE,

      self::MEDICINES_VIEW              => $lang ? __('permissions.'.self::MEDICINES_VIEW) : self::MEDICINES_VIEW,
      self::MEDICINES_CREATE            => $lang ? __('permissions.'.self::MEDICINES_CREATE) : self::MEDICINES_CREATE,
      self::MEDICINES_UPDATE            => $lang ? __('permissions.'.self::MEDICINES_UPDATE) : self::MEDICINES_UPDATE,
      self::MEDICINES_DELETE            => $lang ? __('permissions.'.self::MEDICINES_DELETE) : self::MEDICINES_DELETE,

      self::CHRONIC_DISEASES_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_VIEW) : self::CHRONIC_DISEASES_VIEW,
      self::CHRONIC_DISEASES_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_CREATE) : self::CHRONIC_DISEASES_CREATE,
      self::CHRONIC_DISEASES_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_UPDATE) : self::CHRONIC_DISEASES_UPDATE,
      self::CHRONIC_DISEASES_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_DELETE) : self::CHRONIC_DISEASES_DELETE,

      self::CHRONIC_DISEASES_PATIENT_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_VIEW) : self::CHRONIC_DISEASES_PATIENT_VIEW,
      self::CHRONIC_DISEASES_PATIENT_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_CREATE) : self::CHRONIC_DISEASES_PATIENT_CREATE,
      self::CHRONIC_DISEASES_PATIENT_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_UPDATE) : self::CHRONIC_DISEASES_PATIENT_UPDATE,
      self::CHRONIC_DISEASES_PATIENT_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_DELETE) : self::CHRONIC_DISEASES_PATIENT_DELETE,

      self::PRESCRIPTIONS_VIEW          => $lang ? __('permissions.'.self::PRESCRIPTIONS_VIEW) : self::PRESCRIPTIONS_VIEW,
      self::PRESCRIPTIONS_CREATE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_CREATE) : self::PRESCRIPTIONS_CREATE,
      self::PRESCRIPTIONS_UPDATE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_UPDATE) : self::PRESCRIPTIONS_UPDATE,
      self::PRESCRIPTIONS_DELETE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_DELETE) : self::PRESCRIPTIONS_DELETE,

      self::SUPPLIERS_VIEW              => $lang ? __('permissions.'.self::SUPPLIERS_VIEW) : self::SUPPLIERS_VIEW,
      self::SUPPLIERS_CREATE            => $lang ? __('permissions.'.self::SUPPLIERS_CREATE) : self::SUPPLIERS_CREATE,
      self::SUPPLIERS_UPDATE            => $lang ? __('permissions.'.self::SUPPLIERS_UPDATE) : self::SUPPLIERS_UPDATE,
      self::SUPPLIERS_DELETE            => $lang ? __('permissions.'.self::SUPPLIERS_DELETE) : self::SUPPLIERS_DELETE,

      self::ITEMSCATEGORY_VIEW          => $lang ? __('permissions.'.self::ITEMSCATEGORY_VIEW) : self::ITEMSCATEGORY_VIEW,
      self::ITEMSCATEGORY_CREATE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_CREATE) : self::ITEMSCATEGORY_CREATE,
      self::ITEMSCATEGORY_UPDATE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_UPDATE) : self::ITEMSCATEGORY_UPDATE,
      self::ITEMSCATEGORY_DELETE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_DELETE) : self::ITEMSCATEGORY_DELETE,

      self::INSURANCE_SOCIETIES_VIEW    => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_VIEW) : self::INSURANCE_SOCIETIES_VIEW,
      self::INSURANCE_SOCIETIES_CREATE  => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_CREATE) : self::INSURANCE_SOCIETIES_CREATE,
      self::INSURANCE_SOCIETIES_UPDATE  => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_UPDATE) : self::INSURANCE_SOCIETIES_UPDATE,
      self::INSURANCE_SOCIETIES_DELETE  => $lang ? __('permissions.'.self::INSURANCE_SOCIETIES_DELETE) : self::INSURANCE_SOCIETIES_DELETE,
      self::ASSURANCE_VIEW              => $lang ? __('permissions.'.self::ASSURANCE_VIEW) : self::ASSURANCE_VIEW,

      self::SUPPLYREQUEST_VIEW          => $lang ? __('permissions.'.self::SUPPLYREQUEST_VIEW) : self::SUPPLYREQUEST_VIEW,
      self::SUPPLYREQUEST_CREATE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_CREATE) : self::SUPPLYREQUEST_CREATE,
      self::SUPPLYREQUEST_UPDATE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_UPDATE) : self::SUPPLYREQUEST_UPDATE,
      self::SUPPLYREQUEST_DELETE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_DELETE) : self::SUPPLYREQUEST_DELETE,

      self::INVENTORYITEM_VIEW          => $lang ? __('permissions.'.self::INVENTORYITEM_VIEW) : self::INVENTORYITEM_VIEW,
      self::INVENTORYITEM_CREATE        => $lang ? __('permissions.'.self::INVENTORYITEM_CREATE) : self::INVENTORYITEM_CREATE,
      self::INVENTORYITEM_UPDATE        => $lang ? __('permissions.'.self::INVENTORYITEM_UPDATE) : self::INVENTORYITEM_UPDATE,
      self::INVENTORYITEM_DELETE        => $lang ? __('permissions.'.self::INVENTORYITEM_DELETE) : self::INVENTORYITEM_DELETE,

      self::INVENTORYTRANSACTIONS_VIEW   => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_VIEW) : self::INVENTORYTRANSACTIONS_VIEW,
      self::INVENTORYTRANSACTIONS_CREATE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_CREATE) : self::INVENTORYTRANSACTIONS_CREATE,
      self::INVENTORYTRANSACTIONS_UPDATE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_UPDATE) : self::INVENTORYTRANSACTIONS_UPDATE,
      self::INVENTORYTRANSACTIONS_DELETE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_DELETE) : self::INVENTORYTRANSACTIONS_DELETE,

      self::DESIGNATIONS_VIEW           => $lang ? __('permissions.'.self::DESIGNATIONS_VIEW) : self::DESIGNATIONS_VIEW,
      self::DESIGNATIONS_CREATE         => $lang ? __('permissions.'.self::DESIGNATIONS_CREATE) : self::DESIGNATIONS_CREATE,
      self::DESIGNATIONS_UPDATE         => $lang ? __('permissions.'.self::DESIGNATIONS_UPDATE) : self::DESIGNATIONS_UPDATE,
      self::DESIGNATIONS_DELETE         => $lang ? __('permissions.'.self::DESIGNATIONS_DELETE) : self::DESIGNATIONS_DELETE,

      self::BONUSES_VIEW                => $lang ? __('permissions.'.self::BONUSES_VIEW) : self::BONUSES_VIEW,
      self::BONUSES_CREATE              => $lang ? __('permissions.'.self::BONUSES_CREATE) : self::BONUSES_CREATE,
      self::BONUSES_UPDATE              => $lang ? __('permissions.'.self::BONUSES_UPDATE) : self::BONUSES_UPDATE,
      self::BONUSES_DELETE              => $lang ? __('permissions.'.self::BONUSES_DELETE) : self::BONUSES_DELETE,

      self::EMPLOYEES_VIEW              => $lang ? __('permissions.'.self::EMPLOYEES_VIEW) : self::EMPLOYEES_VIEW,
      self::EMPLOYEES_CREATE            => $lang ? __('permissions.'.self::EMPLOYEES_CREATE) : self::EMPLOYEES_CREATE,
      self::EMPLOYEES_UPDATE            => $lang ? __('permissions.'.self::EMPLOYEES_UPDATE) : self::EMPLOYEES_UPDATE,
      self::EMPLOYEES_DELETE            => $lang ? __('permissions.'.self::EMPLOYEES_DELETE) : self::EMPLOYEES_DELETE,

      self::CONTRACTS_VIEW              => $lang ? __('permissions.'.self::CONTRACTS_VIEW) : self::CONTRACTS_VIEW,
      self::CONTRACTS_CREATE            => $lang ? __('permissions.'.self::CONTRACTS_CREATE) : self::CONTRACTS_CREATE,
      self::CONTRACTS_UPDATE            => $lang ? __('permissions.'.self::CONTRACTS_UPDATE) : self::CONTRACTS_UPDATE,
      self::CONTRACTS_DELETE            => $lang ? __('permissions.'.self::CONTRACTS_DELETE) : self::CONTRACTS_DELETE,

      self::ATTENDANCES_VIEW            => $lang ? __('permissions.'.self::ATTENDANCES_VIEW) : self::ATTENDANCES_VIEW,
      self::ATTENDANCES_CREATE          => $lang ? __('permissions.'.self::ATTENDANCES_CREATE) : self::ATTENDANCES_CREATE,
      self::ATTENDANCES_UPDATE          => $lang ? __('permissions.'.self::ATTENDANCES_UPDATE) : self::ATTENDANCES_UPDATE,
      self::ATTENDANCES_DELETE          => $lang ? __('permissions.'.self::ATTENDANCES_DELETE) : self::ATTENDANCES_DELETE,

      self::LOANS_VIEW                  => $lang ? __('permissions.'.self::LOANS_VIEW) : self::LOANS_VIEW,
      self::LOANS_CREATE                => $lang ? __('permissions.'.self::LOANS_CREATE) : self::LOANS_CREATE,
      self::LOANS_UPDATE                => $lang ? __('permissions.'.self::LOANS_UPDATE) : self::LOANS_UPDATE,
      self::LOANS_DELETE                => $lang ? __('permissions.'.self::LOANS_DELETE) : self::LOANS_DELETE,

      self::CAREER_CHANGES_VIEW         => $lang ? __('permissions.'.self::CAREER_CHANGES_VIEW) : self::CAREER_CHANGES_VIEW,
      self::CAREER_CHANGES_CREATE       => $lang ? __('permissions.'.self::CAREER_CHANGES_CREATE) : self::CAREER_CHANGES_CREATE,
      self::CAREER_CHANGES_UPDATE       => $lang ? __('permissions.'.self::CAREER_CHANGES_UPDATE) : self::CAREER_CHANGES_UPDATE,
      self::CAREER_CHANGES_DELETE       => $lang ? __('permissions.'.self::CAREER_CHANGES_DELETE) : self::CAREER_CHANGES_DELETE,

      self::TRANSACTIONS_VIEW           => $lang ? __('permissions.'.self::TRANSACTIONS_VIEW) : self::TRANSACTIONS_VIEW,
      self::TRANSACTIONS_CREATE         => $lang ? __('permissions.'.self::TRANSACTIONS_CREATE) : self::TRANSACTIONS_CREATE,
      self::TRANSACTIONS_UPDATE         => $lang ? __('permissions.'.self::TRANSACTIONS_UPDATE) : self::TRANSACTIONS_UPDATE,
      self::TRANSACTIONS_DELETE         => $lang ? __('permissions.'.self::TRANSACTIONS_DELETE) : self::TRANSACTIONS_DELETE,

      self::SALARIES_VIEW              => $lang ? __('permissions.'.self::SALARIES_VIEW) : self::SALARIES_VIEW,
      self::SALARIES_CREATE            => $lang ? __('permissions.'.self::SALARIES_CREATE) : self::SALARIES_CREATE,
      self::SALARIES_UPDATE            => $lang ? __('permissions.'.self::SALARIES_UPDATE) : self::SALARIES_UPDATE,
      self::SALARIES_DELETE            => $lang ? __('permissions.'.self::SALARIES_DELETE) : self::SALARIES_DELETE,
      self::SALARIES_UPDATE_TO_PROCESSED => $lang ? __('permissions.'.self::SALARIES_UPDATE_TO_PROCESSED) : self::SALARIES_UPDATE_TO_PROCESSED,
      self::SALARIES_UPDATE_TO_PAID      => $lang ? __('permissions.'.self::SALARIES_UPDATE_TO_PAID) : self::SALARIES_UPDATE_TO_PAID,

      self::ACCOUNTING_GENERAL_VIEW    => $lang ? __('permissions.'.self::ACCOUNTING_GENERAL_VIEW) : self::ACCOUNTING_GENERAL_VIEW,
      self::ACCOUNTING_DOCTORS_VIEW   => $lang ? __('permissions.'.self::ACCOUNTING_DOCTORS_VIEW) : self::ACCOUNTING_DOCTORS_VIEW,
      self::ACCOUNTING_CREATE            => $lang ? __('permissions.'.self::ACCOUNTING_CREATE) : self::ACCOUNTING_CREATE,
      self::ACCOUNTING_UPDATE            => $lang ? __('permissions.'.self::ACCOUNTING_UPDATE) : self::ACCOUNTING_UPDATE,
      self::ACCOUNTING_PRINT_A_REPORT     => $lang ? __('permissions.'.self::ACCOUNTING_PRINT_A_REPORT) : self::ACCOUNTING_PRINT_A_REPORT,
      self::ACCOUNTING_PRINT_A_DOCTOR_REPORT     => $lang ? __('permissions.'.self::ACCOUNTING_PRINT_A_DOCTOR_REPORT) : self::ACCOUNTING_PRINT_A_DOCTOR_REPORT,
      self::ACCOUNTING_VIEW_DOCTORS_INCOME     => $lang ? __('permissions.'.self::ACCOUNTING_VIEW_DOCTORS_INCOME) : self::ACCOUNTING_VIEW_DOCTORS_INCOME,
      self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR     => $lang ? __('permissions.'.self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR) : self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR,
    ];
  }
  public static function accountant($lang = null):array
  {
    return [
      self::DOCTORS_VIEW                => $lang ? __('permissions.'.self::DOCTORS_VIEW) : self::DOCTORS_VIEW,

      self::PATIENTS_VIEW               => $lang ? __('permissions.'.self::PATIENTS_VIEW) : self::PATIENTS_VIEW,
      self::PATIENTS_CREATE             => $lang ? __('permissions.'.self::PATIENTS_CREATE) : self::PATIENTS_CREATE,
      self::PATIENTS_UPDATE             => $lang ? __('permissions.'.self::PATIENTS_UPDATE) : self::PATIENTS_UPDATE,
      self::PATIENTS_DELETE             => $lang ? __('permissions.'.self::PATIENTS_DELETE) : self::PATIENTS_DELETE,

      self::CHECKUPS_VIEW               => $lang ? __('permissions.'.self::CHECKUPS_VIEW) : self::CHECKUPS_VIEW,
      self::CHECKUPS_CREATE             => $lang ? __('permissions.'.self::CHECKUPS_CREATE) : self::CHECKUPS_CREATE,
      self::CHECKUPS_UPDATE             => $lang ? __('permissions.'.self::CHECKUPS_UPDATE) : self::CHECKUPS_UPDATE,
      self::CHECKUPS_DELETE             => $lang ? __('permissions.'.self::CHECKUPS_DELETE) : self::CHECKUPS_DELETE,

      self::CHECKUPS_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_PENDING) : self::CHECKUPS_UPDATE_TO_PENDING,
      self::CHECKUPS_UPDATE_TO_IN_CONSULTATION  => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_IN_CONSULTATION) : self::CHECKUPS_UPDATE_TO_IN_CONSULTATION,
      self::CHECKUPS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_COMPLETED) : self::CHECKUPS_UPDATE_TO_COMPLETED,

      self::CHECKUP_SERVICES_VIEW       => $lang ? __('permissions.'.self::CHECKUP_SERVICES_VIEW) : self::CHECKUP_SERVICES_VIEW,
      self::CHECKUP_SERVICES_CREATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_CREATE) : self::CHECKUP_SERVICES_CREATE,
      self::CHECKUP_SERVICES_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE) : self::CHECKUP_SERVICES_UPDATE,
      self::CHECKUP_SERVICES_DELETE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_DELETE) : self::CHECKUP_SERVICES_DELETE,

      self::CHECKUP_SERVICES_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_PENDING) : self::CHECKUP_SERVICES_UPDATE_TO_PENDING,
      self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED) : self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED,

      self::CHECKUP_RADIOLIGY_VIEW       => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_VIEW) : self::CHECKUP_RADIOLIGY_VIEW,
      self::CHECKUP_RADIOLIGY_CREATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_CREATE) : self::CHECKUP_RADIOLIGY_CREATE,
      self::CHECKUP_RADIOLIGY_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE) : self::CHECKUP_RADIOLIGY_UPDATE,
      self::CHECKUP_RADIOLIGY_DELETE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_DELETE) : self::CHECKUP_RADIOLIGY_DELETE,

      self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING          => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING) : self::CHECKUP_RADIOLIGY_UPDATE_TO_PENDING,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED) : self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED,

      self::HOSPITALIZATIONS_VIEW              => $lang ? __('permissions.'.self::HOSPITALIZATIONS_VIEW) : self::HOSPITALIZATIONS_VIEW,
      self::HOSPITALIZATIONS_CREATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_CREATE) : self::HOSPITALIZATIONS_CREATE,
      self::HOSPITALIZATIONS_UPDATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE) : self::HOSPITALIZATIONS_UPDATE,
      self::HOSPITALIZATIONS_DELETE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_DELETE) : self::HOSPITALIZATIONS_DELETE,

      self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED          => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED) : self::HOSPITALIZATIONS_UPDATE_TO_ACCEPTED,
      self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED         => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED) : self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED,
      self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED       => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED) : self::HOSPITALIZATIONS_UPDATE_TO_DISCHARGED,

      self::OPERATIONS_VIEW              => $lang ? __('permissions.'.self::OPERATIONS_VIEW) : self::OPERATIONS_VIEW,
      self::OPERATIONS_CREATE            => $lang ? __('permissions.'.self::OPERATIONS_CREATE) : self::OPERATIONS_CREATE,
      self::OPERATIONS_UPDATE            => $lang ? __('permissions.'.self::OPERATIONS_UPDATE) : self::OPERATIONS_UPDATE,
      self::OPERATIONS_DELETE            => $lang ? __('permissions.'.self::OPERATIONS_DELETE) : self::OPERATIONS_DELETE,

      self::OPERATIONS_UPDATE_TO_PENDING        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_PENDING) : self::OPERATIONS_UPDATE_TO_PENDING,
      self::OPERATIONS_UPDATE_TO_SCHEDULED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_SCHEDULED) : self::OPERATIONS_UPDATE_TO_SCHEDULED,
      self::OPERATIONS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_COMPLETED) : self::OPERATIONS_UPDATE_TO_COMPLETED,

      self::MEDICAL_SERVICES_VIEW       => $lang ? __('permissions.'.self::MEDICAL_SERVICES_VIEW) : self::MEDICAL_SERVICES_VIEW,

      self::CHRONIC_DISEASES_PATIENT_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_VIEW) : self::CHRONIC_DISEASES_PATIENT_VIEW,
      self::CHRONIC_DISEASES_PATIENT_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_CREATE) : self::CHRONIC_DISEASES_PATIENT_CREATE,
      self::CHRONIC_DISEASES_PATIENT_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_UPDATE) : self::CHRONIC_DISEASES_PATIENT_UPDATE,
      self::CHRONIC_DISEASES_PATIENT_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_DELETE) : self::CHRONIC_DISEASES_PATIENT_DELETE,

      self::PRESCRIPTIONS_VIEW          => $lang ? __('permissions.'.self::PRESCRIPTIONS_VIEW) : self::PRESCRIPTIONS_VIEW,

      self::TRANSACTIONS_VIEW           => $lang ? __('permissions.'.self::TRANSACTIONS_VIEW) : self::TRANSACTIONS_VIEW,
      self::TRANSACTIONS_CREATE         => $lang ? __('permissions.'.self::TRANSACTIONS_CREATE) : self::TRANSACTIONS_CREATE,
      self::TRANSACTIONS_UPDATE         => $lang ? __('permissions.'.self::TRANSACTIONS_UPDATE) : self::TRANSACTIONS_UPDATE,
      self::TRANSACTIONS_DELETE         => $lang ? __('permissions.'.self::TRANSACTIONS_DELETE) : self::TRANSACTIONS_DELETE,

      self::SALARIES_VIEW              => $lang ? __('permissions.'.self::SALARIES_VIEW) : self::SALARIES_VIEW,
      self::SALARIES_CREATE            => $lang ? __('permissions.'.self::SALARIES_CREATE) : self::SALARIES_CREATE,
      self::SALARIES_UPDATE            => $lang ? __('permissions.'.self::SALARIES_UPDATE) : self::SALARIES_UPDATE,
      self::SALARIES_DELETE            => $lang ? __('permissions.'.self::SALARIES_DELETE) : self::SALARIES_DELETE,

      self::ACCOUNTING_GENERAL_VIEW    => $lang ? __('permissions.'.self::ACCOUNTING_GENERAL_VIEW) : self::ACCOUNTING_GENERAL_VIEW,
      self::ACCOUNTING_DOCTORS_VIEW   => $lang ? __('permissions.'.self::ACCOUNTING_DOCTORS_VIEW) : self::ACCOUNTING_DOCTORS_VIEW,
      self::ACCOUNTING_CREATE            => $lang ? __('permissions.'.self::ACCOUNTING_CREATE) : self::ACCOUNTING_CREATE,
      self::ACCOUNTING_UPDATE            => $lang ? __('permissions.'.self::ACCOUNTING_UPDATE) : self::ACCOUNTING_UPDATE,
      self::ACCOUNTING_PRINT_A_REPORT     => $lang ? __('permissions.'.self::ACCOUNTING_PRINT_A_REPORT) : self::ACCOUNTING_PRINT_A_REPORT,
      self::ACCOUNTING_PRINT_A_DOCTOR_REPORT     => $lang ? __('permissions.'.self::ACCOUNTING_PRINT_A_DOCTOR_REPORT) : self::ACCOUNTING_PRINT_A_DOCTOR_REPORT,
      self::ACCOUNTING_VIEW_DOCTORS_INCOME     => $lang ? __('permissions.'.self::ACCOUNTING_VIEW_DOCTORS_INCOME) : self::ACCOUNTING_VIEW_DOCTORS_INCOME,
      self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR     => $lang ? __('permissions.'.self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR) : self::ACCOUNTING_PAYMENT_TO_THE_DOCTOR,
    ];
  }
  public static function receptionist($lang = null):array
  {
    return [
      self::DOCTORS_VIEW                => $lang ? __('permissions.'.self::DOCTORS_VIEW) : self::DOCTORS_VIEW,

      self::PATIENTS_VIEW               => $lang ? __('permissions.'.self::PATIENTS_VIEW) : self::PATIENTS_VIEW,
      self::PATIENTS_CREATE             => $lang ? __('permissions.'.self::PATIENTS_CREATE) : self::PATIENTS_CREATE,
      self::PATIENTS_UPDATE             => $lang ? __('permissions.'.self::PATIENTS_UPDATE) : self::PATIENTS_UPDATE,
      self::PATIENTS_DELETE             => $lang ? __('permissions.'.self::PATIENTS_DELETE) : self::PATIENTS_DELETE,

      self::CHECKUPS_VIEW               => $lang ? __('permissions.'.self::CHECKUPS_VIEW) : self::CHECKUPS_VIEW,
      self::CHECKUPS_CREATE             => $lang ? __('permissions.'.self::CHECKUPS_CREATE) : self::CHECKUPS_CREATE,
      self::CHECKUPS_UPDATE             => $lang ? __('permissions.'.self::CHECKUPS_UPDATE) : self::CHECKUPS_UPDATE,
      self::CHECKUPS_DELETE             => $lang ? __('permissions.'.self::CHECKUPS_DELETE) : self::CHECKUPS_DELETE,

      self::CHECKUPS_UPDATE_TO_IN_CONSULTATION  => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_IN_CONSULTATION) : self::CHECKUPS_UPDATE_TO_IN_CONSULTATION,
      self::CHECKUPS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_COMPLETED) : self::CHECKUPS_UPDATE_TO_COMPLETED,

      self::CHECKUP_SERVICES_VIEW       => $lang ? __('permissions.'.self::CHECKUP_SERVICES_VIEW) : self::CHECKUP_SERVICES_VIEW,
      self::CHECKUP_SERVICES_CREATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_CREATE) : self::CHECKUP_SERVICES_CREATE,
      self::CHECKUP_SERVICES_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE) : self::CHECKUP_SERVICES_UPDATE,
      self::CHECKUP_SERVICES_DELETE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_DELETE) : self::CHECKUP_SERVICES_DELETE,

      self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED) : self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED,

      self::CHECKUP_RADIOLIGY_VIEW       => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_VIEW) : self::CHECKUP_RADIOLIGY_VIEW,
      self::CHECKUP_RADIOLIGY_CREATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_CREATE) : self::CHECKUP_RADIOLIGY_CREATE,
      self::CHECKUP_RADIOLIGY_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE) : self::CHECKUP_RADIOLIGY_UPDATE,
      self::CHECKUP_RADIOLIGY_DELETE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_DELETE) : self::CHECKUP_RADIOLIGY_DELETE,

      self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED        => $lang ?__('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED) : self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED,

      self::HOSPITALIZATIONS_VIEW              => $lang ? __('permissions.'.self::HOSPITALIZATIONS_VIEW) : self::HOSPITALIZATIONS_VIEW,
      self::HOSPITALIZATIONS_CREATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_CREATE) : self::HOSPITALIZATIONS_CREATE,
      self::HOSPITALIZATIONS_UPDATE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE) : self::HOSPITALIZATIONS_UPDATE,
      self::HOSPITALIZATIONS_DELETE            => $lang ? __('permissions.'.self::HOSPITALIZATIONS_DELETE) : self::HOSPITALIZATIONS_DELETE,

      self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED         => $lang ? __('permissions.'.self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED) : self::HOSPITALIZATIONS_UPDATE_TO_ADMITTED,

      self::OPERATIONS_VIEW              => $lang ? __('permissions.'.self::OPERATIONS_VIEW) : self::OPERATIONS_VIEW,
      self::OPERATIONS_CREATE            => $lang ? __('permissions.'.self::OPERATIONS_CREATE) : self::OPERATIONS_CREATE,
      self::OPERATIONS_UPDATE            => $lang ? __('permissions.'.self::OPERATIONS_UPDATE) : self::OPERATIONS_UPDATE,
      self::OPERATIONS_DELETE            => $lang ? __('permissions.'.self::OPERATIONS_DELETE) : self::OPERATIONS_DELETE,

      self::OPERATIONS_UPDATE_TO_SCHEDULED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_SCHEDULED) : self::OPERATIONS_UPDATE_TO_SCHEDULED,
      self::OPERATIONS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_COMPLETED) : self::OPERATIONS_UPDATE_TO_COMPLETED,

      self::CHRONIC_DISEASES_PATIENT_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_VIEW) : self::CHRONIC_DISEASES_PATIENT_VIEW,
      self::CHRONIC_DISEASES_PATIENT_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_CREATE) : self::CHRONIC_DISEASES_PATIENT_CREATE,
      self::CHRONIC_DISEASES_PATIENT_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_UPDATE) : self::CHRONIC_DISEASES_PATIENT_UPDATE,
      self::CHRONIC_DISEASES_PATIENT_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_DELETE) : self::CHRONIC_DISEASES_PATIENT_DELETE,

      self::PRESCRIPTIONS_VIEW          => $lang ? __('permissions.'.self::PRESCRIPTIONS_VIEW) : self::PRESCRIPTIONS_VIEW,
    ];
  }
  public static function doctor($lang = null):array
  {
    return [

      self::CHECKUPS_DOCTOR_VIEW        => $lang ? __('permissions.'.self::CHECKUPS_DOCTOR_VIEW) : self::CHECKUPS_DOCTOR_VIEW,
      self::CHECKUPS_DOCTOR_UPDATE      => $lang ? __('permissions.'.self::CHECKUPS_DOCTOR_UPDATE) : self::CHECKUPS_DOCTOR_UPDATE,

      self::CHECKUPS_UPDATE_TO_IN_CONSULTATION  => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_IN_CONSULTATION) : self::CHECKUPS_UPDATE_TO_IN_CONSULTATION,
      self::CHECKUPS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUPS_UPDATE_TO_COMPLETED) : self::CHECKUPS_UPDATE_TO_COMPLETED,

      self::CHECKUP_SERVICES_CREATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_CREATE) : self::CHECKUP_SERVICES_CREATE,
      self::CHECKUP_SERVICES_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE) : self::CHECKUP_SERVICES_UPDATE,
      self::CHECKUP_SERVICES_DELETE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_DELETE) : self::CHECKUP_SERVICES_DELETE,

      self::CHRONIC_DISEASES_PATIENT_VIEW       => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_VIEW) : self::CHRONIC_DISEASES_PATIENT_VIEW,
      self::CHRONIC_DISEASES_PATIENT_CREATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_CREATE) : self::CHRONIC_DISEASES_PATIENT_CREATE,
      self::CHRONIC_DISEASES_PATIENT_UPDATE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_UPDATE) : self::CHRONIC_DISEASES_PATIENT_UPDATE,
      self::CHRONIC_DISEASES_PATIENT_DELETE     => $lang ? __('permissions.'.self::CHRONIC_DISEASES_PATIENT_DELETE) : self::CHRONIC_DISEASES_PATIENT_DELETE,

      self::PRESCRIPTIONS_VIEW          => $lang ? __('permissions.'.self::PRESCRIPTIONS_VIEW) : self::PRESCRIPTIONS_VIEW,
      self::PRESCRIPTIONS_CREATE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_CREATE) : self::PRESCRIPTIONS_CREATE,
      self::PRESCRIPTIONS_UPDATE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_UPDATE) : self::PRESCRIPTIONS_UPDATE,
      self::PRESCRIPTIONS_DELETE        => $lang ? __('permissions.'.self::PRESCRIPTIONS_DELETE) : self::PRESCRIPTIONS_DELETE,
    ];
  }
  public static function radiologyManager($lang = null):array
  {
    return [
      self::CHECKUP_RADIOLIGY_VIEW       => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_VIEW) : self::CHECKUP_RADIOLIGY_VIEW,
      self::CHECKUP_RADIOLIGY_CREATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_CREATE) : self::CHECKUP_RADIOLIGY_CREATE,
      self::CHECKUP_RADIOLIGY_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE) : self::CHECKUP_RADIOLIGY_UPDATE,
      self::CHECKUP_RADIOLIGY_DELETE     => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_DELETE) : self::CHECKUP_RADIOLIGY_DELETE,

      self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_RADIOLIGY_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED        => $lang ?__('permissions.'.self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED) : self::CHECKUP_RADIOLIGY_UPDATE_TO_COMPLETED,
    ];
  }
  public static function laboratoryManager($lang = null):array
  {
    return [
      self::CHECKUP_SERVICES_VIEW       => $lang ? __('permissions.'.self::CHECKUP_SERVICES_VIEW) : self::CHECKUP_SERVICES_VIEW,
      self::CHECKUP_SERVICES_CREATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_CREATE) : self::CHECKUP_SERVICES_CREATE,
      self::CHECKUP_SERVICES_UPDATE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE) : self::CHECKUP_SERVICES_UPDATE,
      self::CHECKUP_SERVICES_DELETE     => $lang ? __('permissions.'.self::CHECKUP_SERVICES_DELETE) : self::CHECKUP_SERVICES_DELETE,

      self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS  => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS) : self::CHECKUP_SERVICES_UPDATE_TO_IN_PROGRESS,
      self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED) : self::CHECKUP_SERVICES_UPDATE_TO_COMPLETED,
    ];
  }
  public static function operationsManager($lang = null):array
  {
    return [
      self::OPERATIONS_VIEW              => $lang ? __('permissions.'.self::OPERATIONS_VIEW) : self::OPERATIONS_VIEW,
      self::OPERATIONS_CREATE            => $lang ? __('permissions.'.self::OPERATIONS_CREATE) : self::OPERATIONS_CREATE,
      self::OPERATIONS_UPDATE            => $lang ? __('permissions.'.self::OPERATIONS_UPDATE) : self::OPERATIONS_UPDATE,
      self::OPERATIONS_DELETE            => $lang ? __('permissions.'.self::OPERATIONS_DELETE) : self::OPERATIONS_DELETE,

      self::OPERATIONS_UPDATE_TO_SCHEDULED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_SCHEDULED) : self::OPERATIONS_UPDATE_TO_SCHEDULED,
      self::OPERATIONS_UPDATE_TO_COMPLETED        => $lang ? __('permissions.'.self::OPERATIONS_UPDATE_TO_COMPLETED) : self::OPERATIONS_UPDATE_TO_COMPLETED,
    ];
  }
  public static function humanResources($lang = null):array
  {
    return [
      self::USERS_VIEW                  => $lang ? __('permissions.'.self::USERS_VIEW) : self::USERS_VIEW,
      self::USERS_CREATE                => $lang ? __('permissions.'.self::USERS_CREATE) : self::USERS_CREATE,
      self::USERS_UPDATE                => $lang ? __('permissions.'.self::USERS_UPDATE) : self::USERS_UPDATE,
      self::USERS_DELETE                => $lang ? __('permissions.'.self::USERS_DELETE) : self::USERS_DELETE,

      self::DEPARTMENTS_VIEW            => $lang ? __('permissions.'.self::DEPARTMENTS_VIEW) : self::DEPARTMENTS_VIEW,
      self::DEPARTMENTS_CREATE          => $lang ? __('permissions.'.self::DEPARTMENTS_CREATE) : self::DEPARTMENTS_CREATE,
      self::DEPARTMENTS_UPDATE          => $lang ? __('permissions.'.self::DEPARTMENTS_UPDATE) : self::DEPARTMENTS_UPDATE,
      self::DEPARTMENTS_DELETE          => $lang ? __('permissions.'.self::DEPARTMENTS_DELETE) : self::DEPARTMENTS_DELETE,

      self::SPECIALITIES_VIEW           => $lang ? __('permissions.'.self::SPECIALITIES_VIEW) : self::SPECIALITIES_VIEW,
      self::SPECIALITIES_CREATE         => $lang ? __('permissions.'.self::SPECIALITIES_CREATE) : self::SPECIALITIES_CREATE,
      self::SPECIALITIES_UPDATE         => $lang ? __('permissions.'.self::SPECIALITIES_UPDATE) : self::SPECIALITIES_UPDATE,
      self::SPECIALITIES_DELETE         => $lang ? __('permissions.'.self::SPECIALITIES_DELETE) : self::SPECIALITIES_DELETE,

      self::DOCTORS_VIEW                => $lang ? __('permissions.'.self::DOCTORS_VIEW) : self::DOCTORS_VIEW,
      self::DOCTORS_CREATE              => $lang ? __('permissions.'.self::DOCTORS_CREATE) : self::DOCTORS_CREATE,
      self::DOCTORS_UPDATE              => $lang ? __('permissions.'.self::DOCTORS_UPDATE) : self::DOCTORS_UPDATE,
      self::DOCTORS_DELETE              => $lang ? __('permissions.'.self::DOCTORS_DELETE) : self::DOCTORS_DELETE,

      self::DESIGNATIONS_VIEW           => $lang ? __('permissions.'.self::DESIGNATIONS_VIEW) : self::DESIGNATIONS_VIEW,
      self::DESIGNATIONS_CREATE         => $lang ? __('permissions.'.self::DESIGNATIONS_CREATE) : self::DESIGNATIONS_CREATE,
      self::DESIGNATIONS_UPDATE         => $lang ? __('permissions.'.self::DESIGNATIONS_UPDATE) : self::DESIGNATIONS_UPDATE,
      self::DESIGNATIONS_DELETE         => $lang ? __('permissions.'.self::DESIGNATIONS_DELETE) : self::DESIGNATIONS_DELETE,

      self::BONUSES_VIEW                => $lang ? __('permissions.'.self::BONUSES_VIEW) : self::BONUSES_VIEW,
      self::BONUSES_CREATE              => $lang ? __('permissions.'.self::BONUSES_CREATE) : self::BONUSES_CREATE,
      self::BONUSES_UPDATE              => $lang ? __('permissions.'.self::BONUSES_UPDATE) : self::BONUSES_UPDATE,
      self::BONUSES_DELETE              => $lang ? __('permissions.'.self::BONUSES_DELETE) : self::BONUSES_DELETE,

      self::EMPLOYEES_VIEW              => $lang ? __('permissions.'.self::EMPLOYEES_VIEW) : self::EMPLOYEES_VIEW,
      self::EMPLOYEES_CREATE            => $lang ? __('permissions.'.self::EMPLOYEES_CREATE) : self::EMPLOYEES_CREATE,
      self::EMPLOYEES_UPDATE            => $lang ? __('permissions.'.self::EMPLOYEES_UPDATE) : self::EMPLOYEES_UPDATE,
      self::EMPLOYEES_DELETE            => $lang ? __('permissions.'.self::EMPLOYEES_DELETE) : self::EMPLOYEES_DELETE,

      self::CONTRACTS_VIEW              => $lang ? __('permissions.'.self::CONTRACTS_VIEW) : self::CONTRACTS_VIEW,
      self::CONTRACTS_CREATE            => $lang ? __('permissions.'.self::CONTRACTS_CREATE) : self::CONTRACTS_CREATE,
      self::CONTRACTS_UPDATE            => $lang ? __('permissions.'.self::CONTRACTS_UPDATE) : self::CONTRACTS_UPDATE,
      self::CONTRACTS_DELETE            => $lang ? __('permissions.'.self::CONTRACTS_DELETE) : self::CONTRACTS_DELETE,

      self::ATTENDANCES_VIEW            => $lang ? __('permissions.'.self::ATTENDANCES_VIEW) : self::ATTENDANCES_VIEW,
      self::ATTENDANCES_CREATE          => $lang ? __('permissions.'.self::ATTENDANCES_CREATE) : self::ATTENDANCES_CREATE,
      self::ATTENDANCES_UPDATE          => $lang ? __('permissions.'.self::ATTENDANCES_UPDATE) : self::ATTENDANCES_UPDATE,
      self::ATTENDANCES_DELETE          => $lang ? __('permissions.'.self::ATTENDANCES_DELETE) : self::ATTENDANCES_DELETE,

      self::LOANS_VIEW                  => $lang ? __('permissions.'.self::LOANS_VIEW) : self::LOANS_VIEW,
      self::LOANS_CREATE                => $lang ? __('permissions.'.self::LOANS_CREATE) : self::LOANS_CREATE,
      self::LOANS_UPDATE                => $lang ? __('permissions.'.self::LOANS_UPDATE) : self::LOANS_UPDATE,
      self::LOANS_DELETE                => $lang ? __('permissions.'.self::LOANS_DELETE) : self::LOANS_DELETE,

      self::CAREER_CHANGES_VIEW         => $lang ? __('permissions.'.self::CAREER_CHANGES_VIEW) : self::CAREER_CHANGES_VIEW,
      self::CAREER_CHANGES_CREATE       => $lang ? __('permissions.'.self::CAREER_CHANGES_CREATE) : self::CAREER_CHANGES_CREATE,
      self::CAREER_CHANGES_UPDATE       => $lang ? __('permissions.'.self::CAREER_CHANGES_UPDATE) : self::CAREER_CHANGES_UPDATE,
      self::CAREER_CHANGES_DELETE       => $lang ? __('permissions.'.self::CAREER_CHANGES_DELETE) : self::CAREER_CHANGES_DELETE,

      self::TRANSACTIONS_VIEW           => $lang ? __('permissions.'.self::TRANSACTIONS_VIEW) : self::TRANSACTIONS_VIEW,
      self::TRANSACTIONS_CREATE         => $lang ? __('permissions.'.self::TRANSACTIONS_CREATE) : self::TRANSACTIONS_CREATE,
      self::TRANSACTIONS_UPDATE         => $lang ? __('permissions.'.self::TRANSACTIONS_UPDATE) : self::TRANSACTIONS_UPDATE,
      self::TRANSACTIONS_DELETE         => $lang ? __('permissions.'.self::TRANSACTIONS_DELETE) : self::TRANSACTIONS_DELETE,

      self::SALARIES_VIEW              => $lang ? __('permissions.'.self::SALARIES_VIEW) : self::SALARIES_VIEW,
      self::SALARIES_CREATE            => $lang ? __('permissions.'.self::SALARIES_CREATE) : self::SALARIES_CREATE,
      self::SALARIES_UPDATE            => $lang ? __('permissions.'.self::SALARIES_UPDATE) : self::SALARIES_UPDATE,
      self::SALARIES_DELETE            => $lang ? __('permissions.'.self::SALARIES_DELETE) : self::SALARIES_DELETE,
      self::SALARIES_UPDATE_TO_PROCESSED => $lang ? __('permissions.'.self::SALARIES_UPDATE_TO_PROCESSED) : self::SALARIES_UPDATE_TO_PROCESSED,
      self::SALARIES_UPDATE_TO_PAID      => $lang ? __('permissions.'.self::SALARIES_UPDATE_TO_PAID) : self::SALARIES_UPDATE_TO_PAID
    ];
  }
  public static function inventoryManager($lang = null):array
  {
    return [
      self::SUPPLIERS_VIEW              => $lang ? __('permissions.'.self::SUPPLIERS_VIEW) : self::SUPPLIERS_VIEW,
      self::SUPPLIERS_CREATE            => $lang ? __('permissions.'.self::SUPPLIERS_CREATE) : self::SUPPLIERS_CREATE,
      self::SUPPLIERS_UPDATE            => $lang ? __('permissions.'.self::SUPPLIERS_UPDATE) : self::SUPPLIERS_UPDATE,
      self::SUPPLIERS_DELETE            => $lang ? __('permissions.'.self::SUPPLIERS_DELETE) : self::SUPPLIERS_DELETE,

      self::ITEMSCATEGORY_VIEW          => $lang ? __('permissions.'.self::ITEMSCATEGORY_VIEW) : self::ITEMSCATEGORY_VIEW,
      self::ITEMSCATEGORY_CREATE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_CREATE) : self::ITEMSCATEGORY_CREATE,
      self::ITEMSCATEGORY_UPDATE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_UPDATE) : self::ITEMSCATEGORY_UPDATE,
      self::ITEMSCATEGORY_DELETE        => $lang ? __('permissions.'.self::ITEMSCATEGORY_DELETE) : self::ITEMSCATEGORY_DELETE,

      self::SUPPLYREQUEST_VIEW          => $lang ? __('permissions.'.self::SUPPLYREQUEST_VIEW) : self::SUPPLYREQUEST_VIEW,
      self::SUPPLYREQUEST_CREATE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_CREATE) : self::SUPPLYREQUEST_CREATE,
      self::SUPPLYREQUEST_UPDATE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_UPDATE) : self::SUPPLYREQUEST_UPDATE,
      self::SUPPLYREQUEST_DELETE        => $lang ? __('permissions.'.self::SUPPLYREQUEST_DELETE) : self::SUPPLYREQUEST_DELETE,

      self::INVENTORYITEM_VIEW          => $lang ? __('permissions.'.self::INVENTORYITEM_VIEW) : self::INVENTORYITEM_VIEW,
      self::INVENTORYITEM_CREATE        => $lang ? __('permissions.'.self::INVENTORYITEM_CREATE) : self::INVENTORYITEM_CREATE,
      self::INVENTORYITEM_UPDATE        => $lang ? __('permissions.'.self::INVENTORYITEM_UPDATE) : self::INVENTORYITEM_UPDATE,
      self::INVENTORYITEM_DELETE        => $lang ? __('permissions.'.self::INVENTORYITEM_DELETE) : self::INVENTORYITEM_DELETE,

      self::INVENTORYTRANSACTIONS_VIEW   => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_VIEW) : self::INVENTORYTRANSACTIONS_VIEW,
      self::INVENTORYTRANSACTIONS_CREATE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_CREATE) : self::INVENTORYTRANSACTIONS_CREATE,
      self::INVENTORYTRANSACTIONS_UPDATE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_UPDATE) : self::INVENTORYTRANSACTIONS_UPDATE,
      self::INVENTORYTRANSACTIONS_DELETE => $lang ? __('permissions.'.self::INVENTORYTRANSACTIONS_DELETE) : self::INVENTORYTRANSACTIONS_DELETE,
    ];
  }

  public static function groups($lang = null):array
  {
    return [
      'roles' => $lang ? __('permissions.group.roles') : 'roles',
      'permissions' => $lang ? __('permissions.group.permissions') : 'permissions',
      'users' => $lang ? __('permissions.group.users') : 'users',
      'settings' => $lang ? __('permissions.group.settings') : 'settings',
      'departments' => $lang ? __('permissions.group.departments') : 'departments',
      'specialities' => $lang ? __('permissions.group.specialities') : 'specialities',
      'doctors' => $lang ? __('permissions.group.doctors') : 'doctors',
      'patients' => $lang ? __('permissions.group.patients') : 'patients',
      'checkups' => $lang ? __('permissions.group.checkups') : 'checkups',
      'checkup_services' => $lang ? __('permissions.group.checkup_services') : 'checkup_services',
      'checkup_radiology' => $lang ? __('permissions.group.checkup_radiology') : 'checkup_radiology',
      'hospitalizations' => $lang ? __('permissions.group.hospitalizations') : 'hospitalizations',
      'operations' => $lang ? __('permissions.group.operations') : 'operations',
      'medical_services' => $lang ? __('permissions.group.medical_services') : 'medical_services',
      'medicines' => $lang ? __('permissions.group.medicines') : 'medicines',
      'chronic_diseases' => $lang ? __('permissions.group.chronic_diseases') : 'chronic_diseases',
      'chronic_diseases_patient' => $lang ? __('permissions.group.chronic_diseases_patient') : 'chronic_diseases_patient',
      'prescriptions' => $lang ? __('permissions.group.prescriptions') : 'prescriptions',
      'suppliers' => $lang ? __('permissions.group.suppliers') : 'suppliers',
      'items_category' => $lang ? __('permissions.group.items_category') : 'items_category',
      'supply_request' => $lang ? __('permissions.group.supply_request') : 'supply_request',
      'inventory_items' => $lang ? __('permissions.group.inventory_items') : 'inventory_items',
      'inventory_transactions' => $lang ? __('permissions.group.inventory_transactions') : 'inventory_transactions',
      'insurance_societies' => $lang ? __('permissions.group.insurance_societies') : 'insurance_societies',
      'assurance' => $lang ? __('permissions.group.assurance') : 'assurance',
      'designations' => $lang ? __('permissions.group.designations') : 'designations',
      'bonuses' => $lang ? __('permissions.group.bonuses') : 'bonuses',
      'employees' => $lang ? __('permissions.group.employees') : 'employees',
      'contracts' => $lang ? __('permissions.group.contracts') : 'contracts',
      'attendances' => $lang ? __('permissions.group.attendances') : 'attendances',
      'loans' => $lang ? __('permissions.group.loans') : 'loans',
      'salaries' => $lang ? __('permissions.group.salaries') : 'salaries',
      'career_changes' => $lang ? __('permissions.group.career_changes') : 'career_changes',
      'transactions' => $lang ? __('permissions.group.transactions') : 'transactions',
      'accounting' => $lang ? __('permissions.group.accounting') : 'accounting',
    ];
  }

  public static function get_name(string $status):string
  {
    return self::all(true)[$status];
  }

  public static function get_group_name(string $status):string
  {
    return self::groups(true)[$status];
  }

  public static function get_resource(string $status):array
  {
    return [
      'value' => $status,
      'name' => self::get_name($status),
    ];
  }
}

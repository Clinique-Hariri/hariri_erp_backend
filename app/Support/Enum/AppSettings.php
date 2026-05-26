<?php

namespace App\Support\Enum;

use function Laravel\Prompts\select;

class AppSettings
{
  //dashboard
  const CHARGILY_PAY_MODE = 'chargily_pay_mode';
  const CHARGILY_PAY_PUBLIC_KEY = 'chargily_pay_public_key';
  const CHARGILY_PAY_SECRET_KEY = 'chargily_pay_secret_key';

  //ccp details
  const RIP = 'rip';
  const CCP = 'ccp';
  const CCP_KEY = 'ccp_key';
  const CCP_NAME = 'ccp_name';
  const CCP_ADDRESS = 'ccp_address';

  //social media
  const FACEBOOK = 'facebook';
  const TWITTER = 'twitter';
  const INSTAGRAM = 'instagram';
  const LINKEDIN = 'linkedin';
  const YOUTUBE = 'youtube';
  const WHATSAPP = 'whatsapp';
  const TELEGRAM = 'telegram';
  const TIKTOK = 'tiktok';

  public static function all():array
  {
    return [
      self::CHARGILY_PAY_MODE => self::CHARGILY_PAY_MODE,
      self::CHARGILY_PAY_PUBLIC_KEY => self::CHARGILY_PAY_PUBLIC_KEY,
      self::CHARGILY_PAY_SECRET_KEY => self::CHARGILY_PAY_SECRET_KEY,
      self::RIP => self::RIP,
      self::CCP => self::CCP,
      self::CCP_KEY => self::CCP_KEY,
      self::CCP_NAME => self::CCP_NAME,
      self::CCP_ADDRESS => self::CCP_ADDRESS,
      self::FACEBOOK => self::FACEBOOK,
      self::TWITTER => self::TWITTER,
      self::INSTAGRAM => self::INSTAGRAM,
      self::LINKEDIN => self::LINKEDIN,
      self::YOUTUBE => self::YOUTUBE,
      self::WHATSAPP => self::WHATSAPP,
      self::TELEGRAM => self::TELEGRAM,
      self::TIKTOK => self::TIKTOK,
    ];
  }

  public static function frontend_all():array
  {
    return [
      self::CHARGILY_PAY_MODE => self::CHARGILY_PAY_MODE,
      self::CHARGILY_PAY_PUBLIC_KEY => self::CHARGILY_PAY_PUBLIC_KEY,
      self::CHARGILY_PAY_SECRET_KEY => self::CHARGILY_PAY_SECRET_KEY,
      self::RIP => self::RIP,
      self::CCP => self::CCP,
      self::CCP_KEY => self::CCP_KEY,
      self::CCP_NAME => self::CCP_NAME,
      self::CCP_ADDRESS => self::CCP_ADDRESS,
      self::FACEBOOK => self::FACEBOOK,
      self::TWITTER => self::TWITTER,
      self::INSTAGRAM => self::INSTAGRAM,
      self::LINKEDIN => self::LINKEDIN,
      self::YOUTUBE => self::YOUTUBE,
      self::WHATSAPP => self::WHATSAPP,
      self::TELEGRAM => self::TELEGRAM,
      self::TIKTOK => self::TIKTOK,
    ];
  }

  public static function permission_slugs()
  {
    return [
      self::CHARGILY_PAY_MODE => 'chargily pay mode (live or test)',
      self::CHARGILY_PAY_PUBLIC_KEY => 'chargily pay public key',
      self::CHARGILY_PAY_SECRET_KEY => 'chargily pay secret key',
      self::RIP => 'RIP',
      self::CCP => 'CCP',
      self::CCP_KEY => 'CCP Key',
      self::CCP_NAME => 'CCP Name',
      self::CCP_ADDRESS => 'CCP Address',
      self::FACEBOOK => 'facebook',
      self::TWITTER => 'twitter',
      self::INSTAGRAM => 'instagram',
      self::LINKEDIN => 'linkedin',
      self::YOUTUBE => 'youtube',
      self::WHATSAPP => 'whatsapp',
      self::TELEGRAM => 'telegram',
      self::TIKTOK => 'tiktok',
    ];
  }

  public static function permission_arabic_slugs()
  {
    return [
      self::CHARGILY_PAY_MODE => 'وضع الدفع شرجيلي (live او test)',
      self::CHARGILY_PAY_PUBLIC_KEY => 'المفتاح العام شرجيلي',
      self::CHARGILY_PAY_SECRET_KEY => 'المفتاح السري شرجيلي',
      self::RIP => 'RIP',
      self::CCP => 'CCP',
      self::CCP_KEY => 'مفتاح CCP',
      self::CCP_NAME => 'اسم CCP',
      self::CCP_ADDRESS => 'عنوان CCP',
      self::FACEBOOK => 'فيسبوك',
      self::TWITTER => 'تويتر',
      self::INSTAGRAM => 'انستغرام',
      self::LINKEDIN => 'لينكد إن',
      self::YOUTUBE => 'يوتيوب',
      self::WHATSAPP => 'واتساب',
      self::TELEGRAM => 'تيليجرام',
      self::TIKTOK => 'تيك توك',
    ];
  }

  public static function permission_french_slugs()
  {
    return [
      self::CHARGILY_PAY_MODE => 'mode de paiement chargily (live ou test)',
      self::CHARGILY_PAY_PUBLIC_KEY => 'clé publique chargily',
      self::CHARGILY_PAY_SECRET_KEY => 'clé secrète chargily',
      self::RIP => 'RIP',
      self::CCP => 'CCP',
      self::CCP_KEY => 'clé CCP',
      self::CCP_NAME => 'nom CCP',
      self::CCP_ADDRESS => 'adresse CCP',
      self::FACEBOOK => 'facebook',
      self::TWITTER => 'twitter',
      self::INSTAGRAM => 'instagram',
      self::LINKEDIN => 'linkedin',
      self::YOUTUBE => 'youtube',
      self::WHATSAPP => 'whatsapp',
      self::TELEGRAM => 'telegram',
      self::TIKTOK => 'tiktok',
    ];
  }

  public static function get_slug($permission)
  {
    return app()->getLocale() == 'ar' ? self::permission_arabic_slugs()[$permission] :
      (app()->getLocale() == 'fr' ? self::permission_french_slugs()[$permission] :
        self::permission_slugs()[$permission]);
  }
}

<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Third Party Services
  |--------------------------------------------------------------------------
  |
  | This file is for storing the credentials for third party services such
  | as Mailgun, Postmark, AWS and more. This file provides the de facto
  | location for this type of information, allowing packages to have
  | a conventional file to locate the various service credentials.
  |
  */

  'postmark' => [
    'token' => env('POSTMARK_TOKEN'),
  ],

  'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
  ],

  'slack' => [
    'notifications' => [
      'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
      'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
    ],
  ],
  'twilio' => [
    'sid' => env('TWILIO_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'service_sid' => env('TWILIO_SERVICE_SID'),
    'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
    'content_sid' => env('TWILIO_CONTENT_SID'),
  ],
  'firebase' => [
    'credentials' => env('FIREBASE_CREDENTIALS', base_path('clinique-hariri-intl-firebase-adminsdk-fbsvc-9d33c19818.json')),
  ],

  'clinic' => [
    'contact' => env('CLINIC_CONTACT', '+213000000000'),
  ],

  'airtel_sms' => [
    'url' => env('AIRTEL_SMS_URL', 'https://www.airtel.td/gateway/v1/sendDefaultSms'),
    'token' => env('AIRTEL_SMS_TOKEN'),
    'username' => env('AIRTEL_SMS_USERNAME'),
    'password' => env('AIRTEL_SMS_PASSWORD'),
    'customer_id' => env('AIRTEL_SMS_CUSTOMER_ID'),
    'sender_id' => env('AIRTEL_SMS_SENDER_ID', 'ARTL'),
  ],


];

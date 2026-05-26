<?php
namespace App\Traits;

use App\Models\Setting;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\FirebaseException;

trait RandomTrait
{

  public function random($length, $pattern = 'alphanumeric') {
    $patterns = [
        'alphanumeric' => 'a-zA-Z0-9',
        'letters' => 'a-zA-Z',
        'numbers' => '0-9',
        'lowercase' => 'a-z',
        'uppercase' => 'A-Z',
        'hex' => 'a-fA-F0-9',
        'uppercase_alphanumeric' => 'A-Z0-9'
    ];

    $chars = $patterns[$pattern] ?? $pattern;
    $result = '';

    while (strlen($result) < $length) {

        $random = base64_encode(random_bytes($length * 3));
        $filtered = preg_replace('/[^' . $chars . ']/', '', $random);
        $result .= $filtered;
    }

    return substr($result, 0, $length);
}
}

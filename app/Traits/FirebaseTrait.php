<?php
namespace App\Traits;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\FirebaseException;

trait FirebaseTrait
{
  protected function firestore($file, $filename)
  {

    try {
      $storage = Firebase::storage();
      $storageClient = $storage->getStorageClient();
      $defaultBucket = $storage->getBucket();

      $object = $defaultBucket->upload(
        $file,
        [
          'predefinedAcl' => 'publicRead',
          'name' => $filename,
        ]
      );

      $url = 'https://storage.googleapis.com/' . $object->info()['bucket'] . '/' . $object->info()['name'];
      return $url;

    } catch (FirebaseException $e) {
      return $e;
    }
  }

  protected function sendToDevice($title, $content, $fcm_token)
  {
    try {
      $messaging = Firebase::messaging();

      $notification = \Kreait\Firebase\Messaging\Notification::fromArray([
        'title' => $title,
        'body' => $content,
        //'image' => $imageUrl,
      ]);

      if ($fcm_token) {

        $message = CloudMessage::withTarget('token', $fcm_token)
          ->withNotification($notification) // optional
          //->withData($data) // optional
        ;

        $messaging->send($message);
      }

      return;
    } catch (FirebaseException $e) {
      return $e;
    }


  }
  protected function sendToDevices($title, $content, $fcm_tokens)
  {
    try {
      $messaging = Firebase::messaging();

      $notification = \Kreait\Firebase\Messaging\Notification::fromArray([
        'title' => $title,
        'body' => $content,
        //'image' => $imageUrl,
      ]);

      $message = CloudMessage::new()
        ->withNotification($notification) // optional
        //->withData($data) // optional
      ;

      $messaging->sendMulticast($message, $fcm_tokens);

      return;
    } catch (FirebaseException $e) {
      return $e;
    }

  }

  protected function getRealtimeData($reference)
  {
    $factory = (new Factory)
      ->withServiceAccount(base_path(env('GOOGLE_APPLICATION_CREDENTIALS')))
      ->withDatabaseUri('https://gs-retour-default-rtdb.firebaseio.com');

    $database = $factory->createDatabase();
    $reference = $database->getReference($reference);
    $snapshot = $reference->getSnapshot()->getValue();
    return $snapshot;
  }

  protected function generateIdToken($uid){

      $auth = Firebase::auth();
      $token = $auth->createCustomToken($uid);
      $response = Http::withUrlParameters(['API_KEY' => env('FIREBASE_API_KEY')])
      ->post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken?key={API_KEY}', [
          'token' => $token->toString(),
          'returnSecureToken' => true
        ]);

      if ($response->ok()) {
        return $response->json()['idToken'];
      }
  }

  protected function getFirebaseUser($identifier){
    $auth = Firebase::auth();

    if (is_string($identifier) && strpos($identifier, '.') !== false) {
      $verifiedIdToken = $auth->verifyIdToken($identifier);
      $uid = $verifiedIdToken->claims()->get('sub');
    } else {
      $uid = $identifier;
    }

    return $auth->getUser($uid);
  }
}

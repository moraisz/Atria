<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\MercureMessage;
use App\Models\User;
use Atria\Http\AbstractClasses\Controller;
use Atria\Http\Response;
use Atria\Modules\Mercure\MercureManager;
use Atria\Modules\Mercure\MercurePublisher;

class HomeController extends Controller
{
    public function __construct(
        private MercurePublisher $mercurePublisher,
        private MercureManager $mercureManager,
    ) {}

    public function get(): Response
    {
        $userId = $this->request->getAttribute('auth_user_id');

        if (!is_int($userId)) {
            return $this->redirect('/login');
        }

        $user = User::findById($userId);
        $mercureTopic = "users/{$userId}";
        $mercureHistory = array_map(static fn(array $message): array => [
            'id' => $message['id'],
            'user_id' => $message['user_id'],
            'message' => $message['payload'],
            'created_at' => $message['created_at'],
            'type' => $message['event_type'],
        ], MercureMessage::forUser($userId));

        $this->response
            ->mercure()
            ->mercureAuthorization($mercureTopic);

        return $this->renderView('pages/home', [
            'title' => 'Home',
            'message' => 'Bem-vindo!',
            'user' => $user,
            'mercureTopic' => $mercureTopic,
            'mercureSubscribeUrl' => $this->mercureManager->subscribeUrl($mercureTopic),
            'mercureDataExample' => 'Hello from Atria',
            'mercureHistory' => $mercureHistory,
        ]);
    }

    public function publishMercure(): Response
    {
        $userId = $this->request->getAttribute('auth_user_id');

        if (!is_int($userId)) {
            return $this->redirect('/login');
        }

        $topic = "users/{$userId}";
        $eventType = 'user.message';
        $message = MercureMessage::record(
            $userId,
            $topic,
            $this->request->bodyString('data'),
            $eventType,
        );
        $messageId = $message['id'] ?? null;

        if (!is_int($messageId) && !(is_string($messageId) && ctype_digit($messageId))) {
            throw new \RuntimeException('Persisted Mercure messages must have a numeric id.');
        }

        $data = json_encode([
            'id' => $messageId,
            'user_id' => $message['user_id'],
            'message' => $message['payload'],
            'created_at' => $message['created_at'],
        ], JSON_THROW_ON_ERROR);

        $this->mercurePublisher->publish(
            $topic,
            $data,
            true,
            (string) $messageId,
            $eventType,
        );
        MercureMessage::markPublished((int) $messageId);

        return $this->response->setStatusCode(204);
    }
}

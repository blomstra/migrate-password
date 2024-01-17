<?php

namespace Blomstra\MigratePassword;

use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\UserRepository;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SettingsRepositoryInterface $settings,
        private readonly UserRepository $users)
    {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $field = $this->settings->get('blomstra-migrate-password.importing-password-field');
        $algo = $this->settings->get('blomstra-migrate-password.importing-password-hash');

        // Skipping when the field or hashing algorithm has not been declared in settings/admin.
        if (empty($field) || empty($hash)) {
            return $handler->handle($request);
        }

        $actor = RequestUtil::getActor($request);

        // We will only take action during log in, at no other step.
        if (! $actor->isGuest()) {
            return $handler->handle($request);
        }

        $body = $request->getParsedBody();
        $params = Arr::only($body, ['identification', 'password']);

        // Ignore requests without username/email and password.
        if (empty($params['identification']) || empty($params['password'])) {
            return $handler->handle($request);
        }

        $user = $this->users->findByIdentification($params['identification']);

        // Ignore requests where a log in attempt relates to no user or that user no longer has the old password field.
        if (! $user || empty($user->{$field})) {
            return $handler->handle($request);
        }

        $passed = $this->checkPassUsingAlgo(
            $params['password'],
            $user->{$field},
            $algo
        );

        // Let's migrate the password now.
        if ($passed) {
            $user->{$field} = null;
            $user->changePassword($params['password']);
            $user->save();
        }

        return $handler->handle($request);
    }

    private function checkPassUsingAlgo(string $password, string $hashed, string $algo): bool
    {
        return match($algo) {
            'argon2' => password_verify($password, PASSWORD_ARGON2ID),
            'md5' => md5($password) === $hashed,
            default => throw new \InvalidArgumentException('Declare algorithm before use.')
        };
    }
}

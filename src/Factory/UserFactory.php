<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {

        return [
            'email' => self::faker()->email(),
            'password' => 'password',
            'roles' => ['ROLE_USER'],
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user) {
                $hashedPassword = $this->userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
            });
    }
}

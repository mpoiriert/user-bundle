<?php

namespace Draw\Bundle\UserBundle\Tests\DependencyInjection;

use Draw\Bundle\UserBundle\EventListener\EncryptPasswordUserEntityListener;
use Draw\Bundle\UserBundle\Tests\Fixtures\Entity\User;

class DrawUserExtensionWithoutEncryptPasswordListenerTest extends DrawUserExtensionTest
{
    public function getConfiguration(): array
    {
        return [
            'user_entity_class' => User::class,
            'encrypt_password_listener' => false,
        ];
    }

    public static function provideTestHasServiceDefinition(): iterable
    {
        yield from static::removeProvidedService(
            [
                EncryptPasswordUserEntityListener::class,
            ],
            parent::provideTestHasServiceDefinition()
        );
    }
}

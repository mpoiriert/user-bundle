<?php

namespace Draw\Bundle\UserBundle\Tests\DependencyInjection;

use Draw\Bundle\UserBundle\PasswordChangeEnforcer\Listener\PasswordChangeEnforcerSubscriber;
use Draw\Bundle\UserBundle\PasswordChangeEnforcer\MessageHandler\PasswordChangeRequestedMessageHandler;
use Draw\Bundle\UserBundle\Tests\Fixtures\Entity\User;

class DrawUserExtensionWithPasswordChangeEnforcerEnabledTest extends DrawUserExtensionTest
{
    public function getConfiguration(): array
    {
        return [
            'user_entity_class' => User::class,
            'password_change_enforcer' => [
                'enabled' => true,
                'change_password_route' => 'test-route',
            ],
        ];
    }

    public function provideTestHasServiceDefinition(): iterable
    {
        yield from parent::provideTestHasServiceDefinition();
        yield [PasswordChangeEnforcerSubscriber::class];
        yield [PasswordChangeRequestedMessageHandler::class];
    }

    public function testTwoFactorAuthenticationSubscriber(): void
    {
        $this->assertSame(
            'test-route',
            $this->getContainerBuilder()
                ->getDefinition(PasswordChangeEnforcerSubscriber::class)
                ->getArgument('$changePasswordRoute')
        );
    }
}

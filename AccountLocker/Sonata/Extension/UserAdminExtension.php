<?php

namespace Draw\Bundle\UserBundle\AccountLocker\Sonata\Extension;

use Draw\Bundle\UserBundle\AccountLocker\Sonata\Controller\RefreshUserLockController;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdminExtension extends AbstractAdminExtension
{
    public function getAccessMapping(AdminInterface $admin)
    {
        return [
            'refresh-user-locks' => 'MASTER',
        ];
    }

    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add(
            'refresh-user-locks',
            $admin->getRouterIdParameter().'/refresh-user-locks',
            ['_controller' => RefreshUserLockController::class.':refreshUserLocksAction']
        );
    }

    public function configureActionButtons(AdminInterface $admin, $list, $action, $object): array
    {
        switch (true) {
            case !$admin->isGranted('refresh-user-locks', $object):
            case !in_array($action, ['edit', 'show']):
                break;
            default:
                $list['refresh-user-lock'] = [
                    'template' => '@DrawUser/AccountLocker/Sonata/refresh_user_locks_button.html.twig',
                ];
        }

        return $list;
    }
}
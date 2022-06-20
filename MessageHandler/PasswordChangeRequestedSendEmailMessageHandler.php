<?php

namespace Draw\Bundle\UserBundle\MessageHandler;

use Doctrine\ORM\EntityRepository;
use Draw\Bundle\UserBundle\Email\PasswordChangeRequestedEmail;
use Draw\Bundle\UserBundle\Entity\PasswordChangeUserInterface;
use Draw\Bundle\UserBundle\Message\PasswordChangeRequestedMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordChangeRequestedSendEmailMessageHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;

    /**
     * @var EntityRepository<UserInterface>
     */
    private EntityRepository $userEntityRepository;

    public static function getHandledMessages(): iterable
    {
        yield PasswordChangeRequestedMessage::class => 'handlePasswordChangeRequestedMessage';
    }

    /**
     * @param EntityRepository<UserInterface> $drawUserEntityRepository
     */
    public function __construct(EntityRepository $drawUserEntityRepository, MailerInterface $mailer)
    {
        $this->userEntityRepository = $drawUserEntityRepository;
        $this->mailer = $mailer;
    }

    public function __invoke(PasswordChangeRequestedMessage $message): void
    {
        $user = $this->userEntityRepository->find($message->getUserId());

        if (!$user instanceof PasswordChangeUserInterface || $user->getNeedChangePassword()) {
            return;
        }

        if (!method_exists($user, 'getEmail') || empty($user->getEmail)) {
            return;
        }

        $this->mailer->send((new PasswordChangeRequestedEmail())->setUserId($user->getId()));
    }
}

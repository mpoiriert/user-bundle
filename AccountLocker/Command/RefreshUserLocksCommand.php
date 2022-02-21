<?php

namespace Draw\Bundle\UserBundle\AccountLocker\Command;

use Doctrine\ORM\EntityRepository;
use Draw\Bundle\UserBundle\AccountLocker\Message\RefreshUserLockMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class RefreshUserLocksCommand extends Command
{
    private $entityRepository;

    private $messageBus;

    public function __construct(
        MessageBusInterface $messageBus,
        EntityRepository $drawUserEntityRepository
    ) {
        $this->messageBus = $messageBus;
        $this->entityRepository = $drawUserEntityRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('draw:user:refresh-user-locks')
            ->setDescription(
                'Send a [RefreshUserLockMessage] for all user. 
                Configure you messenger routing properly otherwise it will be sync'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Sending [RefreshUserLockMessage] messages');

        $progressBar = $io->createProgressBar($this->entityRepository->count([]));

        $rows = $this->entityRepository->createQueryBuilder('user')
            ->select('user.id')
            ->getQuery()
            ->execute();

        foreach ($rows as $row) {
            $this->messageBus->dispatch(new RefreshUserLockMessage($row['id']));
            $progressBar->advance();
        }

        $io->newLine(2);

        $io->success('Messages sent!');

        return 0;
    }
}
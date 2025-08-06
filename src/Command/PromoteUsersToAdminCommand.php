<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:promote-users-to-admin',
    description: 'Macht alle existierenden Benutzer zu Administratoren',
)]
class PromoteUsersToAdminCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Macht alle existierenden Benutzer zu Administratoren')
            ->setHelp('Dieser Befehl fügt allen existierenden Benutzern die ROLE_ADMIN Rolle hinzu.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Zeigt nur an, was passieren würde, ohne Änderungen zu speichern')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Führt die Änderungen ohne Bestätigung aus')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $isDryRun = $input->getOption('dry-run');
        $isForced = $input->getOption('force');

        $io->title('🚀 Benutzer zu Administratoren befördern');

        // Alle Benutzer laden
        $users = $this->userRepository->findAll();

        if (empty($users)) {
            $io->warning('Keine Benutzer in der Datenbank gefunden.');
            return Command::SUCCESS;
        }

        $io->section('📋 Gefundene Benutzer:');

        $usersToPromote = [];
        $alreadyAdmins = [];

        foreach ($users as $user) {
            $roles = $user->getRoles();
            $isAdmin = in_array('ROLE_ADMIN', $roles);

            $statusIcon = $isAdmin ? '👑' : '👤';
            $statusText = $isAdmin ? 'bereits Admin' : 'wird befördert';

            $io->writeln(sprintf(
                '%s %s (%s) - %s',
                $statusIcon,
                $user->getEmail(),
                implode(', ', $roles),
                $statusText
            ));

            if ($isAdmin) {
                $alreadyAdmins[] = $user;
            } else {
                $usersToPromote[] = $user;
            }
        }

        if (empty($usersToPromote)) {
            $io->success('Alle Benutzer sind bereits Administratoren! 🎉');
            return Command::SUCCESS;
        }

        $io->section('📊 Zusammenfassung:');
        $io->writeln([
            sprintf('👤 Benutzer gesamt: %d', count($users)),
            sprintf('👑 Bereits Admins: %d', count($alreadyAdmins)),
            sprintf('🚀 Zu befördern: %d', count($usersToPromote)),
        ]);

        if ($isDryRun) {
            $io->note('DRY-RUN: Keine Änderungen werden gespeichert.');
            $io->success('Simulation abgeschlossen. Verwende --force um die Änderungen anzuwenden.');
            return Command::SUCCESS;
        }

        // Bestätigung einholen (außer bei --force)
        if (!$isForced) {
            $confirm = $io->confirm(
                sprintf('Möchtest du %d Benutzer zu Administratoren befördern?', count($usersToPromote)),
                false
            );

            if (!$confirm) {
                $io->info('Vorgang abgebrochen.');
                return Command::FAILURE;
            }
        }

        // Benutzer befördern
        $io->section('🚀 Beförderung läuft...');
        $io->progressStart(count($usersToPromote));

        $promotedCount = 0;

        foreach ($usersToPromote as $user) {
            try {
                // ROLE_ADMIN hinzufügen (ROLE_USER bleibt erhalten)
                $currentRoles = $user->getRoles();
                if (!in_array('ROLE_ADMIN', $currentRoles)) {
                    $currentRoles[] = 'ROLE_ADMIN';
                    $user->setRoles($currentRoles);

                    $this->entityManager->persist($user);
                    $promotedCount++;

                    $io->writeln(sprintf(' ✅ %s zu Admin befördert', $user->getEmail()));
                }

                $io->progressAdvance();
            } catch (\Exception $e) {
                $io->error(sprintf('Fehler bei %s: %s', $user->getEmail(), $e->getMessage()));
            }
        }

        if ($promotedCount > 0) {
            $this->entityManager->flush();
            $io->progressFinish();

            $io->newLine(2);
            $io->success([
                sprintf('🎉 %d Benutzer erfolgreich zu Administratoren befördert!', $promotedCount),
                'Alle Benutzer haben jetzt ROLE_ADMIN Berechtigung.'
            ]);
        } else {
            $io->warning('Keine Benutzer wurden befördert.');
        }

        return Command::SUCCESS;
    }
}

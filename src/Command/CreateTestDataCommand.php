<?php

namespace App\Command;

use App\Entity\CampaignStand;
use App\Entity\Participant;
use App\Entity\StandParticipation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-test-data',
    description: 'Erstellt Testdaten für Wahlkampfstände und Teilnehmer',
)]
class CreateTestDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Erstelle Testdaten für Standplaner');

        // Teilnehmer erstellen
        $participants = $this->createParticipants();
        $io->success('✅ ' . count($participants) . ' Teilnehmer erstellt');

        // Wahlkampfstände erstellen
        $stands = $this->createCampaignStands();
        $io->success('✅ ' . count($stands) . ' Wahlkampfstände erstellt');

        // Teilnahmen zuordnen
        $participations = $this->createParticipations($stands, $participants);
        $io->success('✅ ' . count($participations) . ' Teilnahmen zugeordnet');

        $this->entityManager->flush();

        $io->success('🎉 Alle Testdaten erfolgreich erstellt!');

        $io->section('Übersicht der erstellten Daten:');
        foreach ($stands as $stand) {
            $io->writeln(sprintf(
                '📍 <info>%s</info> am %s (%d Teilnehmer)',
                $stand->getDistrict(),
                $stand->getStartTime()->format('d.m.Y H:i'),
                $stand->getParticipations()->count()
            ));
        }

        return Command::SUCCESS;
    }

    private function createParticipants(): array
    {
        $names = [
            'Anna Müller',
            'Max Mustermann',
            'Sarah Schmidt',
            'Tom Wagner',
            'Lisa Weber',
            'David Klein',
            'Julia Fischer',
            'Michael Becker',
            'Sophie Richter',
            'Lukas Neumann',
            'Emma Braun',
            'Felix Hoffmann',
            'Lena Koch',
            'Jonas Schulz',
            'Mia Zimmermann'
        ];

        $participants = [];
        foreach ($names as $name) {
            $participant = new Participant();
            $participant->setName($name);
            $this->entityManager->persist($participant);
            $participants[] = $participant;
        }

        return $participants;
    }

    private function createCampaignStands(): array
    {
        $standData = [
            [
                'district' => 'Mitte',
                'address' => 'Alexanderplatz 1, 10178 Berlin',
                'start' => '2025-08-15 10:00:00',
                'end' => '2025-08-15 14:00:00'
            ],
            [
                'district' => 'Kreuzberg',
                'address' => 'Bergmannstraße 25, 10961 Berlin',
                'start' => '2025-08-16 09:00:00',
                'end' => '2025-08-16 13:00:00'
            ],
            [
                'district' => 'Prenzlauer Berg',
                'address' => 'Kollwitzplatz, 10435 Berlin',
                'start' => '2025-08-17 11:00:00',
                'end' => '2025-08-17 15:00:00'
            ],
            [
                'district' => 'Friedrichshain',
                'address' => 'Boxhagener Platz, 10245 Berlin',
                'start' => '2025-08-18 08:30:00',
                'end' => '2025-08-18 12:30:00'
            ],
            [
                'district' => 'Charlottenburg',
                'address' => 'Savignyplatz 6, 10623 Berlin',
                'start' => '2025-08-19 12:00:00',
                'end' => '2025-08-19 16:00:00'
            ],
            [
                'district' => 'Neukölln',
                'address' => 'Hermannplatz, 12051 Berlin',
                'start' => '2025-08-20 10:30:00',
                'end' => '2025-08-20 14:30:00'
            ]
        ];

        $stands = [];
        foreach ($standData as $data) {
            $stand = new CampaignStand();
            $stand->setDistrict($data['district']);
            $stand->setAddress($data['address']);
            $stand->setStartTime(new \DateTimeImmutable($data['start']));
            $stand->setEndTime(new \DateTimeImmutable($data['end']));

            $this->entityManager->persist($stand);
            $stands[] = $stand;
        }

        return $stands;
    }

    private function createParticipations(array $stands, array $participants): array
    {
        $participations = [];
        $statuses = [
            StandParticipation::STATUS_ATTENDING,
            StandParticipation::STATUS_MAYBE,
            StandParticipation::STATUS_NOT_ATTENDING
        ];

        foreach ($stands as $stand) {
            // Zufällige Anzahl von Teilnehmern pro Stand (3-8)
            $participantCount = rand(3, 8);
            $selectedParticipants = array_rand($participants, $participantCount);

            // Sicherstellen, dass wir ein Array haben
            if (!is_array($selectedParticipants)) {
                $selectedParticipants = [$selectedParticipants];
            }

            foreach ($selectedParticipants as $participantIndex) {
                $participant = $participants[$participantIndex];

                // Höhere Wahrscheinlichkeit für "attending" (60%), dann "maybe" (30%), dann "not_attending" (10%)
                $rand = rand(1, 100);
                if ($rand <= 60) {
                    $status = StandParticipation::STATUS_ATTENDING;
                } elseif ($rand <= 90) {
                    $status = StandParticipation::STATUS_MAYBE;
                } else {
                    $status = StandParticipation::STATUS_NOT_ATTENDING;
                }

                $participation = new StandParticipation();
                $participation->setParticipant($participant);
                $participation->setStatus($status);

                // Verwende die addParticipation Methode für korrekte bidirektionale Beziehung
                $stand->addParticipation($participation);

                $this->entityManager->persist($participation);
                $participations[] = $participation;
            }
        }

        return $participations;
    }
}

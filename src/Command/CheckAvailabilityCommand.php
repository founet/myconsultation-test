<?php

namespace App\Command;

use App\Entity\Address;
use App\Entity\Reason;
use App\Entity\AvailabilitySlot;
use App\Entity\SlotReason;
use App\Service\AvailabilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:check-availability')]
class CheckAvailabilityCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private AvailabilityService $availabilityService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Création de données de test
        $address = new Address();
        $address->setName('Cabinet Test');
        $this->em->persist($address);

        $reason = new Reason();
        $reason->setName('Consultation générale');
        $reason->setDurationMinutes(30);
        $this->em->persist($reason);

        $slot = new AvailabilitySlot();
        $slot->setAddress($address);
        $slot->setWeekdays(['wednesday', 'thursday']);
        $slot->setStartTime(new \DateTimeImmutable('08:00'));
        $slot->setEndTime(new \DateTimeImmutable('12:00'));
        $this->em->persist($slot);

        $slotReason = new SlotReason();
        $slotReason->setAvailabilitySlot($slot);
        $slotReason->setReason($reason);
        $this->em->persist($slotReason);

        $this->em->flush();

        // Lancer le calcul
        $output->writeln('Recherche de créneau...');
        $slot = $this->availabilityService->getNextAvailableSlot($address->getId(), $reason->getId());

        if ($slot) {
            $output->writeln('✅ Prochaine disponibilité : ' . $slot->format('Y-m-d H:i'));
        } else {
            $output->writeln('❌ Aucune disponibilité trouvée.');
        }

        return Command::SUCCESS;
    }
}


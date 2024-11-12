<?php

namespace App\DataFixtures;

use App\Entity\Quota;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuotaFixtures extends Fixture
{
	public function load(ObjectManager $manager): void
	{
		$quotas = [
			['nom' => 'Starter', 'sim5Quota' => '20', 'sim10Quota' => '20', 'sim15Quota' => '20', 'sim20Quota' => '20', 'code' => 'first'],
			['nom' => 'Advanced', 'sim5Quota' => '30', 'sim10Quota' => '30', 'sim15Quota' => '30', 'sim20Quota' => '30', 'code' => 'second'],
			['nom' => 'Premium', 'sim5Quota' => '40', 'sim10Quota' => '40', 'sim15Quota' => '40', 'sim20Quota' => '40', 'code' => 'third'],
			['nom' => 'Suprême', 'sim5Quota' => '50', 'sim10Quota' => '50', 'sim15Quota' => '50', 'sim20Quota' => '50', 'code' => 'fourth'],
		];
		foreach ($quotas as $quotaData) {
			$quota = new Quota();
			$quota->setNom($quotaData['nom']);
			$quota->setSim5Quota($quotaData['sim5Quota']);
			$quota->setSim10Quota($quotaData['sim10Quota']);
			$quota->setSim15Quota($quotaData['sim15Quota']);
			$quota->setSim20Quota($quotaData['sim20Quota']);
			$quota->setCode($quotaData['code']);
			$manager->persist($quota);
		}

		$manager->flush();
	}
}

<?php
// src/Service/DistanceCalculator.php

namespace App\Service;

class DistanceCalculator
{
	const EARTH_RADIUS_KM = 6371;

	/**
	 * Calcule la distance entre deux points géographiques en kilomètres.
	 *
	 * @param float $lat1 Latitude du premier point
	 * @param float $lon1 Longitude du premier point
	 * @param float $lat2 Latitude du deuxième point
	 * @param float $lon2 Longitude du deuxième point
	 * @return float Distance en kilomètres
	 */
	public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
	{
		$dLat = deg2rad($lat2 - $lat1);
		$dLon = deg2rad($lon2 - $lon1);

		$a = sin($dLat / 2) * sin($dLat / 2) +
			cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
			sin($dLon / 2) * sin($dLon / 2);

		$c = 2 * asin(sqrt($a));

		return self::EARTH_RADIUS_KM * $c;
	}
}

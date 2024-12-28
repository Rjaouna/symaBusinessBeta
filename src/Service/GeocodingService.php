<?php
// src/Service/GeocodingService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
	private $client;
	private $baseUrl = 'https://nominatim.openstreetmap.org/search';

	public function __construct(HttpClientInterface $client)
	{
		$this->client = $client;
	}

	/**
	 * Géocode une adresse et retourne les coordonnées.
	 *
	 * @param string $address
	 * @return array|null
	 */
	public function geocode(string $address): ?array
	{
		$response = $this->client->request('GET', $this->baseUrl, [
			'query' => [
				'q' => $address,
				'format' => 'json',
				'limit' => 1,
			],
			'headers' => [
				'User-Agent' => 'SymfonyApp/1.0', // Important pour Nominatim
			],
		]);

		$data = $response->toArray();

		if (count($data) === 0) {
			return null;
		}

		return [
			'latitude' => (float) $data[0]['lat'],
			'longitude' => (float) $data[0]['lon'],
		];
	}
}

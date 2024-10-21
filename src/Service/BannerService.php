<?php
// src/Service/BannerService.php
namespace App\Service;

use App\Repository\BannerRepository;

class BannerService
{
	private $bannerRepository;

	public function __construct(BannerRepository $bannerRepository)
	{
		$this->bannerRepository = $bannerRepository;
	}

	public function getBanners()
	{
		return $this->bannerRepository->findAll();
	}
}

<?php


namespace App\Controller;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BaseController extends AbstractController
{
    protected $cacheManager;
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }


}
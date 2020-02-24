<?php


namespace App\Controller;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\AsciiSlugger;


class BaseController extends AbstractController
{
    protected $cacheManager;
    /**
     * @var AsciiSlugger
     */
    protected $slugger;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        $this->slugger = new AsciiSlugger();
    }


}
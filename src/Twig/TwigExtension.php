<?php


namespace App\Twig;


use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    private $imageDir;
    private $cacheManager;

    public function __construct(CacheManager $cacheManager, string $imageDir)
    {
        $this->imageDir = $imageDir;
        $this->cacheManager = $cacheManager;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('my_imagine_filter', array($this, 'myImagineFilter')),
        );
    }

    public function myImagineFilter($path, $filter, array $runtimeConfig = array(), $resolver = null)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext === "svg") {
            return $this->imageDir . "/" . basename($path);
        } else {
            return $this->cacheManager->getBrowserPath($path, $filter, $runtimeConfig, $resolver);
        }

    }
}
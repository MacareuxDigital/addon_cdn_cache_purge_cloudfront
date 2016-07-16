<?php
namespace Concrete\Package\CdnCachePurgeCloudfront;

use Package;
use Events;
use Concrete\Package\CdnCachePurgeCloudfront\Cache\CloudFrontCache;
use Concrete\Core\Backup\ContentImporter;

class Controller extends Package
{
    protected $pkgHandle = 'cdn_cache_purge_cloudfront';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion = '0.9';
    protected $phpVersionRequired = '5.5.0';

    /**
     * @var bool Remove \Src from package namespace.
     */
    protected $pkgAutoloaderMapCoreExtensions = true;

    public function getPackageDescription()
    {
        return t("Flushes Amazon CloudFront cache when you click Clear Cache button.");
    }

    public function getPackageName()
    {
        return t("CDN Cache Purge for CloudFront");
    }

    public function on_start()
    {
        $this->registerAutoload();

        Events::addListener('on_cache_flush', function () {
            $base_path = Core::getApplicationURL() . '/';
            $cloudfront = new CloudFrontCache();
            $cloudfront->createInvalidationRequest(array(
                $base_path . '*'
            ));
        });
    }

    protected function registerAutoload()
    {
        require $this->getPackagePath() . '/vendor/autoload.php';
    }

    public function install()
    {
        if (version_compare(PHP_VERSION, $this->phpVersionRequired, '<')) {
            throw new Exception(t('This package requires PHP %s or greater.', $this->phpVersionRequired));
        }
        if (! file_exists($this->getPackagePath() . '/vendor/autoload.php')) {
            throw new Exception(t('Required libraries not found.'));
        }
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/config/dashboard.xml');
        return $pkg;
    }
}

<?php
namespace Concrete\Package\CdnCachePurgeCloudfront;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Package\CdnCachePurgeCloudfront\Cache\CloudFrontCache;
use Concrete\Package\CdnCachePurgeCloudfront\Cache\CloudFrontCacheV2;
use Core;
use Events;
use Package;

/**
 * Class Controller
 * @package Concrete\Package\CdnCachePurgeCloudfront
 */
class Controller extends Package
{
    /**
     * @var string Package handle.
     */
    protected $pkgHandle = 'cdn_cache_purge_cloudfront';

    /**
     * @var string Required concrete5 version.
     */
    protected $appVersionRequired = '5.7.5';

    /**
     * @var string Package version.
     */
    protected $pkgVersion = '0.9.3';

    /**
     * @var string Required PHP version.
     */
    protected $phpVersionRequired = '5.5.0';

    /**
     * @var bool Remove \Src from package namespace.
     */
    protected $pkgAutoloaderMapCoreExtensions = true;

    /**
     * Returns the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return t("Flushes Amazon CloudFront cache when you click Clear Cache button.");
    }

    /**
     * Returns the translated package name.
     *
     * @return string
     */
    public function getPackageName()
    {
        return t("CDN Cache Purge for CloudFront");
    }

    /**
     * Startup process of the package.
     */
    public function on_start()
    {
        $cloudfront = $this->getHelper();

        Events::addListener('on_cache_flush', function () use ($cloudfront) {
            $base_path = Core::getApplicationRelativePath() . '/';
            $cloudfront->createInvalidationRequest(array(
                $base_path . '*',
            ));
        });
    }

    /**
     * Get CloudFrontCache Helper Class
     *
     * @return CloudFrontCache|CloudFrontCacheV2
     */
    protected function getHelper()
    {
        // If other package (e.g. S3 Storage) uses api version v2, avoid autoloading v3 sdk
        if ($this->getFileConfig()->get('aws.cloudfront.api_version') == 2) {
            $cloudfront = new CloudFrontCacheV2();
        } else {
            if ($this->getFileConfig()->get('aws.cloudfront.autoload') !== 'disable') {
                $this->registerAutoload();
            }

            $cloudfront = new CloudFrontCache();
        }

        return $cloudfront;
    }

    /**
     * Register autoloader
     */
    protected function registerAutoload()
    {
        require $this->getPackagePath() . '/vendor/autoload.php';
    }

    /**
     * Install process of the package.
     */
    public function install()
    {
        if (version_compare(PHP_VERSION, $this->phpVersionRequired, '<')) {
            throw new Exception(t('This package requires PHP %s or greater.', $this->phpVersionRequired));
        }
        if (!file_exists($this->getPackagePath() . '/vendor/autoload.php')) {
            throw new Exception(t('Required libraries not found.'));
        }
        $pkg = parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/config/dashboard.xml');

        return $pkg;
    }
}

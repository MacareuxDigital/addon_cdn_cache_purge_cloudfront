<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class CdnCachePurgeCloudfrontPackage extends Package
{
    protected $pkgHandle = 'cdn_cache_purge_cloudfront';
    protected $appVersionRequired = '5.6.2';
    protected $pkgVersion = '0.1';
    protected $phpVersionRequired = '5.5.0';

    public function getPackageDescription()
    {
        return t("Clears Cache via Amazon CloudFront API");
    }

    public function getPackageName()
    {
        return t("CDN Cache Purge for CloudFront");
    }

    public function on_start()
    {
        $this->registerAutoload();
        
        Events::extend('on_cache_flush', __CLASS__, 'clearCloudFrontCache', __FILE__);
    }

    protected function registerAutoload()
    {
        $classes = array(
            'CloudfrontCache' => array('library', 'cloudfront_cache', 'cdn_cache_purge_cloudfront')
        );
        Loader::registerAutoload($classes);

        require_once(__DIR__ . '/vendor/autoload.php');
    }

    public function install()
    {
        if (version_compare(PHP_VERSION, $this->phpVersionRequired, '<')) {
            throw new Exception(t('This package requires PHP %s or greater.', $this->phpVersionRequired));
        }
        if (! file_exists(__DIR__ . '/vendor/autoload.php')) {
            throw new Exception(t('Required libraries not found.'));
        }
        $pkg = parent::install();
        return $pkg;
    }
    
    public function clearCloudFrontCache($cache)
    {
        $cloudfront = new CloudfrontCache();
        $cloudfront->createInvalidationRequest(array(
            '/*'
        ));
    }
}

<?php
namespace Concrete\Package\CdnCachePurgeCloudfront\Cache;

use Aws\CloudFront\CloudFrontClient;
use Package;

class CloudFrontCache
{
    protected static $sdk_version = '2016-01-28';
    protected static $region = 'us-east-1';
    protected $client = null;

    public static function getSdkVersion()
    {
        return self::$sdk_version;
    }

    public static function getClient()
    {
        $pkg = Package::getByHandle('cdn_cache_purge_cloudfront');
        $accessKey = $pkg->getFileConfig()->get('aws.cloudfront.access_key');
        $accessSecret = $pkg->getFileConfig()->get('aws.cloudfront.access_secret');
        if ($accessKey && $accessSecret) {
            $cloudFront = new CloudFrontClient(array(
                'region' => self::$region,
                'version' => self::$sdk_version,
                'credentials' => array(
                    'key' => $accessKey,
                    'secret' => $accessSecret,
                ),
            ));

            return $cloudFront;
        }
    }

    public function __construct()
    {
        $client = static::getClient();
        if (is_object($client)) {
            $this->client = $client;
        }
    }

    public function createInvalidationRequest($paths = array())
    {
        $pkg = Package::getByHandle('cdn_cache_purge_cloudfront');
        $distributionId = $pkg->getFileConfig()->get('aws.cloudfront.distribution_id');
        if ($distributionId && is_object($this->client) && count($paths) > 0) {
            /** @var \Aws\Result $result */
            $result = $this->client->createInvalidation(array(
                'DistributionId' => AWS_CLOUDFRONT_DISTRIBUTION,
                'InvalidationBatch' => array(
                    'Paths' => array(
                        'Quantity' => count($paths),
                        'Items' => $paths,
                    ),
                    'CallerReference' => time(),
                ),
            ));

            return $result;
        }
    }
}

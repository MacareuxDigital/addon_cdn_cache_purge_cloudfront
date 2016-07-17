<?php
namespace Concrete\Package\CdnCachePurgeCloudfront\Cache;

use Aws\CloudFront\CloudFrontClient;
use Package;

/**
 * Class CloudFrontCache
 * @package Concrete\Package\CdnCachePurgeCloudfront\Cache
 */
class CloudFrontCacheV2
{
    /** @var string AWS Region */
    protected static $region = 'us-east-1';

    /** @var CloudFrontClient|null */
    protected $client = null;

    /**
     * Get supported AWS SDK version
     * @return string
     */
    public static function getSdkVersion()
    {
        return self::$sdk_version;
    }

    /**
     * Get AWS CloudFront Client instance
     * @return CloudFrontClient
     */
    public static function getClient()
    {
        $pkg = Package::getByHandle('cdn_cache_purge_cloudfront');
        $accessKey = $pkg->getFileConfig()->get('aws.cloudfront.access_key');
        $accessSecret = $pkg->getFileConfig()->get('aws.cloudfront.access_secret');
        if ($accessKey && $accessSecret) {
            $cloudFront = CloudFrontClient::factory(array(
                'region' => self::$region,
                'credentials' => array(
                    'key'    => $accessKey,
                    'secret' => $accessSecret,
                )
            ));

            return $cloudFront;
        }
    }

    /**
     * CloudFrontCache constructor.
     */
    public function __construct()
    {
        $client = static::getClient();
        if (is_object($client)) {
            $this->client = $client;
        }
    }

    /**
     * Create invalidation request for cloudfront
     * 
     * @param array $paths Paths for invalidation request
     * @return \Aws\Result
     */
    public function createInvalidationRequest($paths = array())
    {
        $pkg = Package::getByHandle('cdn_cache_purge_cloudfront');
        $distributionId = $pkg->getFileConfig()->get('aws.cloudfront.distribution_id');
        if ($distributionId && is_object($this->client) && count($paths) > 0) {
            /** @var \Guzzle\Service\Resource\Model $result */
            $result = $this->client->createInvalidation(array(
                'DistributionId' => $distributionId,
                'Paths' => array(
                    'Quantity' => count($paths),
                    'Items' => $paths,
                ),
                'CallerReference' => time(),
            ));

            return $result;
        }
    }
}

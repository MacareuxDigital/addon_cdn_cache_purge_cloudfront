<?php

use Aws\CloudFront\CloudFrontClient;

class CloudfrontCache
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
        if (defined('AWS_CLOUDFRONT_ACCESS_KEY') && defined('AWS_CLOUDFRONT_ACCESS_SECRET')) {
            $cloudFront = new CloudFrontClient(array(
                'region'  => self::$region,
                'version' => self::$sdk_version,
                'credentials' => array(
                    'key'    => AWS_CLOUDFRONT_ACCESS_KEY,
                    'secret' => AWS_CLOUDFRONT_ACCESS_SECRET,
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
        if (defined('AWS_CLOUDFRONT_DISTRIBUTION') && is_object($this->client) && count($paths) > 0) {
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

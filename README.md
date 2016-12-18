# A concrete5 add-on to purge CDN caches on CloudFront

Flushes Amazon CloudFront cache when you click Clear Cache button.

## Installation

```bash
$ cd ./packages
$ git clone git@github.com:hissy/addon_cloudfront_cache_purge.git cdn_cache_purge_cloudfront
$ cd cdn_cache_purge_cloudfront
$ composer install
$ cd ../../
$ ./concrete/bin/concrete5 c5:package-install cdn_cache_purge_cloudfront
```

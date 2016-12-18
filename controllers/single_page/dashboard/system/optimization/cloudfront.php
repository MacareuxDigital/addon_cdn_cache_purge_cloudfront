<?php
namespace Concrete\Package\CdnCachePurgeCloudfront\Controller\SinglePage\Dashboard\System\Optimization;

use Core;
use Package;

class Cloudfront extends \Concrete\Core\Page\Controller\DashboardPageController
{
    public function view()
    {
        /** @var \Concrete\Core\Package\Package $pkg */
        $pkg = Package::getByHandle('cdn_cache_purge_cloudfront');
        $accessKey = $pkg->getFileConfig()->get('aws.cloudfront.access_key');
        $accessSecret = $pkg->getFileConfig()->get('aws.cloudfront.access_secret');
        $distributionId = $pkg->getFileConfig()->get('aws.cloudfront.distribution_id');
        $this->set('accessKey', $accessKey);
        $this->set('accessSecret', $accessSecret);
        $this->set('distributionId', $distributionId);
    }

    public function settings_saved()
    {
        $this->set('message', t('Settings saved.'));
        $this->view();
    }

    public function update_settings()
    {
        if ($this->token->validate("update_settings")) {
            if ($this->isPost()) {
                /** @var \Concrete\Core\Utility\Service\Validation\Strings $validator */
                $validator = Core::make('helper/validation/strings');
                $accessKey = $this->post('accessKey');
                $accessSecret = $this->post('accessSecret');
                $distributionId = $this->post('distributionId');

                if (!$validator->notempty($accessKey)) {
                    $this->error->add(t('Please enter Access Key.'));
                }
                if (!$validator->notempty($accessSecret)) {
                    $this->error->add(t('Please enter Access Secret.'));
                }
                if (!$validator->notempty($distributionId)) {
                    $this->error->add(t('Please enter Distribution ID.'));
                }

                if (!$this->error->has()) {
                    /** @var \Concrete\Core\Package\Package $pkg */
                    $pkg = Package::getByHandle('cdn_cache_purge_cloudfront');
                    $pkg->getFileConfig()->save('aws.cloudfront.access_key', $accessKey);
                    $pkg->getFileConfig()->save('aws.cloudfront.access_secret', $accessSecret);
                    $pkg->getFileConfig()->save('aws.cloudfront.distribution_id', $distributionId);
                    $this->redirect('/dashboard/system/optimization/cloudfront', 'settings_saved');
                }
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->view();
    }
}

<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<form action="<?php echo $this->action('update_settings')?>" method="post">
    <?php echo $this->controller->token->output('update_settings')?>
    <fieldset>
        <legend><?php echo t('Credentials'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('accessKey', t('Access Key')); ?>
            <?php echo $form->text('accessKey', $accessKey); ?>
        </div>
        <div class="form-group">
            <?php echo $form->label('accessSecret', t('Access Secret')); ?>
            <div class="input-group">
                <?php echo $form->password('accessSecret', $accessSecret); ?>
                <a href="#" id="showAccessSecret" class="input-group-addon"><?=t('Display')?></a>
            </div>
            <p class="help-block"><?php echo t('Enter the security credentials to verify whether you have permission to access. To see your Access Key & Secret, visit AWS IAM console.'); ?></p>
        </div>
        <legend><?php echo t('CloudFront'); ?></legend>
        <div class="form-group">
            <?php echo $form->label('distributionId', t('Distribution ID')); ?>
            <?php echo $form->text('distributionId', $distributionId); ?>
            <p class="help-block"><?php echo t('Enter ID which distribution you want to clear caches.'); ?></p>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-success" type="submit" ><?php echo t('Save')?></button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(function() {
        $('#showAccessSecret').click(function (e) {
            e.preventDefault();
            $('#accessSecret').prop('type', 'text');
        });
    });
</script>
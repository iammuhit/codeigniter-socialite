<section class="content">
    <div class="row">
        <?php foreach ($providers as $provider => $details): ?>

            <div data-provider="<?php echo $provider ?>" class="provider col-md-6 <?php echo empty($details['credentials']) ? 'no_credentials' : 'has_credentials' ?> <?php echo alternator('', 'last') ?>">

                <?php echo form_open('admin/social/save_credentials/' . $provider, 'class="save_credentials"') ?>

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $details['human'] ?></h3>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="client_key"><?php echo lang('social:client_key') ?> <span>*</span></label>
                            <div class="input"><?php echo form_input('client_key', isset($details['credentials']) ? $details['credentials']->client_key : '', ['class' => 'form-control']) ?></div>				
                        </div>

                        <div class="form-group">
                            <label for="client_secret"><?php echo lang('social:client_secret') ?> <span>*</span></label>
                            <div class="input"><?php echo form_input('client_secret', isset($details['credentials']) ? $details['credentials']->client_secret : '', ['class' => 'form-control']) ?></div>				
                        </div>

                        <div class="form-group">
                            <label for="scope"><?php echo lang('social:scope') ?></label>
                            <div class="input"><?php echo form_input('scope', isset($details['credentials']) ? $details['credentials']->scope : (empty($details['default_scope']) ? '' : $details['default_scope']), ['class' => 'form-control']) ?></div>				
                        </div>

                        <div class="buttons form-group">

                            <button type="submit" name="save" value="save" class="btn btn-primary save" disabled>
                                <span><?php echo lang('buttons:save') ?></span>
                            </button>

                            <button type="button" name="remove" value="<?php echo $provider ?>" class="btn btn-danger clear" <?php echo empty($details['credentials']->client_key) ? 'disabled' : '' ?>>
                                <span><?php echo lang('global:remove') ?></span>
                            </button>

                            <button type="button" name="disable" value="0" class="btn btn-danger status <?php echo empty($details['credentials']->is_active) ? 'hidden' : '' ?>">
                                <span><?php echo version_compare(CMS_VERSION, '2.0.9', '>') ? lang('global:disable') : lang('disable_label') ?></span>
                            </button>

                            <button type="button" name="enable" value="1" class="btn btn-success status <?php echo empty($details['credentials']->is_active) ? '' : 'hidden' ?>">
                                <span><?php echo version_compare(CMS_VERSION, '2.0.9', '>') ? lang('global:enable') : lang('enable_label') ?></span>
                            </button>

                            <button type="button" class="btn btn-warning token">
                                <span><?php echo lang('social:get_tokens') ?></span>
                            </button>

                        </div>

                        <div class="tokens">
                            <dl>
                                <dt><?php echo lang('social:access_token') ?></dt>
                                <dd><?php echo isset($details['credentials']->access_token) ? "<span>{$details['credentials']->access_token}</span>" : lang('global:check-none') ?></dd>

                                <?php if ($details['strategy'] == 'oauth'): ?>
                                    <dt><?php echo lang('social:secret') ?></dt>
                                    <dd><?php echo (!empty($details['credentials']->secret)) ? "<span>{$details['credentials']->secret}</span>" : lang('global:check-none') ?></dd>

                                    <dt><?php echo lang('social:refresh_token') ?></dt>
                                    <dd><em>n/a</em></dd>

                                    <dt><?php echo lang('social:expires') ?></dt>
                                    <dd><em>n/a</em></dd>
                                <?php elseif ($details['strategy'] == 'oauth2'): ?>
                                    <dt><?php echo lang('social:secret') ?></dt>
                                    <dd><em>n/a</em></dd>

                                    <dt><?php echo lang('social:refresh_token') ?></dt>
                                    <dd><?php echo isset($details['credentials']->refresh_token) ? $details['credentials']->refresh_token : lang('global:check-none') ?></dd>

                                    <dt><?php echo lang('social:expires') ?></dt>
                                    <dd><?php echo (!empty($details['credentials']->expires)) ? date('Y-m-d h:m:s', $details['credentials']->expires) : lang('global:check-none') ?></dd>
                                <?php endif ?>
                            </dl>
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        <?php endforeach ?>
    </div>
</section>
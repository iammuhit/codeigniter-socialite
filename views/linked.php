<div class="box">
    <div class="box-header">
        <h2 class="box-title"><?php echo lang('social:linked_accounts') ?></h2>
    </div>
    <div class="box-body">
        <ul>
            <?php foreach ($authentications as $auth): ?>
                <li><?php echo ucfirst($auth->provider) ?> <span class="uid">(<?php echo $auth->uid ?>)</span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
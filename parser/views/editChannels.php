<?php $this->showTree(CHANNEL_PARAM) ?>
<form method="POST" class="form note-form">
    <h1 class="title form__field_full"><?php echo $this->h1; ?></h1>
    <?php
        $this->notices();
        if ( ! empty($channels) ) {
            $this->channelsForm($channels);
            $this->submitForm();
        } else {
            echo 'Каналы не найдены';
        }
    ?>
</form>
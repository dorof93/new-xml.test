<?php $this->showTree(FILE_PARAM) ?>
<form method="POST" class="form note-form" enctype="multipart/form-data">
    <h1 class="title form__field_full"><?php echo $this->h1; ?></h1>
    <?php $this->notices() ?>
    <div class="form__field form__file">
        <input class="form__input form__file-input" name="xmlfile" type="file">
    </div>
    <?php $this->submitForm() ?>
</form>
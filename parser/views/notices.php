<div class="form__field form__field_full">
    <?php if ( ! empty($_SESSION['errors']) ) { ?>
        <div class="form__backend-errors note-form__backend-errors">
            <?php foreach ($_SESSION['errors'] as $error) { ?>
                <div class="form__error form__error_active">
                    <div class="form__error-marker">!</div>
                    <div class="form__error-text"><?php echo $error ?></div>
                </div>
            <?php } ?> 
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php } ?>
    <?php if ( ! empty($_SESSION['success']) ) { ?>
        <div class="form__backend-success note-form__backend-success">
            <?php foreach ($_SESSION['success'] as $success) { ?>
                <div class="form__success form__success_active">
                    <div class="form__success-marker">i</div>
                    <div class="form__success-text"><?php echo $success ?></div>
                </div>
            <?php } ?> 
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php } ?>
</div>
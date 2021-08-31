<?php get_header() ?>

<?php if($this->getFirebaseUser()): ?>
    <div class=""><a href="/logout">LOGOUT</a></div>
<?php else: ?>
    <div id="wpfp-backdrop" class="modal-backdrop bg-light d-none" style="z-index: 10000;"></div>
    <div id="wpfp-loader" class="d-flex justify-content-center d-none">
        <div class="spinner-border text-primary" style="width: 5rem; height: 5rem; z-index: 10001;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div id="firebaseui-auth-container"></div>
<?php endif; ?>

<?php get_footer() ?>

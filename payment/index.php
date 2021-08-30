<?php get_header() ?>
<div class="container payment-form mw-sm mx-auto text-dark">
    <form id="payment-form" class="rounded">
        
        <div class="px-4 pt-4 border-gray-300 border rounded mb-3">
            <?php foreach($this->products as $product): ?>
                <div class="form-group pb-4">
                    <h3><?php echo $product['product']->name ?></h3>
                    <p class="text-mute"><?php echo $product['product']->description ?></p>
                    <?php foreach($product['prices'] as $price): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price-id" value="<?php echo $price->id ?>">
                            <label class="form-check-label" for="<?php echo $price->id ?>">
                                <?php echo number_format($price->unit_amount) ?>
                                <?php echo __($price->currency) ?>
                                <?php echo __($price->recurring->interval) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="form-group pb-2">
            <label for="card-element-cardnumber" class="pb-2"><?php echo __('カード番号') ?></label>
            <div id="card-element-cardnumber" class="p-3 rounded" style="border: 1px solid #ced4da;"></div>
        </div>
        <div id="card-message" class="alert alert-danger d-none " role="alert"></div>

        <div class="form-group pb-2">
            <label for="card-name" class="pb-2"><?php echo __('カードに書かれているお名前') ?></label>
            <input type="text" class="form-control p-3" id="card-name" aria-describedby="cardNameHelp" placeholder="eg.) TARO YAMADA" required>
            <small id="cardNameHelp" class="form-text text-muted"></small>
        </div>
        <div id="card-name-message" class="alert alert-danger d-none" role="alert"></div>

        <div class="form-group my-3">
            <button class="btn btn-primary w-100 p-3" type="submit" id="submit-payment-button" disabled="disabled">
                <span id="text-subscribe-spinner" class="d-none spinner-border spinner-border" role="status" aria-hidden="true"></span>
                <span id="text-subscribe"><?php echo __('購入する') ?></span>
            </button>
        </div>


    </form>
</div>
<?php get_footer() ?>

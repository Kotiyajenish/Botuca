"use strict";

(function ($) {
  'use strict';

  $(document).on('click', '.merchant-flexible-content-control.volume-discounts-style .layout', function () {
    var $this = $(this),
      $parent = $this.closest('.merchant-flexible-content-control.volume-discounts-style');
    $parent.find('.layout').removeClass('active');
    $this.addClass('active');
    initPreview();
  });
  $(document).on('change.merchant keyup', function () {
    initPreview();
  });
  function initPreview() {
    var layout = $('.merchant-flexible-content-control.volume-discounts-style').find('.layout.active'),
      titleText = layout.find('.merchant-field-table_title input').val(),
      titleTextColor = layout.find('.merchant-field-title_text_color input').val(),
      titleTextFontSize = layout.find('.merchant-field-title_font_size input').val(),
      titleTextFontWeight = layout.find('.merchant-field-title_font_weight select').val(),
      bgColor = layout.find('.merchant-field-table_item_bg_color input').val(),
      borderColor = layout.find('.merchant-field-table_item_border_color input').val(),
      textColor = layout.find('.merchant-field-table_item_text_color input').val(),
      labelBgColor = layout.find('.merchant-field-table_label_bg_color input').val(),
      labelTextColor = layout.find('.merchant-field-table_label_text_color input').val(),
      discountType = layout.find('.merchant-field-discount_type input:checked').val(),
      discountAmount = +layout.find('.merchant-field-discount input').val(),
      saveLabelValue = layout.find('.merchant-field-save_label input').val(),
      buyLabelValue = layout.find('.merchant-field-buy_text input').val(),
      quantityValue = +layout.find('.merchant-field-quantity input').val(),
      cartOfferTitle = layout.find('.merchant-group-field-cart_page .merchant-field-title input').val(),
      cartBundleButtonText = layout.find('.merchant-group-field-cart_page .merchant-field-button_text input').val();
    $('.merchant-volume-discounts-title').css({
      'color': titleTextColor,
      'font-size': titleTextFontSize + 'px',
      'font-weight': titleTextFontWeight
    }).html(titleText);
    $('.merchant-volume-discounts-item').css({
      'border-color': borderColor,
      'background-color': bgColor,
      'color': textColor
    });
    var $saveLabelPreview = $('.merchant-volume-discounts-item-label');
    var $buyLabelPreview = $('.merchant-volume-discounts-buy-label');
    $saveLabelPreview.find('span:first').css({
      'background-color': labelBgColor,
      'color': labelTextColor
    });
    var currency = $saveLabelPreview.closest('.mrc-preview-right-column').attr('data-currency');
    var discountEach = discountType === 'fixed_discount' ? "".concat(currency).concat(discountAmount) : "".concat(discountAmount, "%");
    var discountTotal = discountType === 'fixed_discount' ? "".concat(currency).concat(discountAmount * quantityValue) : "".concat(discountAmount, "%");

    // Update Save label content
    saveLabelValue = saveLabelValue.replace(/{amount}|{percent}/g, discountTotal);
    $saveLabelPreview.find('span:first').text(saveLabelValue);

    // Update Tier format text content
    buyLabelValue = buyLabelValue.replace(/{discount}|{percent}/g, "<strong>".concat(discountEach, "</strong>")).replace(/{quantity}|{amount}/g, "<strong>".concat(quantityValue, "</strong>"));
    $buyLabelPreview.html(buyLabelValue);
    $('.merchant-cart-preview .cart-item-offer__container .offer-description').text(cartOfferTitle.replace('{quantity}', '3').replace('{discount}', '20%'));
    $('.merchant-cart-preview .cart-item-offer__container .add-to-cart .add-to-cart-button').text(cartBundleButtonText);
  }
  $('.merchant-flexible-content-control.volume-discounts-style .layout:first-child').addClass('active').trigger('click');
  $(document).on('change', 'input[type="radio"]', function () {
    var value = $(this).val();
    if (value !== 'percentage_discount' && value !== 'fixed_discount') {
      return;
    }
    var $layout = $(this).closest('.layout');
    $layout.find('input[type="text"], textarea').each(function () {
      // Define the replacement string based on the radio button value
      var replacement = value === 'percentage_discount' ? '{percent}' : '{amount}';
      var currentValue = $(this).val();

      /**
       * Previously wrong variable `{amount}` was used for this field. Correct one is `{quantity}`.
       * So fix it as soon as Discount type is changed.
       * Keep it for backward compatibility
       */
      if ($(this).attr('name').includes('buy_text')) {
        replacement = '{quantity}';
      }

      // Replace occurrences of {amount} or {percent} with the appropriate replacement
      var newValue = currentValue.replace(/{amount}|{percent}/g, replacement);
      $(this).val(newValue);
    });
  });
  function show_single_product_preview() {
    var element = $('.merchant-single-product-preview');
    element.addClass('show');
  }
  function hide_single_product_preview() {
    var element = $('.merchant-single-product-preview');
    element.removeClass('show');
  }
  function show_cart_page_preview() {
    var element = $('.merchant-cart-preview');
    element.addClass('show');
  }
  function hide_cart_page_preview() {
    var element = $('.merchant-cart-preview');
    element.removeClass('show');
  }
  $('.merchant-module-page-setting-box').on('click', function (e) {
    var clickedElement = $(e.target);
    if (clickedElement.closest('.merchant-group-field-cart_page').length > 0 || clickedElement.hasClass('merchant-group-field-cart_page')) {
      show_cart_page_preview();
      hide_single_product_preview();
    } else {
      show_single_product_preview();
      hide_cart_page_preview();
    }
  });
  show_single_product_preview();
})(jQuery);
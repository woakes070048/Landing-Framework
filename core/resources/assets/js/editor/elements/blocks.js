function lfInitBlocks() {
  /*
    Loop through all blocks, generate semi-unique class
    to reference block for use in the editor. Add `-clone`
    suffix to class to prevent cloning to the power and
    link settings button with dropdown to block (Tether).
  */

  $('.-x-block').each(function() {
    var $block = $(this);
    var $button = $(xTplBlockButton).clone().appendTo('body');

    // Set unique class
    var timestamp = new Date().getTime();
    var unique_class = '-x-data-block-' + timestamp;

    $block.addClass(unique_class);
    $block.attr('data-x-el', unique_class);
    $button.attr('data-x-el', unique_class);

    // Replace class so it won't be cloned in next loop
    $button.removeClass('-x-el-block-edit').addClass('-x-el-block-edit-clone -x-el-inline-button-clone');

    // Check background image and/or color is available
    if ($block.find('.-x-block-bg-img').length || $block.find('.-x-block-bg-color').length) {
      $button.find('.-x-el-block-background').removeClass('-x-el-disabled');
    } else {
      $button.find('.-x-el-block-background').addClass('-x-el-disabled');
    }

    new Tether({
      element: $button,
      target: $block,
      attachment: 'top left',
      offset: '-5px -5px',
      targetAttachment: 'top left',
      classPrefix: '-x-data',
      constraints: [{
        to: 'scrollParent',
        attachment: 'together'
      }],
      optimizations: {
        moveElement: true,
        gpu: true
      }
    });
  });

  lfParseBlocks(true);

  /* 
    Open modal to configure background options
  */

  $('body').on('click', '.-x-el-block-background', function() {
    var block_class = $(this).parents('.-x-el-block-edit-clone').attr('data-x-el');

    if (! $(this).hasClass('-x-el-disabled') && typeof block_class !== typeof undefined && block_class !== false) {
      $(this).parents('.-x-el-dropdown').css('cssText', 'display: none !important;');

      // Check what settings can be configured in the modal
      var $block = $('.' + block_class);
      var bg_img = ($block.find('.-x-block-bg-img').length) ? 1 : 0;
      var bg_color = ($block.find('.-x-block-bg-color').length) ? 1 : 0;

      lfOpenModal(_lang["url"] + '/landingpages/editor/modal/background?bg_img=' + bg_img + '&bg_color=' + bg_color + '', block_class);
    }
  });


  /* 
    Move block one position up
  */

  $('body').on('click', '.-x-el-block-move-up', function() {
    var block_class = $(this).parents('.-x-el-block-edit-clone').attr('data-x-el');
    var block_prev = $('.' + block_class).attr('data-x-prev');

    if (! $(this).hasClass('-x-el-disabled') && typeof block_prev !== typeof undefined && block_prev !== false && typeof block_class !== typeof undefined && block_class !== false) {
      lfSwapElements($('.' + block_prev)[0], $('.' + block_class)[0]);

      // Timeout to make sure dom has changed
      setTimeout(lfParseBlocks, 70);
    }
  });

  /* 
    Move block one position down
  */

  $('body').on('click', '.-x-el-block-move-down', function() {
    var block_class = $(this).parents('.-x-el-block-edit-clone').attr('data-x-el');
    var block_next = $('.' + block_class).attr('data-x-next');

    if (! $(this).hasClass('-x-el-disabled') && typeof block_next !== typeof undefined && block_next !== false && typeof block_class !== typeof undefined && block_class !== false) {
      lfSwapElements($('.' + block_class)[0], $('.' + block_next)[0]);

      // Timeout to make sure dom has changed
      setTimeout(lfParseBlocks, 70);
    }
  });

  /* 
    Delete block
  */

  $('body').on('click', '.-x-el-block-edit-delete', function() {
    var block_class = $(this).parents('.-x-el-block-edit-clone').attr('data-x-el');

    if (! $(this).hasClass('-x-el-disabled') && typeof block_class !== typeof undefined && block_class !== false) {
      $('.-x-el-block-edit-clone[data-x-el=' + block_class + ']').remove();
      $('.' + block_class).remove();

      // Delete other elements
      $('[data-x-parent-block=' + block_class + ']').each(function() {
        $(this).remove();
      });

      // Timeout to make sure dom has changed
      setTimeout(lfParseBlocks, 70);
    }
  });

  /* 
    Duplicate block
  */

  $('body').on('click', '.-x-el-block-edit-duplicate', function() {
    var block_class = $(this).parents('.-x-el-block-edit-clone').attr('data-x-el');

    if (! $(this).hasClass('-x-el-disabled') && typeof block_class !== typeof undefined && block_class !== false) {
      var timestamp = new Date().getTime();
 
      // Clone block and replace with new class
      var $new_block = $('.' + block_class).clone().insertAfter('.' + block_class);
      $new_block.removeClass(block_class);
      $new_block.addClass('-x-data-block-' + timestamp);
      $new_block.attr('data-x-el', '-x-data-block-' + timestamp);

      // Settings
      var $new_block_settings = $('.-x-el-block-edit-clone[data-x-el=' + block_class + ']').clone().insertAfter('.-x-el-block-edit-clone[data-x-el=' + block_class + ']');
      $new_block_settings.attr('data-x-el', '-x-data-block-' + timestamp);

      new Tether({
        element: $new_block_settings,
        target: $new_block,
        attachment: 'top left',
        offset: '-5px -5px',
        targetAttachment: 'top left',
        classPrefix: '-x-data',
        constraints: [{
          to: 'scrollParent',
          attachment: 'together'
        }],
        optimizations: {
          moveElement: true,
          gpu: true
        }
      });

      // Timeout to make sure dom has changed
      setTimeout(lfParseBlocks, 70);

      // Duplicate other elements
      lfDuplicateBlockImages($new_block);
      lfDuplicateBlockLinks($new_block);
      lfDuplicateBlockLists($new_block);
      lfDuplicateBlockText($new_block);

      if (typeof lfDuplicateBlockHook === 'function') {
        lfDuplicateBlockHook($new_block);
      }
    }
  });

}

/* 
  Loop through block settings to set attributes
  and fix z-index overlapping. This function is
  called initially after settings are cloned, and
  later after layout changes like moving blocks.
*/

function lfParseBlocks(init) {
  var zIndex = 200;
  
  $('.-x-block').each(function() {
    var block_class = $(this).attr('data-x-el');
    var $block_settings = $('.-x-el-block-edit-clone[data-x-el=' + block_class + ']');

    // Check if block is first
    var prev = $('.' + block_class).prevAll('.-x-block').first();
    var first = ! prev.length;

    if (first) {
      $block_settings.find('.-x-el-block-move-up').addClass('-x-el-disabled');
      $('.' + block_class).attr('data-x-prev', null);
    } else {
      $block_settings.find('.-x-el-block-move-up').removeClass('-x-el-disabled');
      $('.' + block_class).attr('data-x-prev', prev.attr('data-x-el'));
    }

    // Check if block is last
    var next = $('.' + block_class).nextAll('.-x-block').first();
    var last = ! next.length;

    if (last) {
      $block_settings.find('.-x-el-block-move-down').addClass('-x-el-disabled');
      $('.' + block_class).attr('data-x-next', null);
    } else {
      $block_settings.find('.-x-el-block-move-down').removeClass('-x-el-disabled');
      $('.' + block_class).attr('data-x-next', next.attr('data-x-el'));
    }

    // Set z-index to prevent overlapping of dropdown menus
    $block_settings.css('cssText', 'z-index: ' + zIndex + ' !important;');
    $block_settings.find('.-x-el-dropdown').css('cssText', 'z-index: ' + zIndex + ' !important;');
    zIndex--;
  });

  // Always reposition tethered elements.
  // Also initially because $block_settings.css('cssText', ...); 
  // seems to reset position
  Tether.position();
}
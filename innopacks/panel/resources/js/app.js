const base = document.querySelector('base')?.href || '';
const editor_language = document.querySelector('meta[name="editor_language"]')?.content || 'en';

import './bootstrap';
import './bootstrap-validation';
import './autocomplete';

import common from './common';
window.inno = common;
import dominateColor from './dominate_color';
window.dominateColor = dominateColor;

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

// Set up CSRF token for all AJAX requests
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
  },
});

$(function () {
  tinymceInit();

  // Laravel Echo with Pusher configuration
  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: false,
    encrypted: true,
    wsHost: 'ws-ap1.pusher.com',
    wsPort: 443,
    wssPort: 443,
    enableStats: false,
    enabledTransports: ['ws', 'wss'], // WebSocket only
  });

  console.log('Pusher cluster:', process.env.MIX_PUSHER_APP_CLUSTER);

  // Listen to 'ReviewUpdated' event on 'reviews-channel'
  window.Echo.channel('reviews-channel')
    .listen('.review.updated', (event) => {
        console.log('Review updated event received:', event);
        reloadReviews();
    })
    .on('pusher:subscription_succeeded', () => {
        console.log('Successfully subscribed to reviews-channel');
    })
    .on('pusher:subscription_error', (status) => {
        console.error('Pusher subscription error:', status);
    })
    .on('pusher:connection_error', (err) => {
        console.error('Pusher connection error:', err);
    });

  // Function to reload reviews dynamically
  function reloadReviews() {
    axios.get('/panel/reviews/latest') // Perbaiki URL di sini
        .then(response => {
            const container = document.getElementById('review-container');
            if (container) {
                container.innerHTML = ''; // Kosongkan kontainer sebelum memasukkan data baru
                response.data.forEach(review => {
                    const reviewElement = `<div class="review-item">
                        <p><strong>${review.customer_name}</strong>: ${review.content}</p>
                        <p>Rating: ${review.rating} | Date: ${review.created_at}</p>
                    </div>`;
                    container.innerHTML += reviewElement;
                });
            }
        })
        .catch(error => console.error('Error loading reviews:', error));
  }

  // Example DOM manipulation
  $('.product-item-card')
    .mouseenter(function () {
      $(this).css('transform', 'translateY(-2%)').removeClass('shadow-sm').addClass('shadow-lg');
    })
    .mouseleave(function () {
      $(this).css('transform', 'translateY(0)').removeClass('shadow-lg').addClass('shadow-sm');
    });

  $('.plugin-market-nav-item')
    .mouseenter(function () {
      $(this).addClass('panel-item-hover');
    })
    .mouseleave(function () {
      $(this).removeClass('panel-item-hover');
    });

  $(document).on('click', '.is-alert .btn-close', function () {
    let top = 70;
    $('.is-alert').each(function () {
      $(this).animate({ top }, 100);
      top += $(this).outerHeight() + 10;
    });
  });

  $(document).on('click', '.mb-menu', function () {
    $('.sidebar-box').toggleClass('active');
  });

  $('.sidebar-box').on('click', function (e) {
    if (!$(e.target).parents('.sidebar-body').length) {
      $('.sidebar-box').removeClass('active');
    }
  });

  // AI Generate Example
  $('.ai-generate').on('click', function (e) {
    let accordionBody = $(this).closest('.accordion-body');
    let formRow = $(this).closest('.form-row');
    let inputEle = formRow.find(':input');

    let formData = {
      locale_code: accordionBody.data('locale-code'),
      locale_name: accordionBody.data('locale-name'),
      column_name: $(this).data('column'),
      column_value: inputEle.val(),
    };

    layer.load(2, { shade: [0.3, '#fff'] });
    axios
      .post('/ai/generate', formData)
      .then((res) => {
        let message = res.data.message;
        inputEle.val(message);
      })
      .catch((err) => {
        layer.msg(err.response.data.message, { icon: 2 });
      })
      .finally(() => {
        layer.closeAll('loading');
      });
  });

  $(document).on('focus', '.date input, .datetime input, .time input', function () {
    if (!$(this).prop('id')) $(this).prop('id', Math.random().toString(36).substring(2));

    $(this).attr('autocomplete', 'off');

    laydate.render({
      elem: '#' + $(this).prop('id'),
      type: $(this).parent().hasClass('date')
        ? 'date'
        : $(this).parent().hasClass('datetime')
        ? 'datetime'
        : 'time',
      trigger: 'click',
      lang: $('html').prop('lang') === 'zh-cn' ? 'cn' : 'en',
    });
  });
});

// Tinymce initialization
const tinymceInit = () => {
  if (typeof tinymce === 'undefined') {
    return;
  }

  tinymce.init({
    selector: '.tinymce',
    language: editor_language,
    branding: false,
    height: 400,
    convert_urls: false,
    inline: false,
    relative_urls: false,
    plugins: 'link lists fullscreen table hr wordcount image imagetools code',
    menubar: '',
    toolbar:
      'undo redo | toolbarImageButton | lineheight | bold italic underline strikethrough | forecolor backcolor | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify numlist bullist formatpainter removeformat charmap emoticons | preview template link anchor table toolbarImageUrlButton fullscreen code',
    toolbar_items_size: 'small',
    image_caption: true,
    imagetools_toolbar: '',
    toolbar_mode: 'wrap',
    font_formats: 'Arial=arial,helvetica,sans-serif;Georgia=georgia,palatino;Verdana=verdana,geneva',
    fontsize_formats: '10px 12px 14px 16px 18px 24px 36px 48px 56px 72px 96px',
    lineheight_formats: '1 1.1 1.2 1.3 1.4 1.5 1.7 2.4 3 4',
    setup: function (ed) {
      ed.ui.registry.addButton('toolbarImageButton', {
        icon: 'image',
        onAction: function () {
          $('#form-upload').remove();
          $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');
          $('#form-upload input[name="file"]').trigger('click');
          $('#form-upload input[name="file"]').on('change', function () {
            let file = this.files[0];
            let formData = new FormData();
            formData.append('image', file);
            formData.append('type', 'common');
            layer.load(2, { shade: [0.3, '#fff'] });
            axios
              .post('/upload/images', formData)
              .then((res) => {
                let url = res.data.url;
                ed.insertContent('<img src="' + url + '" class="img-fluid" />');
              })
              .catch((err) => {
                layer.msg(err.response.data.message, { icon: 2 });
              })
              .finally(() => {
                layer.closeAll('loading');
              });
          });
        },
      });
    },
  });
};

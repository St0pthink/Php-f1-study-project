const $ = require('jquery');
window.$ = $;
window.jQuery = $;

require('popper.js');
require('bootstrap');
require('../sass/app.scss');


$(function () {
  $('#download_button').on('click', function (e) {
    e.preventDefault();
    const $toast = $('#seasonToast');
    $toast.toast({ autohide: true, delay: 4000 }).toast('show');
  });
});

$(document).on('click', '.card.cd', function (e) {
  const $card = $(this);

  if ($card.data('noModal')) {return;}
  if ($('body').hasClass('page-driver-show')) {return;}

  show_card_info($(this));
});

$(document).on('click', '.card.cd .title-link', function (e) {
  e.stopPropagation();
});

function replace_popovers(text) {
  let html = text;
  const TERMS = 
  {
    'DNF': { content: 'Did Not Finish - не финишировал.' },
    'DNS': { content: 'Did Not Start - не стартовал.' },
  };

  Object.entries(TERMS).forEach(([pattern, info]) => {
    const re = new RegExp(`\\b(${pattern})\\b`, 'g');
    html = html.replace(re,`<a href="#" tabindex="0" data-toggle="popover" data-content="${info.content}">$1</a>`);});

  return html;
}


function set_index($m, idx) { $m.data('cardIndex', idx); }
function get_index($m) { return $m.data('cardIndex'); }

function change_info(way) {
  const $m    = $('#detailsModal');
  const cards = get_modal_cards();  

  const idx  = get_index($m);
  const next = idx + way;

  if (next < 0 || next >= cards.length) {
    return;
  }

  set_index($m, next);

  const $nextCard = $(cards.get(next));
  fill_info($nextCard);
}

function change_with_keys(e) {
  if (e.key === 'ArrowLeft')  { e.preventDefault(); change_info(-1); }
  if (e.key === 'ArrowRight') { e.preventDefault(); change_info(1);  }
}


$(document).off('keydown.modalNav').on('keydown.modalNav', change_with_keys);

function get_modal_cards() {
  return $('.card.cd').filter(function () {
    return !$(this).data('noModal');
  });
}

function show_card_info($card) {
  const cards = get_modal_cards();
  const idx   = cards.index($card[0]);

  const $m = $('#detailsModal');
  set_index($m, idx);

  fill_info($card);
}


function fill_info($card) {
  const title = $card.find('.card-title').text();
  const url   = $card.data('showUrl');

  const $m = $('#detailsModal');
  $m.find('.modal-title').text(title);
  $m.modal('show');

  $.get(url, function (html) {
      $m.find('#detailsText').html(html);
      make_view();
  });
}

function make_view() {
  const $m = $('#detailsModal');


  $m.find('.details li').each(function () 
    {
        const $li = $(this);
        const raw = $li.text().trim();
        const m   = raw.match(/^(.*?)\s[—–-]\s(.*)$/);    
        const name = (m[1]);
        const val  = (m[2]);
        $li.empty()
            .append($('<span class="name">').text(name))
            .append($('<span class="sep">').text('—'))
            .append($('<span>').html(replace_popovers(val)));
    });

    const $n = $('#detailsModal');
    const $container = $n.find('#detailsText .details');

    $n.find('[data-toggle="popover"]').popover('dispose');
    $n.find('[data-toggle="popover"]').popover({
        container: $container[0],
        trigger: 'hover focus',
        html: true,
        placement: 'top',
        template:
        '<div class="popover">' +
            '<div class="arrow"></div>' +
            '<div class="popover-body"></div>' +
        '</div>'
    });
    
}

window.make_view = make_view;
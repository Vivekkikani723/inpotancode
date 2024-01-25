(function ($) {
  $(document).ready(function () {
    let filterPostAndTaxonomy = $('.filter-sec .filter-type input[name="post_types"], .filter-type-cat input[name="categories"]');
    let search = $('#custom-search #search-input');
    let pagination = $('.page-numbers');

    // for checkbox
    filterPostAndTaxonomy.change(function () {

      if ($('.filter-sec span#selected-page').length) {
        $('#selected-page').remove();
      }

      filterByAjax();
    });

    // for search query text
    search.focusout(function () {

      if ($('.filter-sec span#selected-page').length) {
        $('#selected-page').remove();
      }

      filterByAjax();
    })

    // pagination 
    $(document).on('click', '.page-numbers', function (event) {
      event.preventDefault();

      let currentPage = 1;
      let selecetedPage = $(this).text();

      if ($('.filter-sec span#selected-page').length) {
        currentPage = $('.filter-sec span#selected-page').text();
        $('#selected-page').remove();
      }

      // NEXT Button on pagination
      if ($(this).hasClass('next')) {
        selecetedPage = (+currentPage) + 1;
      }

      // Previous Button on pagination
      if ($(this).hasClass('prev')) {
        selecetedPage = (+currentPage) - 1;
      }

      // console.log(selecetedPage);

      let selectedPageHtml = `<span id="selected-page" style="display:none !important;">${selecetedPage}</span>`;

      $('.filter-sec').append(selectedPageHtml);

      filterByAjax();
    });

    // Show All Post Without filtering
    $('#show-all-button').on('click', function () {

      if ($('.filter-sec span#selected-page').length) {
        $('#selected-page').remove();
      }

      search.val('');
      search.attr('placeholder', 'Type your search...');

      $.each(filterPostAndTaxonomy, function () {
        $(this).prop('checked', false);
      });

      filterByAjax();
    })

    // search 
    $('#custom-search').on('submit', function (e) {

      if ($('.filter-sec span#selected-page').length) {
        $('#selected-page').remove();
      }
      e.preventDefault();
      filterByAjax();

    })
  });

  function filterByAjax() {
    let selectedPostType = getSelectedPostTypes();
    let selectedPostTaxonomy = getSelectedPostTaxonomy();
    let serachQuery = $('#custom-search #search-input').val();
    let headerspace = $('.sub-page-header').height();
    let paged = 1;

    if ($('.filter-sec span#selected-page').length) {
      paged = $('.filter-sec span#selected-page').html();
    }

    let ajaxURL = $('#custom-search input[name="ajax_url"]').val();

    $('.filter-sec .filter-post .posts').html('Loading...');
    $('.filter-sec .filter-post .pagination').html('');

    $.ajax({
      url: ajaxURL, // Replace with your AJAX endpoint URL
      type: 'POST',
      dataType: "json",
      data: {
        action: 'filter_post_by_ajax', // Replace with your AJAX action hook
        search: serachQuery,
        paged: paged,
        selectedPostType: selectedPostType,
        selectedPostTaxonomy: selectedPostTaxonomy
      },
      success: function (response) {
        $('.filter-sec .filter-post .posts').html(response.html);
        $('.filter-sec .filter-post .pagination').html(response.pagination);
        $('html, body').animate({
          scrollTop: $(".et_pb_section_7").offset().top - headerspace
        }, 500);
      }
    });
    // console.log( selectedPostType, selectedPostTaxonomy, serachQuery );
  }

  function getSelectedPostTypes() {
    let filterPost = $('.filter-sec .filter-type input[name="post_types"]');
    let selectedPost = [];

    $.each(filterPost, function () {
      let value = $(this).val();

      if ($(this).is(':checked')) {

        selectedPost.push(value);
      } else {

        let index = selectedPost.indexOf(value);
        if (index !== -1) {
          selectedPost.splice(index, 1); // Remove the value from the array
        }
      }
    });

    return selectedPost;
  }

  function getSelectedPostTaxonomy() {
    let filterTaxonomy = $('.filter-type-cat input[name="categories"]');
    let selectedTaxonomy = [];

    $.each(filterTaxonomy, function () {
      let value = $(this).val();

      if ($(this).is(':checked')) {
        selectedTaxonomy.push(value);
      } else {
        let index = selectedTaxonomy.indexOf(value);
        if (index !== -1) {
          selectedTaxonomy.splice(index, 1); // Remove the value from the array
        }
      }
    });

    return selectedTaxonomy;
  }
})(jQuery);

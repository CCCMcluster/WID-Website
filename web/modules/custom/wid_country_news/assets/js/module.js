(function ($, Drupal, drupalSettings) {
  let base_url = drupalSettings.path.baseUrl;
  let news = drupalSettings.news;
  let country_iso = 'AF';
  fetch(`${base_url}news/${country_iso}?_format=json`)
    .then(response => response.json())
    .then(result => {
      console.log(result);
    });
})(jQuery, Drupal, drupalSettings);

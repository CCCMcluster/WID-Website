(function ($, Drupal, drupalSettings) {
  let base_url = drupalSettings.path.baseUrl;
  let news = drupalSettings.news;
  
  const countries = document.getElementById('countries');
  countries.addEventListener('click', function(e) {
    if(e.target && e.target.nodeName == "LI") {
      const country_iso = e.target.getAttribute('value');

      fetch(`${base_url}news/${country_iso}?_format=json`)
        .then(response => response.json())
        .then(results => {
          const cardsContainer = document.getElementById('news-cards');
          cardsContainer.innerHTML = '';

          for(let result of results) {
            const singleCard = document.createElement('div');
            singleCard.classList.add('single-card');
            cardsContainer.append(singleCard);

            if(result.field_featured_media && result.field_featured_media !== '') {
              const cardImage = document.createElement('img');
              cardImage.setAttribute('src', result.field_featured_media)
              singleCard.append(cardImage);
            }

            addElementToCard(singleCard, 'h2', result.title);
            addElementToCard(singleCard, 'p', result.body.length > 80 ? result.body.slice(0,80) : result.body);
            addElementToCard(singleCard, 'span', result.created);
          }
        });
    }
  })

  function addElementToCard(parent, tagName, textContent=null) {
    const newChild = document.createElement(tagName);
    if(textContent) {
      newChild.appendChild(document.createTextNode(textContent));
    }
    parent.append(newChild);
  };
})(jQuery, Drupal, drupalSettings);

(function ($, Drupal, drupalSettings) {
  let mapData = JSON.parse(drupalSettings.mapData);
  
  const width = 950;
  const height = 600;

  const svg = d3.select("#wid-map")
    .append('svg')
    .attr('id', 'map')
    .attr('width', width)
    .attr('height', height)
    .attr('viewBox', `0 0 ${width} ${height}`);

  const projection = d3.geoRobinson()
      .translate([width / 2, height / 2])
      .scale(width / 2 / Math.PI, height / 2 / Math.PI);

  d3.select('#wid-map').append('div').attr('class', 'report-section');
  setActiveCountry(mapData[0].iso_2);
  
  d3.json("https://gist.githack.com/ft9dipesh/7de26da7df31263dd4ab2550f35f3882/raw/d2908030d79c7c1055af3098e49edbc8cacca8b8/world_noAQ.geojson",
    function(data){
      svg.append("g")
        .selectAll("path")
        .data(data.features)
        .enter()
        .append("path")
        .attr("fill", "#fff")
        .attr("d", d3.geoPath()
            .projection(projection)
        )
        .style("stroke", "#fff");

      for (let [index, country] of mapData.entries()) {
  		  const x = projection(country.centroid)[0],
  		  	y = projection(country.centroid)[1];

  		  const marker = svg.append("svg:path")
  		  	.attr('class', `marker`)
          .attr('d', "M0,0l-8.8-17.7C-12.1-24.3-7.4-32,0-32h0c7.4,0,12.1,7.7,8.8,14.3L0,0z")
  		  	.attr('transform', `translate(${x}, ${y}) scale(0)`)
          .on("click", function() {
            setActiveCountry(country.iso_2);
            d3.selectAll('.marker').classed('marker__active', false);
            this.classList.add('marker__active');
          })
          .transition()
  		  	.duration(300)
  		  	.attr("transform", `translate(${x}, ${y}) scale(.6)`);
  	  }
    }
  );

  function setActiveCountry(countryCode) {
    d3.selectAll('.report').remove();
    fetch(`${window.location.origin}/report/country?iso=${countryCode}`)
      .then(res => res.json())
      .then(data => {
        data.slice(0, 3).forEach((report, index) => {
          const reportElement = d3.selectAll('.report-section')
            .append('div')
            .attr('class', 'report')
            .classed('report__active', index===0);
          index===0 && reportElement.append('p').attr('class', 'report-country').text(`Reports in ${report.country}`);
          reportElement.append('p').attr('class', 'report-title').text(report.title);
          reportElement.append('p')
            .attr('class', 'report-body')
            .text(`${report.body.split(" ").splice(0, index===0?40:5).join(" ")}...`);
        });
      });
  }
})(jQuery, Drupal, drupalSettings);

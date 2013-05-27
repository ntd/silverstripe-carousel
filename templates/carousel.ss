<div id="idCarousel" class="carousel slide">
	<ol class="carousel-indicators"><% loop CarouselSeats %>
		<li data-target="#idCarousel" data-slide-to="$Index"<% if First %> class="active"<% end_if %>></li><% end_loop %>
	</ol>
	<div class="carousel-inner"><% loop CarouselSeats %>
		<div class="item<% if First %> active<% end_if %>">
			$Image.Carousel<% if Title %>
			<div class="carousel-caption"><h4>$Image.Title</h4></div><% end_if %>
		</div><% end_loop %>
	</div>
	<a class="carousel-control left" href="#idCarousel" data-slide="prev">&lsaquo;</a>
	<a class="carousel-control right" href="#idCarousel" data-slide="next">&rsaquo;</a>
</div>

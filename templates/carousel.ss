<div id="ss-carousel" class="carousel slide" data-ride="carousel">
	<ol class="carousel-indicators"><% loop $CarouselSeats %>
		<li data-target="#ss-carousel" data-slide-to="$Index"<% if $First %> class="active"<% end_if %>></li><% end_loop %>
	</ol>
	<div class="carousel-inner"><% loop $CarouselSeats %>
		<div class="item<% if $First %> active<% end_if %>">
			$Image.Carousel<% if Title %>
			<div class="carousel-caption">
				<h4>$Image.Title</h4>
			</div><% end_if %>
		</div>
	</div>
	<a class="left carousel-control" href="#ss-carousel" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left"></span>
	</a>
	<a class="right carousel-control" href="#ss-carousel" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right"></span>
	</a>
</div>

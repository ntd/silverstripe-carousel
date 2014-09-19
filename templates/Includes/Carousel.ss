<div id="ss-carousel" class="carousel slide" data-ride="carousel">
	<ol class="carousel-indicators"><% loop $Slots %>
		<li data-target="#ss-carousel" data-slide-to="$Pos(0)"<% if $First %> class="active"<% end_if %>></li><% end_loop %>
	</ol>
	<div class="carousel-inner"><% loop $Slots.sort(Order) %>
	    <div class="item<% if $First %> active<% end_if %>"><% with $Image.croppedImage($Top.Width,$Top.Height) %>
			<img src="$URL" alt="$Title" width="$Width" height="$Height"><% end_with %><% if $Top.Captions && $Title %>
			<div class="carousel-caption">
				<h4>$Image.Title</h4>
				</div><% end_if %>
		</div><% end_loop %>
	</div>
	<a class="left carousel-control" href="#ss-carousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left"></span>
	</a>
	<a class="right carousel-control" href="#ss-carousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right"></span>
	</a>
</div>

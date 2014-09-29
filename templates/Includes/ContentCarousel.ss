<% if $Images %>
<div class="row">
	<div class="lead col-md-12">
		<div id="ss-carousel" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators"><% loop $Images %>
				<li data-target="#ss-carousel" data-slide-to="$Pos(0)"<% if $First %> class="active"<% end_if %>></li><% end_loop %>
			</ol>
			<div class="carousel-inner"><% loop $Images %>
			    <div class="item<% if $First %> active<% end_if %>"><% with $MaybeCroppedImage($Top.Width,$Top.Height) %>
					<img src="$URL" alt="$Title" width="$Width" height="$Height"><% end_with %><% if $Top.Captions %>
					<div class="carousel-caption">
						<h4>$Title</h4>
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
	</div>
</div>
<% end_if %>

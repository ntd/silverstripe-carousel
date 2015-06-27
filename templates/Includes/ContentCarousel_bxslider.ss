<% if $Images %>
<ul id="the-carousel"><% loop $Images %>
	<li><% with $MaybeCroppedImage($Top.Width,$Top.Height) %>
		<img src="$URL" alt="$Title"<% if $Top.Captions && $Caption %> title="$Caption"<% end_if %> width="$Width" height="$Height"><% end_with %>
	</li><% end_loop %>
</ul>
<% require CSS("//cdn.jsdelivr.net/bxslider/4.2.5/jquery.bxslider.css") %>
<script type="text/javascript" src="http://cdn.jsdelivr.net/g/jquery@1,bxslider@4"></script>
<script type="text/javascript">
\$(document).ready(function() {
	\$('#the-carousel').bxSlider({
		slideWidth: $Images.first.Width,
		slideMargin: 8,
		auto: true,
		autoControls: true
	});
});
</script>
<% end_if %>

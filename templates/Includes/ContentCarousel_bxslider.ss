<% if $Images %>
<ul id="ss-carousel"><% loop $Images %>
	<li><% with $MaybeCroppedImage($Top.Width,$Top.Height) %>
		<img src="$URL" alt="$Title"<% if $Top.Captions && $Caption %> title="$Caption"<% end_if %> width="$Width" height="$Height"><% end_with %>
	</li><% end_loop %>
</ul>
<% end_if %>

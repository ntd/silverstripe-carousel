<%-- WARNING! The silverstrap theme overrides this template --%>
<h1>$Title</h1>
<% include ContentCarousel_bxslider %>
<div class="typography">
	$Content
	$Form
</div>

<%-- Include BXSlider 4 --%>
<% require CSS("//cdn.jsdelivr.net/bxslider/4/jquery.bxslider.css") %>
<script type="text/javascript" src="//cdn.jsdelivr.net/g/jquery@1,bxslider@4"></script>

<%-- Enable the carousel --%>
<script type="text/javascript">
\$(document).ready(function() {
    \$('#ss-carousel').bxSlider({<% if $Width > 0 %>
		slideWidth: $Width,<% else_if $Images %>
		slideWidth: $Images.first.Width,<% end_if %>
		slideMargin: 8,
		auto: true,
		autoControls: true
	});
});
</script>

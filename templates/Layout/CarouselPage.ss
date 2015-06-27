<%-- silverstrap has a Bootstrap dedicated template that overrides this one:
     this page uses out of the box the bxSlider module --%>
<div><% if $Title %>
	<h1>$Title</h1><% end_if %>
	<% include ContentCarousel_bxslider %>
	<main class="typography">
		$Content
		$Form
	</main>
</div>

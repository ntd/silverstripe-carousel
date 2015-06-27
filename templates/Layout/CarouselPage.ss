<%-- silverstrap contains a dedicated template that overrides this one --%>
<div class="container"><% if $Title %>
	<div class="page-header">
		<h1>$Title</h1>
	</div><% end_if %>
	<% include ContentCarousel %>
	<div class="row">
	<main class="col-xs-12 typography">
		$Content
		$Form
	</main>
</div>

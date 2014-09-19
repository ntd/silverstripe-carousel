This module requires
[SortableGridField](http://github.com/UndefinedOffset/SortableGridField).

It implements the new **CarouselPage** type that adds a new tab in the
CMS for handling carousel _slots_ and images. The size of the images can
be customized on a per page basis, and so is the option to show or hide
the captions.

The `Carousel.ss` template renders a self-contained `<div>` with a
[Bootstrap carousel](http://getbootstrap.com/javascript/#carousel).
You can include it in any place inside your page template, e.g.:

    <% include Carousel.ss %>
    <%--
        The following chunk of javascript enables the carousel rotation:
        see the Bootstrap documentation for the available customizations
    --%>
    <script>
    $(document).ready(function() {
        $('#ss-carousel').carousel();
    });
    </script>

Alternatively, the `CarouselPage.ss` layout template is provided. It
renders a full (standard) page, though it works out of the box only with
the [silverstrap](http://dev.entidi.com/p/silverstrap/) theme because it
relies on some convention adopted by that theme.

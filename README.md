Silverstripe Carousel
=====================

It implements a new page type (_CarouselPage_) that provides everything
needed to manage a carousel of images. Differently from other carousels,
this one does not have the concept of seats (or slots), so managing the
images is **much** easier on the backend side.

You can do bulk uploads and reorder the images by dragging and dropping
their thumbnails in a dedicated tab (_Image list_) inside the CMS. The
same image can be shared among multiple carousels. This module adds also
some field to the _Settings_ tab, allowing the customization of some
aspect of the carousel on a per page basis (e.g., width and height of
the images and a flag to show or hide captions). Support for the
[translatable](https://github.com/silverstripe/silverstripe-translatable)
module is provided out of the box, i.e. when creating a translation,
the new page will borrow the original carousel images.

To improve file organization, if you upload your images from the
carousel a specific folder is pre-selected (it it exists!) instead of
the fallback one (`Uploads`). The folder name depends on the class
hierarchy. If, for example, you inherit your `HomePage` type from
`CarouselPage`, the code will look for any `Home` or `Carousel` folder
(in this order) under your assets directory.

Usage
-----

This module is front end agnostic, that is you can use the javascript
library you prefer by writing a proper template.

Out of the box there are a couple of templates inside `Includes` that
implement a [Bootstrap](http://getbootstrap.com/javascript/#carousel)
(`ContentCarousel_bootstrap.ss`) or a [bxSlider](http://bxslider.com/)
(`ContentCarousel_bxslider.ss`) carousel.

You can include one of them in any place inside your page template, e.g.
a basic Bootstrap layout template could look like this one:

    <%-- Layout/CarouselPage.ss --%>
    <div class="page-header">
        <h1>$Title</h1>
    </div>
    <% include ContentCarousel_bootstrap.ss %>
    <div class="row typography">
        $Content
    </div>

    <%-- Include Bootstrap 3 --%>
    <% require CSS("//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.min.css") %>
    <script type="text/javascript" src="//cdn.jsdelivr.net/g/jquery@1,bootstrap@3"></script>

    <%-- Enable the carousel --%>
    <script type="text/javascript">
        \$(document).ready(function() {
            \$('#ss-carousel').carousel();
        });
    </script>

The default layout (`CarouselPage.ss`) embeds a _bxSlider_ carousel.

Alternatively, the [silverstrap](http://dev.entidi.com/p/silverstrap/)
theme already supports this module out of the box. If you intend to
leverage _Bootstrap_, consider using this theme instead, either by
overriding or by modifying it.

### Image captions

HTML captions are allowed. This is internally done by leveraging the
`Content` field of the `File` table (typically empty). This is supposed
to be an HTML chunk despite being defined as a plain text field by the
SilverStripe code.

If the captions are enabled, they can be edited directly inside the
_Image list_ tab by clicking the _Edit_ button.

When `Content` is not defined, an `<h4>` element with the image title is
used instead: see `templates/Includes/ImageCaption.ss` for details.

Author
------

This project has been developed by [ntd](mailto:ntd@entidi.it). Its
[home page](http://silverstripe.entidi.com/) is shared by other
[SilverStripe](http://www.silverstripe.org/) modules and themes.

To check out the code, report issues or propose enhancements, go to the
[dedicated tracker](http://dev.entidi.com/p/silverstripe-carousel).
Alternatively, you can do the same things by leveraging the official
[github repository](https://github.com/ntd/silverstripe-carousel).

Installation
------------

The feature of reordering with drag and drop is provided by the
[sortablefile](https://github.com/bummzack/sortablefile) module that
*must* be installed before.

To install silverstripe-carousel itself you should proceed as usual:
drop the directory tree in your SilverStripe root and do a
`/dev/build/`. You will gain the new `CarouselPage` type in the CMS.

If you use [composer](https://getcomposer.org/), the dependencies will
be pulled-in automatically, so you could just run the following command:

    composer require entidi/silverstripe-carousel dev-master

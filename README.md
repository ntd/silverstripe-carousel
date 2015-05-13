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
the images and a flag to show or hide captions).

To improve file organization, if you upload your images from the
carousel a specific folder is pre-selected (it it exists!) instead of
the fallback one (`Uploads`). The folder name depends on the class
hierarchy. If, for example, you inherit your `HomePage` type from
`CarouselPage`, the code will look for any `Home` or `Carousel` folder
(in this order) under your assets directory.

Usage
-----

The `ContentCarousel.ss` template renders a self-contained `<div>` with a
[Bootstrap carousel](http://getbootstrap.com/javascript/#carousel)
inside. You can include it in any place inside your pages, e.g.:

    <%-- This is a typical Page.ss --%>
    <h1>$Title</h1>
    <div class="carousel">
        <% include ContentCarousel.ss %>
    </div>
    <%-- The following chunk of javascript enables the carousel rotation:
         see the Bootstrap docs for the available options.
         You can (and should) put it in your external javascript file. --%>
    <script>
    $(document).ready(function() {
        $('#ss-carousel').carousel();
    });
    </script>
    <div class="content">
        $Content
    </div>

Alternatively, the `CarouselPage.ss` layout template is provided. It
renders a full (standard) page, though it works out of the box only with
the [silverstrap](http://dev.entidi.com/p/silverstrap/) theme because it
relies on some convention adopted by that theme.

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

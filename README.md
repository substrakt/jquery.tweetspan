jQuery.tweetspan
================

Pull updates from a Twitter account straight into an HTML element. Check out test.html for a simple example.

You can use any field from the
[Twitter Search JSON API](https://dev.twitter.com/docs/api/1/get/search)'s result set. You can use filters
to determine how text should be formatted. Currently available filters are:

- ``tweet``: To format tweet contents, linking @ usernames, hashtags and URLs
- ``localtime``: To convert the ``created_at`` field into a local time string
- ``timesince``: To convert the ``created_at`` field into a string showing how long ago the tweet was posted
- ``capfirst``: To capitalise the first letter of a string.

To chain filters together, separate each filter name with a space, just as you would with classes.

Any questions, please contact [Substrakt](http://substrakt.co.uk).
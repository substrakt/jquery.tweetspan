# jQuery.tweetspan

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

## WordPress plugin

There's a simple WordPress plugin to achieve the same results.

1. Download ``jquery.tweetspan.php`` and ``jquery.tweetspan.min.js``
2. Create a directory in your WordPress installation's plugins directory called ``jquery.tweetspan`` and upload those two files there
3. Activate the plugin

Now you should be able to write your HTML as normal.

## Quick example

Once your JavaScript is in place - or you've used the WordPress plugin to do so - a simple HTML example
would work like this:

    <div class="tweets" data-account="substrakt" data-count="5">
        <h1>Tweets from @<a href="http://twitter.com/substrakt">substrakt</a></h1>
        <div class="tweet well">
            <img class="avatar" data-field="profile_image_url" />
            <p data-field="text" data-format="tweet"></p>
            
            <p>
                <small>
                    <span data-field="created_at" data-format="timesince capfirst"></span>
                </small>
            </p>
        </div>
    </div>

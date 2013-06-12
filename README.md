# jQuery.tweetspan

Pull updates from a Twitter account straight into an HTML element. Check out test.html for a simple example.

You can use any field from the
[Twitter Search JSON API](https://dev.twitter.com/docs/api/1/get/search)'s result set. You can use filters
to determine how text should be formatted. Currently available filters are:

- `tweet`: To format tweet contents, linking @ usernames, hashtags and URLs
- `localtime`: To convert the `created_at` field into a local time string
- `timesince`: To convert the `created_at` field into a string showing how long ago the tweet was posted
- `capfirst`: To capitalise the first letter of a string.

To chain filters together, separate each filter name with a space, just as you would with classes.

## Authentication

Since mid 2013, Twitter has required that calls to its search API be authenticated using the same method as
their main API. You'll need a server-side component which can authenticate and communicate with Twitter.

If you use WordPress, you can install it via the instructions below and then visit the Settings > Tweetspan
page in your WordPress dashboard, entering the details for a Twitter app that can access the search API.
Alternatively you can build an intermediary solution yourself (which we did at Substrakt) so that you don't
have to enter or change these details for every site you deploy the plugin on.

## Getting started in PHP

For WordPress-specific instructions, see the section below.

1. Upload the contents of the repo to your server
2. Add a `<script>` tag pointing to the latest available version of jQuery, in your `<head>` tag
3. Add another `<script>` tag in your `<head>`, pointing to jquery.tweetspan.min.js
4. Add another `<script>` tag (in your `<body>` if you prefer) which tells jQuery.tweetspan which endpoint to use:

	<script>$.tweetspan('endpoint', '/jquery.tweetspan.php');</script>

See the "HTML" section below for details on what HTML to include.

## Getting started in WordPress

1. Download the contents of the repo
2. Create a directory in your WordPress installation's plugins directory called "jquery.tweetspan" and upload the contents there
3. Activate the plugin
4. Visit **Settings** > **jQuery Tweetspan** and enter the relevant details

If you want your server to act as the endpoint, meaning that it communicates with Twitter, you'll need to
[create a Twitter app](https://dev.twitter.com/apps/new) and pass the various app keys to the plugin's settings
page.

If you have a third-party service that can pass your search queries to Twitter without you having to specify
OAuth info, you can enter the URL into the "Endpoint URL" box, leaving the boxes above blank.

There's a widget which you'll find in **Appearance** > **Widgets** called **Tweetspan**. Drag that to a sidebar
and specify the username to pull tweets from, and the number of tweets to pull. That'll basically render
the HTML you see below, and add in all of the necessary `<script>` tags.

## HTML

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

## Options

You can set a callback option, to set a function that can run when tweets have been obtained. For example:

	<script>
		jQuery(document).ready(
			function($) {
				$('.tweets').tweetspan('callback',
					function(context) {
						alert(context.find('.tweet').length + ' tweet(s)');
					}
				);
			}
		);
	</script>

## Credits

The Twitter OAuth client bundled with the PHP solution is
[TwitterOAuth](https://github.com/abraham/twitteroauth) by
[Abraham Williams](https://github.com/abraham).

This plugin is written and maintained by [Mark Steadman](http://marksteadman.com/),
Technical Director of [Substrakt](http://substrakt.co.uk/).
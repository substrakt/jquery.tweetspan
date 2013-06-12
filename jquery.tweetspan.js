jQuery.fn.tweetspan = function(option, argument) {
	if(typeof(option) == 'string') {
		var options = $(this).data('ts-options');
		
		if(typeof(options) == 'undefined') {
			options = {};
		}
		
		options[option] = argument;
		$(this).data('ts-options', options);
	}
};

jQuery.tweetspan = function(cmd, args) {
	switch(cmd) {
		case 'endpoint':
			if(typeof args != 'undefined') {
				jQuery.tweetspan.endpoint = args;
			}
			
			return jQuery.tweetspan.endpoint;
			break;
	}
};

jQuery.tweetspan.endpoint = 'http://search.twitter.com/search.json';

jQuery(document).ready(
	function($) {
		var twitterFilters = {
			tweet: function(text) {
				var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i;
				text = text.replace(exp, "<a href='$1' target='_blank'>$1</a>");
				
				exp = /(^|\s)#(\w+)/g;
				text = text.replace(exp, "$1<a href='http://search.twitter.com/search?q=%23$2' target='_blank'>#$2</a>");
				
				exp = /(^|\s)@(\w+)/g;
				text = text.replace(exp, "$1<a href='http://www.twitter.com/$2' target='_blank'>@$2</a>");
				
				return text;
			},
			localtime: function(text) {
				return new Date(Date.parse(text)).toLocaleString();
			},
			timesince: function(text) {
				var tTime = new Date(text);
				var cTime = new Date();
				var sinceMin = Math.round((cTime - tTime) / 60000);
				
				if(sinceMin == 0) {
					var sinceSec = Math.round((cTime - tTime) / 1000);
					if(sinceSec < 10) {
						var since = 'less than 10 seconds ago';
					} else if(sinceSec < 20) {
						var since = 'less than 20 seconds ago';
					} else {
						var since = 'half a minute ago';
					}
				} else if(sinceMin == 1) {
					var sinceSec=Math.round((cTime - tTime) / 1000);
					if(sinceSec == 30) {
						var since = 'half a minute ago';
					} else if(sinceSec < 60) {
						var since = 'less than a minute ago';
					} else {
						var since = 'a minute ago';
					}
				} else if(sinceMin < 45) {
					var since = sinceMin +' minutes ago';
				} else if(sinceMin > 44 && sinceMin < 60) {
					var since = 'about an hour ago';
				} else if(sinceMin < 1440) {
					var sinceHr = Math.round(sinceMin / 60);
					if(sinceHr == 1) {
						var since = 'about an hour ago';
					} else {
						var since = 'about ' + sinceHr + ' hours ago';
					}
				} else if(sinceMin > 1439 && sinceMin < 2880) {
					var since = 'a day ago';
				} else {
					var sinceDay = Math.round(sinceMin / 1440);
					var since = sinceDay + ' days ago';
				}

				return since;
			},
			capfirst: function(text) {
				return text.substr(0, 1).toUpperCase() + text.substr(1);
			}
		}
		
		function formatTweetFields(jsonParent, htmlParent, parentKey) {
			var kk, field, text, filters, filter;
			
			for(var k in jsonParent) {
				text = jsonParent[k];
				if(typeof text == 'object') {
					formatTweetFields(text, htmlParent, k);
					continue;
				}
				
				if(typeof parentKey != 'undefined') {
					kk = parentKey + '.' + k;
				} else {
					kk = k;
				}
				
				field = htmlParent.find('[data-field="' + kk + '"]');
				if(field.length == 0) {
					continue;
				}
				
				filters = field.attr('data-format');
				
				if(filters) {
					filters = filters.split(' ');
					for(var f = 0; f < filters.length; f ++) {
						filter = filters[f];
						
						if(filter) {
							filter = twitterFilters[filter];
							if(typeof filter != 'undefined') {
								text = filter(text);
							}
						}
					}
				}
				
				if(field[0].tagName.toLowerCase() != 'img') {
					field.html(text);
				} else {
					field.attr('src', text);
				}
			}
		}
		
		$('.tweets[data-account]').each(
			function() {
				var self = $(this);
				var account = self.attr('data-account');
				var hashtag = self.attr('data-hashtag');
				var count = parseInt(self.attr('data-count'));
				var url = $.tweetspan('endpoint');
				var params = {};
				
				if(url.indexOf('?') == -1) {
					url += '?';
				} else {
					url += '&';
				}
				
				params['from'] = account;
				params['count'] = count;
				
				if(hashtag) {
					params['q'] = '#' + hashtag;
				}
				
				url += $.param(params);
				
				$.getJSON(url + '&callback=?',
					(
						function(context) {
							return function(data) {
								var template = context.find('.tweet');
								var parent = template.parent();
								var options = context.data('ts-options');
								var results = typeof data.statuses != 'undefined' ? data.statuses : [];
								var tweet;
								
								for(var i = 0; i < results.length; i ++) {
									tweet = template.clone();
									formatTweetFields(results[i], tweet);
									parent.append(tweet);
								}
								
								template.remove();
								context.show();
								
								if(typeof(options) == 'object') {
									if(typeof(options['callback']) == 'function') {
										options['callback'](context);
									}
								}
							}
						}
					)(self)
				);
			}
		).hide();
	}
);
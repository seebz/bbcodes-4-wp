
jQuery(function($) {
	var qt = QTags;

	edButtons = [];
	edButtons.push( new QTags.TagButton('b','b','[b]','[/b]','b') );
	edButtons.push( new QTags.TagButton('i','i','[i]','[/i]','i') );
	edButtons.push( new QTags.TagButton('u','u','[u]','[/u]','u') );

	var imgButton = new QTags.TagButton('img','img','[img]','[/img]','i');
	imgButton.callback = function(e, c, ed, defaultValue) {
		if ( ! defaultValue ) {
			defaultValue = 'http://';
		}
		var src = prompt(quicktagsL10n.enterImageURL, defaultValue), alt;
		if ( src ) {
		//	alt = prompt(quicktagsL10n.enterImageDescription, '');
			this.tagStart = '[img]' + src + '[/img]';
			qt.TagButton.prototype.callback.call(this, e, c, ed);
		}
	};
	edButtons.push( imgButton );

	var urlButton = new QTags.TagButton('url','url','[url]','[/url]','l');
	urlButton.callback = function(e, c, ed, defaultValue) {
		var URL, t = this;

		//if ( typeof(wpLink) != 'undefined' ) {
		//	wpLink.open();
		//	return;
		//}

		if ( ! defaultValue )
			defaultValue = 'http://';

		if ( t.isOpen(ed) === false ) {
			URL = prompt(quicktagsL10n.enterURL, defaultValue);
			if ( URL ) {
				t.tagStart = '[url]' + URL + '[/url]';
				qt.TagButton.prototype.callback.call(t, e, c, ed);
			}
		} else {
			qt.TagButton.prototype.callback.call(t, e, c, ed);
		}
	};
	edButtons.push( urlButton );

	edButtons.push( new QTags.TagButton('quote','quote','[quote]','[/quote]','q') );
	edButtons.push( new QTags.CloseButton() );


	// wp
	quicktags('comment');

	// bbp
	quicktags('bbp_topic_content');
	quicktags('bbp_reply_content');

	// bp
	quicktags('topic_text');
	quicktags('post_text');
	quicktags('reply_text');
});

/**
 * Client code for frontend_shortlink
 */

(function ($, undefined) {

	var FIELD = 'field-entry_shortlink';
	var FIELD_CLASS = '.' + FIELD;
	var target = $();

	var hookOne = function (index, elem) {
		elem = $(elem);

		var url = elem.attr('data-url');
		var label = elem.attr('data-label');

		if (!!url) {
			var li = $('<li />'),
				link = $('<a />')
				.text(label)
				.attr('class', 'button drawer vertical-right entry-shortlink')
				.attr('href', url)

			li.append(link);

			target.append(li);
		}
	};

	var init = function () {
		target = Symphony.Elements.context.find('.actions');
		if (!target.length) {
			target = $('<ul>').attr('class', 'actions');
			Symphony.Elements.breadcrumbs.after(target);
		}
		return $(FIELD_CLASS).each(hookOne);
	};

	$(init);

})(jQuery);

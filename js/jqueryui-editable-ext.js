/**
* Requires jquery-iframe-transport.js
* The [source for the plugin](http://github.com/cmlenz/jquery-iframe-transport)
* is available on [Github](http://github.com/) and dual licensed under the MIT
* or GPL Version 2 licenses.
*/

/**
Address editable input.
Internally value stored as {city: "Moscow", street: "Lenina", building: "15"}

@class address
@extends abstractinput
@final
@example
<a href="#" id="address" data-type="address" data-pk="1">awesome</a>
<script>
$(function(){
$('#address').editable({
url: '/post',
title: 'Enter city, street and building #',
value: {
city: "Moscow",
street: "Lenina",
building: "15"
}
});
});
</script>
**/
(function ($) {
	var Address = function (options) {
		this.init('address', options, Address.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(Address, $.fn.editabletypes.abstractinput);

	$.extend(Address.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');
		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			$(element).empty();
			if(!value) return;
			var html = $('<div>').text(value.city).html() + ', ' + $('<div>').text(value.street).html() + ' st., bld. ' + $('<div>').text(value.building).html();
			$(element).html(html);
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			city: "Moscow",
			street: "Lenina",
			building: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="city"]').val(value.city);
			this.$input.filter('[name="street"]').val(value.street);
			this.$input.filter('[name="building"]').val(value.building);
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				city: this.$input.filter('[name="city"]').val(),
				street: this.$input.filter('[name="street"]').val(),
				building: this.$input.filter('[name="building"]').val()
			};
		},

		/**
		Activates input: sets focus on the first field.

		@method activate()
		**/
		activate: function() {
			this.$input.filter('[name="city"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	Address.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '<div class="editable-address"><label><span>City: </span><input type="text" name="city" class="input-small"></label></div>'+
		'<div class="editable-address"><label><span>Street: </span><input type="text" name="street" class="input-small"></label></div>'+
		'<div class="editable-address"><label><span>Building: </span><input type="text" name="building" class="input-mini"></label></div>',

		inputclass: ''
	});

	$.fn.editabletypes.address = Address;
} (window.jQuery) );


/**
First/Last name editable input.
Internally value stored as {first: "Arnold", last: "Bailey"}

@class firstlast
@extends abstractinput
@final
@example
<a href="#" id="firstlast" data-type="firstlast" data-pk="1">awesome</a>
<script>
$('#address').editable({
url: '/post',
title: 'Enter First and Last Name',
value: {
first: "Arnold",
last: "Bailey"
}
});
});
</script>
**/

(function ($) {
	var FirstLast = function (options) {
		this.init('firstlast', options, FirstLast.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(FirstLast, $.fn.editabletypes.abstractinput);

	$.extend(FirstLast.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');
		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			$(element).empty();
			if(!value) return;
			$(element).text(value.first + ' ' + value.last);
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			city: "Moscow",
			street: "Lenina",
			building: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
			var firstName = str.split(' ').slice(0, -1).join(' ');
			var lastName = str.split(' ').slice(-1).join(' ');

			ret =JSON.stringify({
				first: firstName,
				last: lastName
			});
			return ret;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="first"]').val(value.first);
			this.$input.filter('[name="last"]').val(value.last);
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				first: this.$input.filter('[name="first"]').val(),
				last: this.$input.filter('[name="last"]').val(),
			};
		},

		/**
		Activates input: sets focus on the first field.

		@method activate()
		**/
		activate: function() {
			this.$input.filter('[name="first"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	FirstLast.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '<div class="editable-firstlast"><label><span>First: </span><input type="text" name="first" class="input-small" placeholder="First Name"></label></div>'+
		'<div class="editable-firstlast"><label><span>Last: </span><input type="text" name="last" class="input-small" placeholder="Last Name"></label></div>',

		inputclass: ''
	});

	$.fn.editabletypes.firstlast = FirstLast;

}(window.jQuery));


/**
File editable input.
Internally value stored as {url: "Moscow", caption: "Lenina", url: "15"}

@class file
@extends abstractinput
@final
@example
<a href="#" id="file" data-type="file" data-pk="1">awesome</a>
<script>
$(function(){
$('#file').editable({
url: '/post',
title: 'Enter image, caption and url #',
value: {
image: "Moscow",
caption: "Lenina",
url: "15"
}
});
});
</script>
**/
(function ($) {

	var File = function (options) {
		this.init('file', options, File.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(File, $.fn.editabletypes.abstractinput);

	$.extend(File.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');

			this.$input.filter('[name="image"]').on('change focus click', function() {
				var $this = $(this),
				$val = $this.val(),
				valArray = $val.split('\\'),
				newVal = valArray[valArray.length-1],
				$button = $this.siblings('button');
				if(newVal !== '') {
					$button.text(newVal);
				}
			});
		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			if(!value) {
				$(element).empty();
				return;
			}
			var html = $('<div>').text(value.image).html() + ', ' + $('<div>').text(value.caption).html() + ' st., bld. ' + $('<div>').text(value.url).html();
			$(element).html(html);
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {image: "Moscow", caption: "Lenina", url: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			image: "Moscow",
			caption: "Lenina",
			url: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="image"]').val(value.image);
			this.$input.filter('[name="caption"]').val(value.caption);
			this.$input.filter('[name="url"]').val(value.url);
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				image: this.$input.filter('[name="image"]').val(),
				caption: this.$input.filter('[name="caption"]').val(),
				url: this.$input.filter('[name="url"]').val()
			};
		},

		/**
		Activates input: sets focus on the first field.

		@method activate()
		**/
		activate: function() {
			this.$input.filter('[name="image"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	File.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '<div class="editable-file">' +
		'<span style="position:relative; display: inline-block; overflow: hidden; cursor: pointer;">' +
		'<input type="file" name="image" class="input-small" size="1" style="opacity: 0;filter: alpha(opacity=0); cursor: pointer; font-size: 400%; height: 600%; position: absolute; top: 0; right: 0; width: 240%" />' +
		'<button type="button" style="cursor: pointer; display: inline-block; margin-right: 5px;  ">Chose file</button>' +
		'</span></div>',

		inputclass: '',

		ajaxOptions: {iframe: true }
	});

	$.fn.editabletypes.file = File;
} (window.jQuery) );


/**
Link/Url name editable input.
Internally value stored as {link: "WebWrights", url: "http://webwrights.com"}

@class link
@extends abstractinput
@final
@example
<span
data-type="link"
data-pk="1"
data-link-label="Company"
data-url-label="URL"
data-value="{link: &quot;WebWrights&quot;, url=&quot;http://company.com&quot;}">
</span>
<script>

$('#company').editable({
url: '/post',
title: 'Enter Compny name and URL',
value: {
link: "WebWrights",
url: "http://webwrights.com"
}
});

</script>
**/

(function ($) {
	var Link = function (options) {
		this.init('link', options, Link.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(Link, $.fn.editabletypes.abstractinput);

	$.extend(Link.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');
			linkLabel = $(this.options.scope).data('link-label');
			linkPlaceholder = $(this.options.scope).data('link-placeholder');
			urlLabel = $(this.options.scope).data('url-label');
			urlPlaceholder = $(this.options.scope).data('url-placeholder');
			if(typeof linkLabel === 'string') this.$tpl.find('.link-label').text(linkLabel);
			if(typeof linkPlaceholder === 'string') this.$tpl.find('[name=link]').attr('placeholder', linkPlaceholder);
			if(typeof urlLabel === 'string') this.$tpl.find('.url-label').text(urlLabel);
			if(typeof urlPlaceholder === 'string') this.$tpl.find('[name=url]').attr('placeholder', urlPlaceholder);
		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			$(element).empty();
			if(!value) return;
			$(element).append($('<a target="_blank">').prop('href', value.url).html(value.link));
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			city: "Moscow",
			street: "Lenina",
			building: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="link"]').val(value.link);
			this.$input.filter('[name="url"]').val(value.url);
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				link: this.$input.filter('[name="link"]').val(),
				url: this.$input.filter('[name="url"]').val(),
			};
		},

		/**
		Activates input: sets focus on the link field.

		@method activate()
		**/
		activate: function() {
			this.$input.filter('[name="link"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	Link.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl:	'<div class="editable-link"><label><span class="link-label">Link: </span><input type="text" name="link" class="input-small"></label></div>'+
		'<div class="editable-link"><label><span class="url-label">URL: </span><input type="text" name="url" class="input-small"></label></div>',

		inputclass: ''
	});

	$.fn.editabletypes.link = Link;

}(window.jQuery));

/**
Portfolio editable input.
Internally value stored as {url: "Moscow", caption: "Lenina", url: "15"}

@class portfolio
@extends abstractinput
@final
@example
<a href="#" id="portfolio" data-type="portfolio" data-pk="1">awesome</a>
<script>
$(function(){
$('#portfolio').editable({
url: '/post',
title: 'Enter file, caption and url #',
value: {
file: "Moscow",
caption: "Lenina",
url: "15"
}
});
});
</script>
**/
(function ($) {
	var Portfolio = function (options) {
		this.init('portfolio', options, Portfolio.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(Portfolio, $.fn.editabletypes.abstractinput);

	$.extend(Portfolio.prototype, {

		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');
			this.$textarea = this.$tpl.find('textarea');

			//			this.$remove = $('<button>').attr({ class: "editable-remove", title: "Remove"}).button({
			//				icons: { primary: "ui-icon-trash" },
			//				text: false
			//			});

			//alert(JSON.stringify(this));

			this.$buttons = this.$input.closest('form').find('.editable-buttons');
			//this.$buttons.append( this.$remove );
			//alert(this.$buttons[0].innerHTML);
			this.$count = this.$tpl.find('span').filter('[class="char-count"]');

			this.maxlength = $(this.options.scope).data('maxlength');

			this.maxlength = (this.maxlength) ? this.maxlength : 400;

			//Change labels if defined
			linkLabel = $(this.options.scope).data('link-label');
			descriptionLabel = $(this.options.scope).data('description-label');
			imageLabel = $(this.options.scope).data('image-label');
			removeLabel = $(this.options.scope).data('remove-label');
			countLabel = $(this.options.scope).data('count-label');

			if(typeof linkLabel === 'string') this.$tpl.find('.link-label').text(linkLabel);
			if(typeof descriptionLabel === 'string') this.$tpl.find('.description-label').text(descriptionLabel);
			if(typeof imageLabel === 'string') this.$tpl.find('.image-label').text(imageLabel);
			if(typeof removeLabel === 'string') this.$tpl.find('.remove-label').text(removeLabel);
			if(typeof countLabel === 'string') this.$tpl.find('.count-label').text(countLabel);


		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			$(element).empty();
			if(!value) return;

			if(value.src){
				var caption = value.caption
				if( value.url.length) caption = caption + ' <a href="' + value.url + '"> <span style="float: right; margin-right: 3em;">' + value.url + '</span></a>';

				var $link = $('<a>').prop( {href: value.src, title: caption} );
				var $img = $('<img>').prop( {src: value.src, title: caption} );
				var $del_icon = $('<a>').prop( {href: '#', class: 'editable-remove'} );

				$(element).append( $link.append($img), $del_icon);

				//attach deletion call
				$del_icon.on('click', function(e){
					e.preventDefault(); e.stopPropagation();
					var $element = $(this).closest('.editable');
					var edata = $element.data('editable');
					//alert(JSON.stringify(edata));
					edata.value.remove = 'remove';
					$element.editable('submit', {
						url: edata.options.url,
						data: {
							name: edata.options.name,
							value: edata.value,
							pk: edata.options.pk,
							action: edata.options.params.action,
							_wpnonce: edata.options.params._wpnonce
						},
						success: function(){
							$element.closest('li').remove();
						}
					})
				});
			}
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}

			this.$input.filter('[name="attachment_id"]').val(value.attachment_id);
			this.$input.filter('[name="url"]').val(value.url);
			this.$input.filter('[name="src"]').val(value.src);
			this.$textarea.filter('[name="caption"]').val(value.caption);
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {

			result =  {
				attachment_id: this.$input.filter('[name="attachment_id"]').val(),
				url: this.$input.filter('[name="url"]').val(),
				src: this.$input.filter('[name="src"]').val(),
				caption: this.$textarea.filter('[name="caption"]').val(),
				remove: this.$input.filter('[name="remove"]').val(),
				file: this.$input.filter('[name="file"]').val().split('\\').pop()
			};
			return result;
		},

		/**
		Activates input: sets focus on the first field.

		@method activate()
		**/
		activate: function() {
			//Change file input to button
			this.$input.filter('[name="file"]').on('change focus click', function() {
				var $this = $(this),
				newVal = $this.val().split('\\').pop(),
				$selected = $this.parent().siblings('.image-label-selected');

				if(newVal !== '') {
					console.log($selected.text() );
					$selected.text(newVal);
				}
			});

			$('button.image-label').button();
			//			var $remove = this.$input.filter('[name="remove"]');
			//			this.$buttons.find('.editable-remove').on('click', function() {
			//				$remove.val('remove');
			//			});

			//Set file specific option and success callback to load new image reference
			//Hard to know if the file is remote so just force the send
			$(this.options.scope).editable('option', 'savenochange', true );

			//Server should return error string or newValue
			$(this.options.scope).editable('option', 'success', function(response, newValue){ return response; });

			//iframe transport specific ajaxOptions.
			$(this.options.scope).editable('option', 'ajaxOptions', {
				dataType: 'json',
				iframe: true,
				files: this.$input.filter('[name="file"]')
			});

			$(this.options.scope).editable('option', 'validate', function(value){
				if(value.file=='' && value.src == '') return 'Requires an image file!';
			});

			//Character counter
			var maxlength = this.maxlength;
			var count = this.$count;

			this.$textarea.on('keyup focus onload', function(){
				var $this = $(this);
				var chars = $this.val().length;
				count.text(maxlength - chars);
				var color = $this.css('border-color');
				if(maxlength < chars){
					$this.css('border-color', 'red');
					$this.val( $this.val().substr(0, maxlength) );
				} else {
					$this.css('border-color', color);
				}
			});

			this.$textarea.val( this.$textarea.val().substr(0, maxlength) );
			count.text(maxlength - this.$textarea.val().length );

			this.$input.filter('[name="file"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	Portfolio.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl:
		'<div class="editable-portfolio" >'+
		'<div style="position: relative; display: inline-block; overflow: hidden; cursor: pointer; vertical-align: middle;">' +
		'<input type="file" class="editable-portfolio" name="file" size="1" />' +
		'<button type="button" class="image-label editable-button" style="cursor: pointer; display: inline-block; margin: 0; ">Choose file</button>' +
		'</div>' +
		'&nbsp;<span class="image-label-selected">No file chosen</span>' +
		'</div>'+
		'<div class="editable-portfolio"><label><span class="link-label">Add a Link</span><br /><input type="text" name="url" class="input-large"></label></div>'+
		'<div class="editable-portfolio"><label><span class="caption-label">Description</span><br /><textarea name="caption" class="input-large" rows="4"></textarea></label>' +
		'<div class="editable-portfolio"><label class="labelright"><span class="char-count"></span> <span class="count-label">characters left</span></label></div>' +
		'<input type="hidden" name="attachment_id" />' +
		'<input type="hidden" name="src" />' +
		'<input type="hidden" name="remove" />' +
		'</div>',

		inputclass: ''
	});

	$.fn.editabletypes.portfolio = Portfolio;
} (window.jQuery) );


/**
Skill name editable input.
Internally value stored as {skill: "PHP programmer", percent: "60"}

@class skill
@extends abstractinput
@final
@example
<a href="#" id="skill" data-type="skill" data-pk="1">awesome</a>
<script>
$('#address').editable({
url: '/post',
title: 'Enter First and Last Name',
value: {
skill: "Arnold",
percent: "Bailey"
}
});
});
</script>
**/

(function ($) {
	var Skill = function (options) {
		this.init('skill', options, Skill.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(Skill, $.fn.editabletypes.abstractinput);

	$.extend(Skill.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');


			this.$buttons = this.$input.closest('form').find('.editable-buttons');
			//			this.$remove = $('<button>').attr({ class: "editable-remove", title: "Remove"}).button({
			//				icons: { primary: "ui-icon-trash" },
			//				text: false
			//			});
			//			this.$buttons.append( this.$remove);

			var $number = this.$tpl.find('span').filter('[class="number"]');

			this.$percent = this.$tpl.find('div').filter('[class="percent"]');
			$(this.$percent).slider({
				range: 'min',
				min: 0,
				max: 100,
				slide: function(){ $($number).text($(this).slider('value')+ '%'); },
				change: function(){ $($number).text($(this).slider('value')+ '%'); }
			});

			//Labels from data
			skillLabel = $(this.options.scope).data('skill-label');
			percentLabel = $(this.options.scope).data('percent-label');

			if(typeof skillLabel === 'string') this.$tpl.find('.skill-label').text(skillLabel);
			if(typeof percentLabel === 'string') this.$tpl.find('.percent-label').text(percentLabel);


		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			$(element).empty();
			if(!value) return;

			var $bar = $('<div>').attr({class: 'skill-bar'});
			var $percent = $('<div>').attr({ class: 'skill-percent'}).css({width: value.percent + '%'});
			var $skill = $('<p>').text(value.skill);
			var $del_icon = $('<a>').prop( {href: '#', class: 'editable-remove'} );

			$(element).append( $bar.append($percent), $del_icon, $skill);

			//attach deletion call
			$del_icon.on('click', function(e){
				e.preventDefault(); e.stopPropagation();
				var $element = $(this).closest('.editable');
				var edata = $element.data('editable');
				//alert(JSON.stringify(edata));
				edata.value.remove = 'remove';
				$element.editable('submit', {
					url: edata.options.url,
					data: {
						name: edata.options.name,
						value: edata.value,
						pk: edata.options.pk,
						action: edata.options.params.action,
						_wpnonce: edata.options.params._wpnonce
					},
					success: function(){
						$element.closest('li').remove();
					}
				})
			});
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			city: "Moscow",
			street: "Lenina",
			building: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="skill_id"]').val(value.skill_id);
			this.$input.filter('[name="skill"]').val(value.skill);
			this.$percent.slider('option', 'value', value.percent)
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				skill_id: this.$input.filter('[name="skill_id"]').val(),
				skill: this.$input.filter('[name="skill"]').val(),
				remove: this.$input.filter('[name="remove"]').val(),
				percent: this.$percent.slider('option', 'value')
			};
		},

		/**
		Activates input: sets focus on the skill field.

		@method activate()
		**/
		activate: function() {

			$(this.options.scope).editable('option', 'ajaxOptions', {
				dataType: 'json',
			});

			//Server should return error string or newValue
			$(this.options.scope).editable('option', 'success', function(response, newValue){ return response; });

			var $remove = this.$input.filter('[name="remove"]');
			this.$buttons.find('.editable-remove').on('click', function() {
				$remove.val('remove');
			});
			this.$input.filter('[name="skill"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	Skill.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl:
		'<div class="editable-skill">'+
		//'<label><span class="skill-label">Skill: </span><br />'+
		'<input type="text" name="skill" class="input-medium"></label></div>'+
		'<div class="editable-skill">'+
		'<span class="percent-label">How good you are 1-100%</span>'+
		'<div class="percent"></div>'+
		'<span class="number">0%</span>' +
		'<input type="hidden" name="skill_id" />' +
		'<input type="hidden" name="remove" />' +
		'</div>' ,

		inputclass: ''
	});

	$.fn.editabletypes.skill = Skill;

}(window.jQuery));

/**
Social Socials editable input.
Internally value stored as {social: "facebook", url: "http://webwrights.com"}

@class social
@extends abstractinput
@final
@example
<span
data-type="social"
data-pk="1"
data-social-label="Company"
data-url-label="URL"
data-value="{social: &quot;WebWrights&quot;, url=&quot;http://company.com&quot;}">
</span>
<script>

$('#company').editable({
url: '/post',
title: 'Enter Compny name and URL',
value: {
social: "WebWrights",
url: "http://webwrights.com"
}
});

</script>
**/

(function ($) {
	var Social = function (options) {
		this.init('social', options, Social.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(Social, $.fn.editabletypes.abstractinput);

	$.extend(Social.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');
			this.$form = this.$input.closest('form');

			//			//add remove button
			//			this.$buttons = this.$input.closest('form').find('.editable-buttons');
			//			this.$buttons.append( $('<button class="editable-remove">remove</button>').button({
			//				icons: { primary: "ui-icon-trash" },
			//				text: false
			//			}).attr('title', 'Remove') );

			//alert(this.$buttons[0].outerHTML);

			var socialLabel = $(this.options.scope).data('social-label');
			var urlLabel = $(this.options.scope).data('url-label');
			if(typeof socialLabel === 'string') this.$tpl.find('.social-label').text(socialLabel);
			if(typeof urlLabel === 'string') this.$tpl.find('.url-label').text(urlLabel);


			var $social = this.$input.filter('[name="social"]');
			var $label = this.$tpl.find('.social-label');
			var $social_id = this.$input.filter('[name="social_id"]');
			var $social_url = this.$tpl.find('input.social-url');

			$('.social_i').on('click', function(e){
				var that = $(this);
				e.preventDefault();
				$('.social_i').removeClass('active');
				that.toggleClass('active');
				$label.text(that.attr('title'));
				$social.val(that.attr('title'));
				$social_id.val( that.attr('class').split(' ')[0] ); //First class in class list must be id like 'fb' = Facebook
				console.log(that);
				$social_url.val( that.text() );
			});
		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			//			alert(JSON.stringify(value) );
			$(element).empty();
			if(!value) return;

			var $link = $('<a>').prop({href: value.url, rel: "nofollow", class: value.social_id + " social_i", target: value.social_id });
			var $del_icon = $('<a>').prop( {href: '#', class: 'editable-remove'} );
			$(element).append($link, $del_icon);

			//attach deletion call
			$del_icon.on('click', function(e){
				e.preventDefault(); e.stopPropagation();
				var $element = $(this).closest('.editable');
				var edata = $element.data('editable');
				//alert(JSON.stringify(edata));
				edata.value.remove = 'remove';
				$element.editable('submit', {
					url: edata.options.url,
					data: {
						name: edata.options.name,
						value: edata.value,
						pk: edata.options.pk,
						action: edata.options.params.action,
						_wpnonce: edata.options.params._wpnonce
					},
					success: function(){
						$element.closest('li').remove();
					}
				})
			});



		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			city: "Moscow",
			street: "Lenina",
			building: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="social"]').val(value.social);
			this.$input.filter('[name="url"]').val(value.url);
			this.$input.filter('[name="social_id"]').val(value.social_id);
			$('a[title="' + value.social + '"]').toggleClass('active');
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				social: this.$input.filter('[name="social"]').val(),
				url: this.$input.filter('[name="url"]').val(),
				social_id: this.$input.filter('[name="social_id"]').val(),
				remove: this.$input.filter('[name="remove"]').val(),
			};
		},

		/**
		Activates input: sets focus on the social field.

		@method activate()
		**/
		activate: function() {

			$(this.options.scope).editable('option', 'ajaxOptions', {
				dataType: 'json',
			});

			//Server should return error string or newValue
			$(this.options.scope).editable('option', 'success', function(response, newValue){ return response; });

			//			var $remove = this.$input.filter('[name="remove"]');
			//			this.$buttons.find('.editable-remove').on('click', function() {
			//				$remove.val('remove');
			//			});

			this.$input.filter('[name="social"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	Social.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl:
		'<div class="pro-social">' +
		'<ul>' +
		'<li><a class="sh social_i" rel="nofollow" title="Share" href="#">http://</a></li>' +
		'<li><a class="fb social_i" rel="nofollow" title="Facebook" href="#">http://facebook.com/</a></li>' +
		'<li><a class="tw social_i" rel="nofollow" title="Twitter: @username" href="#">http://twitter.com/</a></li>' +
		'<li><a class="gp social_i" rel="nofollow" title="Google+" href="#">http://plus.google.com</a></li>' +
		'<li><a class="rs social_i" rel="nofollow" title="RSS Feed" href="#">http://</a></li>' +
		'<li><a class="pt social_i" rel="nofollow" title="Pintrest" href="#">http://pintrest.com/</a></li>' +
		'<li><a class="li social_i" rel="nofollow" title="LinkedIn" href="#">http://linkedin.com</a></li>' +
		'<li><a class="yt social_i" rel="nofollow" title="YouTube" href="#">http://youtube.com</a></li>' +
		'<li><a class="em social_i" rel="nofollow" title="Email" href="#">mailto:</a></li>' +
		'<li><a class="ad social_i" rel="nofollow" title="Add This" href="#">http://addthis.com</a></li>' +
		'<li><a class="rd social_i" rel="nofollow" title="Reddit" href="#">http://reddit.com</a></li>' +
		'<li><a class="su social_i" rel="nofollow" title="Stumble Upon" href="#">http://stumbleupon.com/</a></li>' +
		'<li><a class="dl social_i" rel="nofollow" title="Delicious" href="#">http://delicious.com/</a></li>' +
		'<li><a class="dg social_i" rel="nofollow" title="Digg" href="#">http://digg.com/</a></li>' +
		'<li><a class="sk social_i" rel="nofollow" title="Skype (SKYPE:skype id)" href="#">SKYPE:</a></li>' +
		'<li><a class="ig social_i" rel="nofollow" title="Instagram" href="#">http://instagram.com/</a></li>' +
		'<li><a class="vi social_i" rel="nofollow" title="Vimeo" href="#">http://vimeo.com/</a></li>' +
		'<li><a class="tm social_i" rel="nofollow" title="Tumblr" href="#">http://tumblr.com/</a></li>' +
		'</ul>' +
		'</div>' +
		'<div class="editable-social"><label><span class="social-label">Social: </span>'+
		' <span class="url-label">URL: </span><input class="social-url input-medium" type="text" name="url"></label>' +
		'<input type="hidden" name="social">' +
		'<input type="hidden" name="social_id">' +
		'<input type="hidden" name="remove">' +
		'</div>',

		inputclass: ''
	});

	$.fn.editabletypes.social = Social;

}(window.jQuery));



/**
Social Single Socials editable input.
Internally value stored as {social: "facebook", url: "http://webwrights.com"}

@class social
@extends abstractinput
@final
@example
<span
data-type="social"
data-pk="1"
data-social-label="Company"
data-url-label="URL"
data-value="{social: &quot;WebWrights&quot;, url=&quot;http://company.com&quot;}">
</span>
<script>

$('#company').editable({
url: '/post',
title: 'Enter Compny name and URL',
value: {
social: "WebWrights",
url: "http://webwrights.com"
}
});

</script>
**/

(function ($) {
	var Social_Single = function (options) {
		this.init('social_single', options, Social_Single.defaults);
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit(Social_Single, $.fn.editabletypes.abstractinput);

	$.extend(Social_Single.prototype, {
		/**
		Renders input from tpl

		@method render()
		**/
		render: function() {
			this.$input = this.$tpl.find('input');
			this.$form = this.$input.closest('form');

			var social_labels = {
				'sh': 'Share',
				'fb': 'Facebook',
				'tw': 'Twitter',
				'gp': 'Google+',
				'rs': 'RSS Feed',
				'pt': 'Pintrest',
				'li': 'LinkedIn',
				'yt': 'YouTube',
				'em': 'Email',
				'ad': 'Add This',
				'rd': 'Reddit',
				'su': 'Stumble Upon',
				'dl': 'Delicious',
				'dg': 'Digg',
				'sk': 'Skype'
			};
			var social = this.$input.filter('[name="social"]');
			var social_id = this.$input.filter('[name="social_id"]');
			var id = $(this.options.scope).data('social-id');

			$(social).val(social_labels[id]);
			$(social_id).val(id);
		},

		/**
		Default method to show value in element. Can be overwritten by display option.

		@method value2html(value, element)
		**/
		value2html: function(value, element) {
			$(element).empty();
			if(!value) return;
			$(element).text(value.url);
		},

		/**
		Gets value from element's html

		@method html2value(html)
		**/
		html2value: function(html) {
			/*
			you may write parsing method to get value by element's html
			e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
			but for complex structures it's not recommended.
			Better set value directly via javascript, e.g.
			editable({
			value: {
			city: "Moscow",
			street: "Lenina",
			building: "15"
			}
			});
			*/
			return null;
		},

		/**
		Converts value to string.
		It is used in internal comparing (not for sending to server).

		@method value2str(value)
		**/
		value2str: function(value) {
			var str = '';
			if(value) {
				for(var k in value) {
					str = str + k + ':' + value[k] + ';';
				}
			}
			return str;
		},

		/*
		Converts string to value. Used for reading value from 'data-value' attribute.

		@method str2value(str)
		*/
		str2value: function(str) {
			/*
			this is mainly for parsing value defined in data-value attribute.
			If you will always set value by javascript, no need to overwrite it
			*/
			return str;
		},

		/**
		Sets value of input.

		@method value2input(value)
		@param {mixed} value
		**/
		value2input: function(value) {
			if(!value) {
				return;
			}
			this.$input.filter('[name="social"]').val(value.social);
			this.$input.filter('[name="url"]').val(value.url);
			this.$input.filter('[name="social_id"]').val(value.social_id);
		},

		/**
		Returns value of input.

		@method input2value()
		**/
		input2value: function() {
			return {
				social: this.$input.filter('[name="social"]').val(),
				url: this.$input.filter('[name="url"]').val(),
				social_id: this.$input.filter('[name="social_id"]').val(),
				remove: this.$input.filter('[name="remove"]').val(),
			};
		},

		/**
		Activates input: sets focus on the social field.

		@method activate()
		**/
		activate: function() {

			$(this.options.scope).editable('option', 'ajaxOptions', {
				dataType: 'json',
			});

			//Server should return error string or newValue
			$(this.options.scope).editable('option', 'success', function(response, newValue){ return response; });

			this.$input.filter('[name="url"]').focus();
		},

		/**
		Attaches handler to submit form in case of 'showbuttons=false' mode

		@method autosubmit()
		**/
		autosubmit: function() {
			this.$input.keydown(function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
		}
	});

	Social_Single.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
		tpl:
		'<div class="editable-social"><label><input type="text" name="url" class="input-medium"></label>' +
		'<input type="hidden" name="social">' +
		'<input type="hidden" name="social_id">' +
		'<input type="hidden" name="remove">' +
		'</div>',

		inputclass: ''
	});

	$.fn.editabletypes.social_single = Social_Single;

}(window.jQuery));

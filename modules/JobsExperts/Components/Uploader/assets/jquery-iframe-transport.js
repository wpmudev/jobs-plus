/**
* The [source for the plugin](http://github.com/cmlenz/jquery-iframe-transport)
* is available on [Github](http://github.com/) and dual licensed under the MIT
* or GPL Version 2 licenses.
*/

(function($, undefined) {
	"use strict";

	$.has_iframe_transport = true;

	$.ajaxPrefilter(function(options, origOptions, jqXHR) {
		if (options.iframe) {
			return "iframe";
		}
	});

	$.ajaxTransport("iframe", function(options, origOptions, jqXHR) {
		var form = null,
		iframe = null,
		name = "iframe-" + $.now(),
		files = $(options.files).filter(":file:enabled"),
		markers = null,
		plus = /\+/g;

		function cleanUp() {
			markers.replaceWith(function(idx) {
				return files.get(idx);
			});
			form.remove();
			iframe.attr("src", "javascript:false;").remove();
		}

		options.dataTypes.shift();

		if (files.length) {
			form = $("<form enctype='multipart/form-data' method='post'></form>").
			hide().attr({action: options.url, target: name});

			$.each( options.data.split('&') || {}, function(name, value) {
				var tuple = value.split('=');
				$("<input type='hidden' />").attr({name:  decodeURIComponent(tuple[0].replace(plus,' ')), value: decodeURIComponent(tuple[1].replace(plus, ' ')) }).
				appendTo(form);
			});

			$("<input type='hidden' value='IFrame' name='X-Requested-With' />").
			appendTo(form);

			markers = files.after(function(idx) {
				return $(this).clone().prop("disabled", true);
			}).next();
			files.appendTo(form);

			return {

				send: function(headers, completeCallback) {
					iframe = $("<iframe src='javascript:false;' name='" + name +
					"' id='" + name + "' style='display:none'></iframe>");

					iframe.bind("load", function() {

						iframe.unbind("load").bind("load", function() {
							var doc = this.contentWindow ? this.contentWindow.document :
							(this.contentDocument ? this.contentDocument : this.document),
							root = doc.documentElement ? doc.documentElement : doc.body,
							textarea = root.getElementsByTagName("textarea")[0],
							type = textarea && textarea.getAttribute("data-type") || null,
							status = textarea && textarea.getAttribute("data-status") || 200,
							statusText = textarea && textarea.getAttribute("data-statusText") || "OK",
							content = {
								html: root.innerHTML,
								text: type ?
								textarea.value :
								root ? (root.textContent || root.innerText) : null
							};
							cleanUp();
							completeCallback(status, statusText, content, type ?
							("Content-Type: " + type) :
							null);
						});

						form[0].submit();
					});

					$("body").append(form, iframe);
				},

				abort: function() {
					if (iframe !== null) {
						iframe.unbind("load").attr("src", "javascript:false;");
						cleanUp();
					}
				}

			};
		}
	});

})(jQuery);

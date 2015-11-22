
if (typeof jQuery === "undefined") { throw new Error("CmsCommon's collection.js requires jQuery"); }

; var CmsCommon = {};
CmsCommon.Form = {};

;(function($){

	CmsCommon.Form.Collection = {
		addFieldset: function(button, placement) {
			var fieldset = $(button).closest("fieldset"),
				template = fieldset.find(">span").data("template"),
				elements = fieldset.find(">[data-counter]"),
				minMax = this.minMaxCounter(elements);

			template = template.replace(/__index__|--index--/g, minMax.max === null ? 0 : minMax.max + 1);
			if (!placement || placement === "append") {
				fieldset.append(template);
			} else {
				var prepended = $(">input[type=hidden]", fieldset);
				if (!prepended.length) {
					prepended = $(">legend", fieldset);
				}

				if (prepended) {
					prepended.after(template);
				} else {
					fieldset.prepend(template);
				}
			}

			$(fieldset).trigger("addFieldset", [template]);

	        return false;
		},

		removeFieldset: function(button) {
			var fieldset = $(button).closest("fieldset");
			$(fieldset).trigger("removeFieldset");
			$(button).closest("[data-counter]").remove();
		},

		minMaxCounter: function(selector)  {
			var min = null, max = null;
			if (selector.length) {
				$(selector).each(function() {
					var id = parseInt($(this).data("counter"), 10);
				    if ((min===null) || (id < min)) { min = id; }
				    if ((max===null) || (id > max)) { max = id; }
				});
			}

			return {min: min, max: max};
		}
	};

})(jQuery);

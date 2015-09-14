
if (typeof jQuery === "undefined") { throw new Error("CmsCommon's collection.js requires jQuery"); }

; var CmsCommon = {};
CmsCommon.Form = {};

;(function($){

	CmsCommon.Form.Collection = {
		addFieldset: function(button, placement) {
			var fieldset = $(button).closest("fieldset"),
				template = $(fieldset).find(">span").data("template"),
				minMax = this.minMaxCounter($(fieldset).find(">[data-counter]"));

	        template = template.replace(/__index__|--index--/g, minMax.max === null ? 0 : minMax.max + 1);
	        if (!placement || placement === "append") {
	        	fieldset.append(template);
	        } else {
	        	if (fieldset.find("[type=hidden]")) {
	        		fieldset.find("[type=hidden]").after(template);
	        	} else if (fieldset.find(">legend")) {
	        		fieldset.find(">legend").after(template);
	        	} else {
	        		fieldset.prepend(template);
	        	}
	        	$(".selectpicker").selectpicker('refresh');
	        }
	        return false;
		},

		removeFieldset: function(button) {
			$(button).closest("[data-counter]").remove();
		},

		minMaxCounter: function(selector)  {
			var min = null, max = null;
			$(selector).each(function() {
				var id = parseInt($(this).data("counter"), 10);
			    if ((min===null) || (id < min)) { min = id; }
			    if ((max===null) || (id > max)) { max = id; }
			});

			return {min: min, max: max};
		}
	};

})(jQuery);

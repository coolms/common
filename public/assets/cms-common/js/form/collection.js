
if (typeof jQuery === "undefined") { throw new Error("CmsCommon's collection.js requires jQuery"); }

; var CmsCommon = {};
CmsCommon.Form = {};

;(function($){

	CmsCommon.Form.Collection = {

		addFieldset: function(button, placement)
		{
			if (!placement) {
				placement = "append";
			}
			var fieldset = $(button).closest("fieldset");
			var template = $(fieldset).find(">span").data("template");
			if (placement === "append") {
				var currentCount = $(fieldset).find(">fieldset").last().data("counter");
			} else {
				var currentCount = $(fieldset).find(">fieldset").first().data("counter");
			}
	        if (typeof currentCount === "undefined") {
	        	currentCount = -1;
	        }
	        template = template.replace(/__index__|--index--/g, currentCount + 1);
	        if (placement === "append") {
	        	$(fieldset).append(template);
	        } else {
	        	$(fieldset).prepend(template);
	        }
	        return false;
		},

		removeFieldset: function(button)
		{
			var fieldset = $(button).closest("fieldset");
			$(fieldset).remove();
		}
	};

})(jQuery);


var Sites = {
	
	approve: function() {
		id = jQuery(this).attr('id').substring(8);
		Sites.element = this;
		jQuery.post("approve/" + id, {}, Sites.approveResponseHandler);
	
	},
	
	approveResponseHandler: function(response, a, b) {
		response = JSON.parse(response);
		jQuery(Sites.element).replaceWith(response.date_approved);
	},

};

jQuery(document).ready(function() {
	jQuery('.approve').click(Sites.approve);	
}); 
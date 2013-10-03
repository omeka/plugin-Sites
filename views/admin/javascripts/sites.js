var Sites = {
	
	approve: function() {
		id = jQuery(this).attr('id').substring(8);
		Sites.element = this;
		url = webRoot + '/admin/sites/approve';
		jQuery.post(url, {"id" : id}, Sites.approveResponseHandler);
	
	},
	
	approveResponseHandler: function(response, a, b) {
		response = JSON.parse(response);
		jQuery(Sites.element).replaceWith(response.date_approved);
	},

};

jQuery(document).ready(function() {
	jQuery('.approve').click(Sites.approve);	
}); 
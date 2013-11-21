var Sites = {
	
	approve: function() {
		id = jQuery(this).attr('id').substring(8);
		Sites.element = this;
		url = webRoot + '/admin/sites/approve';
		jQuery.post(url, {"id" : id}, Sites.approveResponseHandler);
	
	},
	
	approveResponseHandler: function(response, status, jqXHR) {
		response = JSON.parse(response);
		console.log(response);
		jQuery(Sites.element).replaceWith(response.date_approved);
		jQuery("input[value = '" + response.id + "']").remove();
	},
	
	setUpBatchApprove: function() {
	    var approveButton = jQuery('#batch-approve');
	    var globalCheckbox = jQuery('#check-all');
	    var siteCheckboxes = jQuery('.site-checkbox');
	    
	    approveButton.prop('disabled', true);
	    
        globalCheckbox.change(function() {
            siteCheckboxes.prop('checked', !!this.checked);
            checkBatchApproveButton();
        });	    
	    
        siteCheckboxes.change(function(){
            if (!this.checked) {
                globalCheckbox.prop('checked', false);
            }
            checkBatchApproveButton();
        });       
        
        function checkBatchApproveButton() {
            var checked = false;
            siteCheckboxes.each(function() {
                if (this.checked) {
                    checked = true;
                    return false;
                }
            });

            approveButton.prop('disabled', !checked);
        }        
	    
	}
};

jQuery(document).ready(function() {
	jQuery('.approve').click(Sites.approve);	
	Sites.setUpBatchApprove();
}); 
_NChkCond(function(){return typeof(CKEDITOR) != 'undefined'}, function(){
	CKEDITOR.on('currentInstance', function(e)
	{
	   if(e.sender._lastInstance && e.sender._lastInstance != e.sender.currentInstance)
	   {
	      if(e.sender._lastInstance.checkDirty())
	      {
	         e.sender._lastInstance.updateElement();
			 var id = e.sender._lastInstance.name;
			 _NSetProperty(id, 'value', e.sender._lastInstance.getData());
	      }
	   }
	   e.sender._lastInstance = e.sender.currentInstance;
	})});
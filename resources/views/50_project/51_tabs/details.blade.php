<div class="row m-0 mb-3 box-shadow-bottom">
	<div class="card w-100">
		<form method="post" action="{{ $project_url }}">
		{{ csrf_field() }}
		<input type="hidden" name="redirect" value="true" />
		<h6 class="card-header {{-- dtitle --}} p-2">{{$project->title}}
			<i class="fa fa-pencil-square-o env_edit pull-right" id="edit_details"></i>
		</h6>
	  	<div class="card-body">
	  		<div id="details_content">
		    	<p class="card-text text-justify">
		    		{{$project->description}}
		    	</p>
		    	<p class="card-text text-justify">
		    		{!! $details !!}
		    	</p>
	    	</div>
	    	<div id="details_editor" style="display: none;">
		  		<div class="form-group">
		            <textarea name="description" class="form-control" id="description" aria-describedby="descriptionHelp" placeholder="Enter description" rows="3" required="required">{{$project->description}}</textarea>
	          	</div>
	          	<div class="form-group">
	    			<textarea name="details" id="details_editor_area" rows="200" cols="100">{{$details_raw}}</textarea>
	    		</div>
	    		<div class="form-group">
	    			<button type="button" class="btn btn-secondary" id="details_editor_close">Close</button>
        			<button type="submit" class="btn btn-primary">Save changes</button>
	    		</div>
	    	</div>
	  	</div>
	  	<div class="card-footer env_uploaded_div pl-2">
	  		<p class="env_p">Uploaded at {{ $project->created_at }}</p>
	  	</div>
	  	</form>
	</div>
</div>

<script type="text/javascript">
  var details_editor = new SimpleMDE({ 
  	element: document.getElementById("details_editor_area"),
  	forceSync: true,
  	previewRender: function(plainText) {
		return this.parent.markdown(plainText);
	}
  });
  $('#edit_details').click(function() {
  	$('#details_content').hide();
  	$('#details_editor').show();
  	details_editor.value($("#details_editor_area").val());
  });
  $('#details_editor_close').click(function() {
  	$('#details_content').show();
  	$('#details_editor').hide();
  });	
</script>

{{--
<div class="row m-0 pb-3">
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#aquaponic</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#container</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#apple</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#pear</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#pirate</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#farm</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#water</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#food</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#solution</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#gate</a></button>
	<button class="btn btn-sm btn-outline-info mr-2 mb-2 badge badge-pill"><a id="env_link" href="#">#block</a></button>
</div>
--}}




<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#pickregistrationimage">
  Pick registration image
</button>

<!-- Modal -->
<div class="modal fade" id="pickregistrationimage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post" action="/admin/registration/image" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Pick registration page image</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <x-multi-language-image-input :name="'pick_registration_image_url'" :localization="$image->localization" :value="$image->value" :maxWidth="'40%;'"></x-multi-language-image-input>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
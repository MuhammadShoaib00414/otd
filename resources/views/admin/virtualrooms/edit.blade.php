@extends('admin.layout')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css"/>
    <style>
        .selectedClickArea {
            box-shadow: 0 0 0 2px white inset;
        }
    </style>
@endsection

@section('page-content')
    <div id="roomBuilder">
        <div class="row pl-3">
            <h4>Edit {{ $virtualRoom->is_mobile ? 'Mobile' : 'Desktop' }} Interactive Header Image</h4>
            @if($virtualRoom->is_mobile)
                <span class="ml-2">(<a href="/admin/virtual-rooms/{{ getsetting('dashboard_virtual_room_id') }}/edit">Switch to desktop</a>)</span>
            @else
                <span class="ml-2">(<a href="/admin/virtual-rooms/{{ getsetting('mobile_dashboard_virtual_room_id') ? getsetting('mobile_dashboard_virtual_room_id') : 0 }}/edit?isMobile=true">{{ getsetting('mobile_dashboard_virtual_room_id') != null ? 'Switch to mobile' : 'Create mobile version' }}</a>)</span>
            @endif
        </div>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <button @click.prevent="newClickArea()" class="btn btn-outline-primary">New click area</button>
            <div>
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#changeImageModal">
                    {{ ($virtualRoom->image_path) ? 'Change image' : 'Upload image' }}
                </button>
                <button @click.prevent="saveAreas()" class="btn btn-outline">@lang('general.save') changes</button>
            </div>
        </div>
        <div class="row pt-2" style="border-top: 2px solid #eee;">
            <div class="col-9">
                <div id="container" style="display: inline-block; position: relative; width: 100%; text-align: center;">
                    @if($virtualRoom->image_path)
                    <img src="{{ $virtualRoom->image_url }}" style="width: 100%;">
                    @else
                    <p>Upload an image to begin.</p>
                    @endif
                    <click-area v-for="(area, id) in clickAreas" :url="area.url" :top="area.top" :target="area.target" :left="area.left" :height="area.height" :width="area.width" :selected="focusArea == id" v-on:changed="updateArea(id, $event)" v-bind:key="id" @mousedown.native="focusArea = id"></click-area>
                </div>
            </div>
            <div class="col-3">
                <div v-if="focusArea != null">
                    <div class="card">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0">Click Area</p>
                                <button class="btn btn-sm" @click.prevent="removeFocusArea()">Delete</button>
                            </div>
                            <div class="form-group">
                                <label>Target URL</label>
                                <input type="text" class="form-control form-control-sm" v-model="clickAreas[focusArea].url" v-on:blur="parseUrl(focusArea)">
                            </div>
                            <div class="form-group">
                                <label class="d-block">Link Opens</label>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" name="target" type="radio" v-model="clickAreas[focusArea].target" id="_blank" value="_blank">
                                  <label class="form-check-label" for="_blank">In New Tab</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" name="target" type="radio" v-model="clickAreas[focusArea].target" id="_self" value="_self">
                                  <label class="form-check-label" for="_self">In Same Tab</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="changeImageModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Change background image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" action="/admin/virtual-rooms/{{ $virtualRoom->id }}/change-image" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="form-group">
                    <label for="photoUpload">Upload image</label>
                    <input class="form-control-file" name="photo" id="photoUpload" type="file" required />
                    <small class="text-muted">(Recommended size: 1900x450)</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>
        Vue.component('click-area', {
          data: function () {
            return {}
          },
          props: ['selected', 'url', 'top', 'left', 'height', 'width', 'target'],
          template: '<div class="click-area" @click="updateProps()" :class="{ selectedClickArea: selected }" style="position: absolute; background-color: rgb(255 191 191 / 70%);" v-bind:style="{ top: top, left: left, width: width, height: height }"></div>',
          mounted: function () {
            $(this.$el).draggable({ containment: "#container" }).resizable();
          },
          methods: {
            updateProps: function () {
                this.width = Number($(this.$el).width()/$('#container').width()*100).toFixed(4) + '%';
                this.height = Number($(this.$el).height()/$('#container').height()*100).toFixed(4) + '%';
                this.top = Number($(this.$el).position().top/$('#container').height()*100).toFixed(4) + '%';
                this.left = Number($(this.$el).position().left/$('#container').width()*100).toFixed(4) + '%';

                this.$emit('changed', {
                    width: this.width,
                    height: this.height,
                    top: this.top,
                    left: this.left,
                })
            },
          }
        })
      var app = new Vue({
        el: '#roomBuilder',
        data: {
            clickAreas: [],
            focusArea: null,
        },
        methods: {
            newClickArea: function () {
                this.clickAreas.push({
                    top: '5%',
                    left: '5%',
                    width: '10%',
                    height: '10%',
                    url: null,
                    target: '_self',
                });
                $('.click-area').draggable({
                    containment: "#container"
                })
                .resizable();
            },
            updateArea: function(id, data) {
                console.log(data);
                this.clickAreas[id].top = data.top;
                this.clickAreas[id].left = data.left;
                this.clickAreas[id].width = data.width;
                this.clickAreas[id].height = data.height;
            },
            removeFocusArea: function() {
                this.clickAreas.splice(this.focusArea, 1);
                this.focusArea = null;
            },
            parseUrl: function (focusArea) {
                var link = this.clickAreas[focusArea].url;
                this.clickAreas[focusArea].url = ((link.indexOf('://') === -1) && (link.indexOf('mailto:') === -1) ) ? 'http://' + link : link
            },
            saveAreas() {
                vthis = this;
                $.ajax({
                    url: '/admin/virtual-rooms/{{ $virtualRoom->id }}/areas',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'click_areas': vthis.clickAreas,
                        'is_mobile': '{{ $virtualRoom->is_mobile }}',
                    },
                    success: function () {
                        alert('@lang('general.save')d!');
                    }
                });
            }
        },
        created: function () {
            var areas = JSON.parse('{!! $areas !!}');
            var vthis = this;
            areas.forEach(function (area) {
                vthis.clickAreas.push({
                    top: area.y_coor,
                    left: area.x_coor,
                    width: area.width,
                    height: area.height,
                    url: area.target_url,
                    target: area.a_target,
                });
            });
        }
    });
    </script>
@endsection
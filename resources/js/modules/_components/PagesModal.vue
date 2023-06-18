<template>
    <div id="modal-wrapper">
        <div class="modal fade px-0" id="modal_content" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true"> <button type="button" @click="closepop()"
                class="close position-absolute zindex-dropdown right-0 text-light" data-dismiss="modal"
                aria-label="Close" id="cross-icon"> <span aria-hidden="true">&times;</span> </button>
            <div class="modal-dialog modal-dialog-centered modal-xl m-auto" role="document">
                <div class="modal-content modal-content-pages bg-transparent border-0">
                    <div class="modal-body text-center p-0" id="pop-up"></div>
                </div>
            </div>
        </div>
    </div>
</template>
 
<script>
export default {
    name: 'PagesModal',
}
</script>
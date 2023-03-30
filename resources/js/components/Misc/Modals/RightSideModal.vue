<template>
    <div class="modal modal_outer right_modal fade" id="modal-right-side" tabindex="-1" role="dialog" aria-labelledby="modal-right-side" >
		<div class="modal-dialog" role="document" :style="'width:'+widthModal+';height:'+heightModal+';'">
			<div class="modal-content" :style="'height:'+heightModal+';'">
				<div class="modal-header orange-gradient-2 position-relative">
              <span class="position-absolute ml-3">
                  <i class="text-lg" :class="icon"></i>
              </span>
              <h4 class="modal-title d-block mx-auto" >
                  <slot name="header">Right Side Modal</slot>
              </h4>
				</div>
				<div class="modal-body py-1 px-2" >
					<slot name="body"></slot>
				</div>

			</div>
		</div>
	</div>
</template>

<script>
   export default {
    name: 'RightSideModal',
    props:{
      icon:{
            type:String,
            default:""
        },
        heightModal:{
            type:String,
            default:'',
        },
        widthModal:{
            type:String,
            default:'',
        },
    },
    methods: {
      close() {
        this.$emit('close');
      },
    },
  };
</script>
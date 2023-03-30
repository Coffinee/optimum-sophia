<template>
    <div class="modal modal_outer right_modal fade" :style="'height:'+heightModal+';width:'+widthModal+';'"  id="modal-right-side" tabindex="-1" role="dialog" aria-labelledby="modal-right-side">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header orange-gradient-2 position-relative">
                    <span class="position-absolute ml-3">
                        <i class="text-lg" :class="icon"></i>
                    </span>
                    <h4 class="modal-title d-block mx-auto" >
                        <slot name="header">Right Side Modal</slot>
                    </h4>
				</div>
				<div class="modal-body">
					<slot name="body"></slot>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
   export default {
    name: 'ModalSmall',
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
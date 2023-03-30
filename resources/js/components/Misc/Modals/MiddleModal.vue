<template>
    <div
        class="modal fade"
        :id="id"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-middle"
    >
        <div
            class="modal-dialog"
            role="document"
            :style="'width:' + widthModal + ';height:' + heightModal + ';'"
        >
            <div class="modal-content" :style="'height:' + heightModal + ';'">
                <div class="modal-header orange-gradient-2 position-relative">
                    <span class="position-absolute ml-3">
                        <i class="text-lg" :class="icon"></i>
                    </span>
                    <h4 class="modal-title">
                        <slot name="header">Modal</slot>
                    </h4>
                </div>
                <div class="modal-body py-1 px-0">
                    <slot name="body"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "MiddleModal",
    props: {
        icon: {
            type: String,
            default: "",
        },
        heightModal: {
            type: String,
            default: "",
        },
        widthModal: {
            type: String,
            default: "",
        },
        id: {
            type: String,
            default: "modal-middle",
        },
    },
    methods: {
        close() {
            this.$emit("close");
        },
    },
};
</script>

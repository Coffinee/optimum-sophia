<template>
    <div class="form-floating">
        <input
            :type="type"
            class="form-control"
            :class="{ 'is-invalid': isInvalid }"
            :id="name"
            :uppercased="true"
            :value="modelValue"
            @input="updateValue"
            :disabled="disabled"
            :placeholder="textholder"
            :maxlength="maxlength"
        />
        <label :style="'background-color:' + labelbg" :for="name"
            >{{ label }}<span v-show="required">*</span></label
        >
    </div>
</template>

<script>
export default {
    name: "FloatingInput",
    props: {
        isInvalid: {
            type: Boolean,
            default: false,
        },
        maxlength: {
            type: Number,
        },
        name: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            default: "",
        },
        modelValue: {
            type: [String, Number],
        },
        type: {
            type: String,
            default: "text",
        },
        uppercased: {
            type: Boolean,
            default: true,
        },
        required: {
            type: Boolean,
            default: false,
        },
        textholder: {
            type: String,
            default: "",
        },
        autocomplete: {
            type: String,
            default: null,
        },
        labelbg: {
            type: String,
            default: "none",
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:modelValue"],
    methods: {
        updateValue(event) {
            return this.$emit(
                "update:modelValue",
                this.$props.uppercased
                    ? event.target.value.toUpperCase()
                    : event.target.value
            );
        },
    },
};
</script>

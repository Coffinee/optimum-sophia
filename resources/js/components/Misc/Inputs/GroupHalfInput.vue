<template>
    <div class="input-group mt-3">
        <div class="input-group-prepend">
            <span class="input-group-text" id="half-input-group">{{label}}</span>
        </div>
        <input 
            :id="name"
            :type="type"
            :value="modelValue"
            :required="required"
            @input="updateValue"
            :placeholder="placeholder"
             class="form-control cust-input" @mousedown="resetInput" :class="{'is-invalid': isInvalid}" :disabled="isDisabled" aria-describedby="half-input-group">
    </div>
</template>

<script>
export default {
  data () {
    return {
      reset: false,
    }
  },
  name: "GroupHalfInput",
  inheritAttrs: false,
  props: {
    name: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      default: "",
    },
    isDisabled:{
      type: Boolean,
      default: false,
    },
    modelValue: {
      type: [String, Number],
    },
    isInvalid:{
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      default: "text",
    },
    required: {
      type: Boolean,
      default: false,
    },
    placeholder: {
      type: String,
      default: null,
    },
  },
  emits:["update:modelValue"],
  methods: {
    resetInput(event){
      event.target.classList.remove('is-invalid')
    },
    updateValue(event) {
      return this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>

<template>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text" id="half-input-group">{{label}}</span>
        </div>
        <select class="custom-select border-radius-0" :class="{'is-invalid': isInvalid}" :id="name" :required="required" :placeholder="placeholder">
            <option v-for="(text, value) in options" :value="value" :key="value" :selected="selected == value">
                {{ text }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
  data () {
    return {
      reset: false,
    }
  },
  name: "GroupHalfDropdown",
  inheritAttrs: false,
  props: {
    options:{

    },
    selected:{
        type: String,
        default: "",
    },
    name: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      default: "",
    },
    
    isInvalid:{
      type: Boolean,
      default: false,
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
    updateValue(event) {
      return this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>

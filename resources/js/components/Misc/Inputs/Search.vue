<template>
    <div class="form-group has-search">
        <i class="bi form-control-feedback" :class="icon"></i>
        <input 
        :id="name"
        :value="modelValue"
        @input="updateValue"
        :placeholder="placeholder"
        type="text" class="form-control">
    </div>
</template>

<script>
export default {
  name: "Search",
  inheritAttrs: false,
  props: {
    name: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      default: "",
    },
    modelValue: {
      type: [String, Number],
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
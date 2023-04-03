<template>
    <div class="mb-3">
        <label :for="name" class="form-label m-0">{{label}}</label>
        <div class="input-group">
            <input
            :id="name"
            :type="obscure ? 'text' :type"
            :value="modelValue"
            :required="required"
            @input="updateValue"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            class="form-control shadow-none border-right-none rounded-0 input-left">
            <span class="input-group-text rounded-0 fs-4" :id="name"><i :class="icon"></i></span>
        </div>
    </div>
</template>

<script>
export default {
  data () {
    return {
        obscure:false,
    }
  },
  name: "GroupButtonPassword",
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
    icon: {
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
    showLabel: {
      type: Boolean,
      default: true,
    },
    required: {
      type: Boolean,
      default: false,
    },
    placeholder: {
      type: String,
      default: null,
    },
    autocomplete: {
      type: String,
      default: null,
    },
  },
  emits:["update:modelValue"],
  methods: {
    updateValue(event) {
      return this.$emit("update:modelValue", event.target.value);
    },
    showPass(event){
        this.obscure = !this.obscure;
    }
  },
};
</script>

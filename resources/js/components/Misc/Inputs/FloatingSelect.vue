<template>
    <div class="form-floating vs mb-3">
        <v-select
            :id="name"
            :options="options"
            :placeholder="placeholder"
            :loading="loading"
            :searchable="searchable"
            :value="input"
            @option:selected="update"
        >
        </v-select>
        <label :for="name">{{label}} <span v-show="required">*</span></label>
    </div>
</template>

<style>
    .vs__dropdown-toggle {
        border-radius:0 ;
    }
    .vs__search, .vs__search:focus{
        font-size: 15px;
    }
</style>

<script>
export default {
  name: "FloatingSelect",
  props: {
    options: {
        type: Array,
        default() {
            return [];
        },
    },
    loading: {
            type: Boolean,
            default: false,
    },
    placeholder: {
        type: String,
        default: "",
    },
    searchable: {
        type: Boolean,
        default: true,
    },
    name: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      default: "",
    },
    required: {
      type: Boolean,
      default: false,
    },
    value: {
        default: null
    },
    input: {
      type: [String, Number],
    },
    
    
  },
  methods:{
    update (item) {
        return this.$emit("option:selected", item.value);
    },
  }

 
};
</script>
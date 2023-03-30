<template>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" :value="value" :checked="modelValue" @change="handleCheck" :id="name">
        <label class="form-check-label" :for="name">
            {{label}}
        </label>
    </div>
</template>

<script>
    export default{
        name:"InputCheckbox",
        
        props:{
            value: {
                type: String,
                default: "",
            },
            label:{
                type: String,
                default:""
            },
            name:{
                type: String,
                default:"b-select"
            },
            modelValue: {
            type: Array,
            required: true,
            },
        },
        emits: ['update:modelValue'],
        methods:{
            handleCheck(event){
                return this.$emit('update:modelValue', event.target.checked);
            }
        }
        

    }
</script>


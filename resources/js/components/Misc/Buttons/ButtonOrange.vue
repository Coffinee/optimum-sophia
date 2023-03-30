<template>
    <button class="btn-general">
        <!-- <i class="pr-2" :class="icon"></i> -->
        {{label}}
    </button>
</template>

<script>
export default{
    name:"ButtonOrange",
    props:{
        icon:{
            type: String,
            default:""
        },
        label:{
            type: String,
            default:""
        }
    }
}
</script>
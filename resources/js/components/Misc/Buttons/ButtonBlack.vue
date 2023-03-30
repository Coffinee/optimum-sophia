<template>
    <button class="btn-export">
        <i class="pr-2" :class="icon"></i>
        {{label}}
    </button>
</template>

<script>
export default{
    name:"ButtonBlack",
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
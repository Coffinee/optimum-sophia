<template>
   <router-view :key="$route.fullPath"></router-view>
   <vue-progress-bar></vue-progress-bar>

</template>
<script>
    export default{
        name: 'Guest',
    }
</script>
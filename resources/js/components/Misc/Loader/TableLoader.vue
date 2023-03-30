<template>
    <tr class="table-loader" v-for="i in rowLength" :key="i" v-show="showLoader">
        <td v-for="item in colLength" :key="item"><free-style-shimmer :is-loading="true"  height="15px" width="80%" border-radius="5px"/></td>
        <td class="text-center d-flex justify-content-evenly align-content-center">
            <free-style-shimmer :is-loading="true"  height="15px" width="40px" border-radius="5px"/>
            <free-style-shimmer :is-loading="true"  height="15px" width="40px" border-radius="5px"/>
        </td>
    </tr>
    
</template>

<script>
export default{
    name:"TableLoader",
    props: {
        showLoader:{
            type:Boolean,
            default: false,
        },
        colLength:{
            type:Number,
            default: 5
        },
        rowLength:{
            type:Number,
            default: 10
        }

    },
}
</script>
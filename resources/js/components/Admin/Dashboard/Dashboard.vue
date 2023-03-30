<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">


<div class="col-lg-12 mb-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-right text-white">DASHBOARD</h6>
        </div>
        <div class="card-body">
        <!-- <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?    </p>
            <p >Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?</p>
            <p >Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?</p>
            <p >Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?</p>
            <p >Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?</p>
            <p >Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?</p>
            <p >Lorem ipsum dolor sit amet consectetur, adipisicing elit. Harum vel voluptates debitis ipsum nisi. Consectetur dolor tempora voluptas, cumque perspiciatis voluptate labore distinctio minus rerum ab autem. Placeat, tenetur esse?</p> -->
        </div>
    </div>
</div>
</div>
</template>

<script>
import PageHeading from "@/components/Layouts/parts/PageHeading.vue";
export default {
    name:"dashboard",
    components:{
        PageHeading
    },
    data(){
        return {
            user:this.$store.state.auth.user
        }
    }
}
</script>
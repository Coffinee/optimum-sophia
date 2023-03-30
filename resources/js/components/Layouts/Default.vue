<template>
    <div id="page-top">
        <div id="wrapper">
            <Sidebar :toogleSidebar="toggled ? 'toggled' : ''"></Sidebar>
            <div id="content-wrapper" class="d-flex flex-column" >
                <div id="content">
                    <Topbar></Topbar>
                    <div class="container-fluid main-content">
                        <router-view :key="$route.fullPath"></router-view>
                        <vue-progress-bar></vue-progress-bar>
                    </div>
                </div>
                <footer class="sticky-footer bg-white" :class="{'w-100' :toggled}">
                    <div class="container my-auto">
                        <div class="copyright text-right">
                            <span class="text-uppercase">DATE TODAY: {{current_date}}</span>
                        </div>
                    </div>
                </footer>
            </div>
            <div class="toogler-sidebar text-center d-none d-md-inline" :class="{'toogler-was-toggled' : toggled }">
                <a @click="toggleSidebar" href="#"><i class="bi " :class="{'bi-chevron-right' : toggled ,  'bi-chevron-left' : !toggled}"></i> </a>
            </div>
            <a class="scroll-to-top rounded" href="#page-top">
                <i class="fas fa-angle-up"></i>
            </a>
        </div>
    </div>
</template>

<script>
import {mapActions} from 'vuex'
import Sidebar from "@/components/Layouts/parts/Sidebar.vue";
import Topbar from "@/components/Layouts/parts/Topbar.vue";
export default {
    name:"default-layout",
    components:{
        Sidebar,
        Topbar
    },  
    data(){
        return {
            user:this.$store.state.auth.user,
            toggled:false,
            current_date: this.$moment().endOf("day").format('LL'),
        }
    },
    methods:{
        ...mapActions({
            signOut:"auth/logout"
        }),
        async logout(){
            await axios.post('/logout').then(({data})=>{
                this.signOut()
                this.$router.push({name:"login"})
            })
        },
        toggleSidebar(){
            this.toggled = !this.toggled
        },
    }
}
</script>

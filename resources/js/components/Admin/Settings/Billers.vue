<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card card-main mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0  text-right text-white">BILLERS</h6>
                </div>
                <div class="card-body">
                    <div class="row g-1 justify-content-between">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group has-search">
                                        <i class="bi bi-search form-control-feedback"></i>
                                        <input v-model="search"  type="text"  v-debounce:500ms="searchBiller" name="search" class="form-control" placeholder="Search" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <ButtonBlack  icon="bi bi-download" label="Export"></ButtonBlack>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <ButtonOrange  icon="bi bi-plus-circle"  label="Add Biller" @click="gotoForm"></ButtonOrange>
                        </div>
                    </div>
                    <div class="col-md-12 py-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" /></th>
                                    <th>CATEGORY</th>
                                    <th>BILLER NAME</th>
                                    <th>FIELDS</th>
                                    <th>DATA ADDED</th>
                                    <th class="text-center" style="width:10%;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <TableLoader :showLoader="isLoading" :colLength="5"></TableLoader>
                                <tr v-if="showNoFound">
                                    <td colspan="6"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                                </tr>
                                <tr v-for="item in billers.data"  :key="item.id">
                                    <td>
                                        <input type="checkbox" >
                                    </td>
                                    <td scope="row">{{ item.biller_category_name.meta_value }}</td>
                                    <td>{{ item.name }}</td>
                                    <td>
                                        <p class="m-0" v-for="(val,index) in JSON.parse(item.fields)" :key="index">
                                            Type: {{val.type}}, Label: {{val.label}}, Width: {{val.width}}, Required: {{val.require}}, {{val.type == 'select' ? 'Options:'+ val.options.map(el =>  el.text ).join(', ') : ''}} 
                                        </p>
                                    </td>
                                    <td> {{  $filters.dateFormat(item.created_at) }} </td>
                                    <td class="text-center">
                                        <router-link :to="{ name: 'billers-edit', params: { data: JSON.stringify(item), },}"  class="rounded-edit-button" >
                                            <i class="bi bi-pencil-fill"></i>
                                        </router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import LaravelVuePagination from "laravel-vue-pagination";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
export default {
    name:"billers",
    components:{
        ButtonOrange,
        ButtonBlack,
        LaravelVuePagination,
        TableLoader,
    },
    data(){
        return {
            editmode:false,
            search:'',
            isLoading:true,
            showNoFound: false,
            billers:{},
            user:this.$store.state.auth.user,
        }
    },
    methods:{
        searchBiller(){

        },
        gotoForm(){
            this.$router.push('/app/settings/biller/create')
        },
        saveBiller(){

        },
        updateBiller(){

        },
        loadData(){
            axios.get('/api/billers')
                .then((data) => {
                    this.isLoading = false;
                    this.showNoFound = data.data.data.total == 0 ? true : false
                    this.billers = data.data.data;
                })
                .catch(() => {
                    console.log("err");
                });
        }
    },
    created(){
        this.loadData()
        // axios.get("/api/get-biller-categories").then(({ data }) => (this.billerCategories = data.data));
    }
}
</script>
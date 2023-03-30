<template>
    <div class="col-md-6">
        <div class="table-holder mb-4">
            <div class="row">
                <div class="col-md-12 h-section">
                    <h3 class="title">BILLER CATEGORY</h3>
                    <p class="description">A comprehensive list of biller category from EOS.</p>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>TRANSACTION TYPE</th>
                        <th>NAME</th>
                    </tr>
                </thead>
                <tbody>
                    <TableLoader :showLoader="loading" :colLength="3"></TableLoader>
                    <tr v-if="showNotFound">
                        <td colspan="4"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                    </tr>
                    <tr v-for="item in biller_categories"  :key="item.id">
                        <td>{{ item.meta_name }}</td>
                        <td>{{ item.meta_value }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <LaravelVuePagination class="m-0" :data="biller_categories" @pagination-change-page="paginatedBillers" ></LaravelVuePagination>
            </div>
            
        </div>
    </div>
</template>

<script>
import { errorMessage, successMessage, eosGetApi } from '@/utilities.js'
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
import LaravelVuePagination from "laravel-vue-pagination";
import axios from 'axios';
export default {
    name:"billers",
    props:{
        biller_categories: {
            type: Array,
            default: {},
        },
        loading:{
            type: Boolean,
            default:true,
        },
        showNotFound:{
            type: Boolean,
            default:true,  
        }
    },
    components:{
        TableLoader,
        LaravelVuePagination
    },
    data(){
        return {
            token:'',
            searchFund:'',
            isLoading:true,
            showNoFound: false,
        }
    },
    methods:{
    //    async loadData(){
    //     await axios.get('/api/get-inbound-api-token').then((data)=> { this.token = data.data.token });

    //     await axios.get(this.$eosUrl+'/api/outbound/billers', this.$eosToken(this.token)).then((data) =>{
    //             this.showNoFound = data.data.data.total == 0 ? true : false
    //             this.billers = data.data.data;
    //             this.isLoading = false;
    //         }).catch((e) => {
    //             errorMessage('Opps!', e.message, 'top-right')
    //         });
    //     },
    //      paginatedBillers(page = 1) {
    //          axios.get(this.$eosUrl+"/api/outbound/billers?page=" + page, this.$eosToken(this.token)).then((data) =>{
    //             this.billers = data.data.data;
    //         }).catch((e) => {
    //             errorMessage('Opps!', e.message, 'top-right')
    //         });
    //     },
    },
    created(){
       
      //  this.loadData()
        //console.log(eosApi('/api/outbound/billers'))
    }
}
</script>
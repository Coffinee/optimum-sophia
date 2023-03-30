<template>
    <div class="col-md-12">
        <div class="table-holder mb-4">
            <div class="row">
                <div class="col-md-12 h-section">
                    <h3 class="title">BRANCH</h3>
                    <p class="description">A comprehensive list of branches from EOS.</p>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>COMPANY NAME</th>
                        <th>BRANCH NAME</th>
                        <th>REFERENCE NUMBER</th>
                        <th>CONTACT #</th>
                        <th>DATE ADDED</th>
                    </tr>
                </thead>
                <tbody>
                    <TableLoader :showLoader="loading" :colLength="4"></TableLoader>
                    <tr v-if="showNotFound">
                        <td colspan="5"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                    </tr>
                    <tr v-for="item in branches"  :key="item.id">
                        <td>{{ item.company_name.name }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.reference_number }}</td>
                        <td>{{ item.contact_number }}</td>
                        <td>{{ $filters.dateFormat(item.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <LaravelVuePagination class="m-0" :data="branches" @pagination-change-page="paginatedBranches" ></LaravelVuePagination>
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
    name:"branches",
    props:{
        branches: {
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

    //     await axios.get(this.$eosUrl+'/api/outbound/branches', this.$eosToken(this.token)).then((data) =>{
    //             this.showNoFound = data.data.data.total == 0 ? true : false
    //             this.branches = data.data.data;
    //             this.isLoading = false;
    //         }).catch((e) => {
    //             errorMessage('Opps!', e.message, 'top-right')
    //         });
    //     },
    //      paginatedBranches(page = 1) {
    //          axios.get(this.$eosUrl+"/api/outbound/branches?page=" + page, this.$eosToken(this.token)).then((data) =>{
    //             this.branches = data.data.data;
    //         }).catch((e) => {
    //             errorMessage('Opps!', e.message, 'top-right')
    //         });
    //     },
    },
    created(){
       
      //  this.loadData()
        //console.log(eosApi('/api/outbound/branches'))
    }
}
</script>
<template>
    <div class="col-md-6">
        <div class="table-holder mb-4">
            <div class="row">
                <div class="col-md-12 h-section">
                    <h3 class="title">TRANSACTION TYPES</h3>
                    <p class="description">A comprehensive list of Transaction Types from EOS.</p>
                </div>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>CODE</th>
                        <th>NAME</th>
                    </tr>
                </thead>
                <tbody>
                    <TableLoader :showLoader="loading" :colLength="4"></TableLoader>
                    <tr v-if="showNotFound">
                        <td colspan="5"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                    </tr>
                    <tr v-for="item in transaction_types"  :key="item.id">
                        <td>{{ item.code }}</td>
                        <td>{{ item.name }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                <LaravelVuePagination class="m-0" :data="transaction_types" @pagination-change-page="paginatedBranches" ></LaravelVuePagination>
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
    name:"operation",
    props:{
        transaction_types: {
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
            branches:{},
            showNoFound: false,
        }
    },
    methods:{

    },
    created(){

    }
}
</script>
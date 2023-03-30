<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-right text-white">
                        REFUND REQUEST
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a @click.prevent="setTab('request')" class="nav-link" :class="{'active' : tabSet == 'request'}" aria-current="page" href="#">REFUND REQUESTS</a>
                        </li>
                        <li class="nav-item">
                            <a @click.prevent="setTab('history')" class="nav-link" :class="{'active' : tabSet == 'history'}" href="#">REFUND HISTORY</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div v-show="tabSet == 'request'">
                            <div class="row g-1 justify-content-end py-3" >
                                <div class="col-md-6">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-6">
                                            <div class="form-group has-search">
                                                <i class="bi bi-search form-control-feedback"></i>
                                                <input v-model="search"  type="text"  v-debounce:500ms="searchData" name="search" class="form-control" placeholder="Search" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <ButtonBlack  icon="bi bi-download" label="Export"></ButtonBlack>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 py-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>TXN NO.</th>
                                            <th>DATE</th>
                                            <th>REFERENCE NO.</th>
                                            <th>REMITTER</th>
                                            <th>BENEFICIARY</th>
                                            <th>TRANSACTION TYPE</th>
                                            <th>CURRENCY</th>
                                            <th>AMOUNT</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <TableLoader :showLoader="isLoading" :colLength="8"></TableLoader>
                                        <tr v-if="showNoFound">
                                            <td colspan="9"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-show="tabSet == 'history'">
                            <div class="row g-1 justify-content-end py-3" >
                                <div class="col-md-6">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-6">
                                            <div class="form-group has-search">
                                                <i class="bi bi-search form-control-feedback"></i>
                                                <input v-model="search"  type="text"  v-debounce:500ms="searchData" name="search" class="form-control" placeholder="Search" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <ButtonBlack  icon="bi bi-download" label="Export"></ButtonBlack>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 py-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>TXN NO.</th>
                                            <th>DATE</th>
                                            <th>REFERENCE NO.</th>
                                            <th>REMITTER</th>
                                            <th>BENEFICIARY</th>
                                            <th>TRANSACTION TYPE</th>
                                            <th>CURRENCY</th>
                                            <th>AMOUNT</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <TableLoader :showLoader="isLoading" :colLength="8"></TableLoader>
                                        <tr v-if="showNoFound">
                                            <td colspan="9"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import { Button, HasError, AlertError } from "vform/src/components/bootstrap5";
import { errorMessage, successMessage } from '@/utilities.js'
import LaravelVuePagination from "laravel-vue-pagination";

import Search from "@/components/Misc/Inputs/Search.vue";
import ButtonOrange from "@/components/Misc/Buttons/ButtonOrange.vue";
import ButtonBlack from "@/components/Misc/Buttons/ButtonBlack.vue";
import GroupHalfInput from "@/components/Misc/Inputs/GroupHalfInput.vue";
import RightSideModal from "@/components/Misc/Modals/RightSideModal.vue";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
export default {
    props: {},
    name: "refund-request",
    components: {
        Search,
        ButtonOrange,
        ButtonBlack,
        RightSideModal,
        GroupHalfInput,
        HasError,
        LaravelVuePagination,
        TableLoader,
    },
    data() {
        return {
            tabSet: "request",
            user: this.$store.state.auth.user,
        };
    },
    methods:{
        setTab(tab){
            this.tabSet = tab
        },
        loadData(){

            setTimeout(() => {
                    this.isLoading = false
                    this.showNoFound = true
                    }, 2500); 
            // axios.get('/api/transactions')
            //     .then((data) => {
            //         this.isLoading = false;
            //         this.transactions = data.data.data;
            //     })
            //     .catch(() => {
            //         console.log("err");
            //     });
        },
        searchData(){

        }
    },  
    created(){
        this.loadData()
    }
};
</script>

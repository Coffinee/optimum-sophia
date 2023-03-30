<template>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0  text-right text-white">OTHER MAINTENANCE</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a @click.prevent="setTab('companies')" class="nav-link" :class="{'active' : tabSet == 'companies'}" aria-current="page" href="#">COMPANIES</a>
                        </li>
                        <li class="nav-item">
                            <a @click.prevent="setTab('branches')" class="nav-link" :class="{'active' : tabSet == 'branches'}" aria-current="page" href="#">BRANCHES</a>
                        </li>
                        <li class="nav-item">
                            <a @click.prevent="setTab('billers')" class="nav-link" :class="{'active' : tabSet == 'billers'}" aria-current="page" href="#">BILLERS</a>
                        </li>
                        <li class="nav-item">
                            <a @click.prevent="setTab('operation')" class="nav-link" :class="{'active' : tabSet == 'operation'}" aria-current="page" href="#">OPERATION</a>
                        </li>
                        <li class="nav-item">
                            <a @click.prevent="setTab('source-of-fund')" class="nav-link" :class="{'active' : tabSet == 'source-of-fund'}" href="#">SOURCE OF FUNDS</a>
                        </li>
                        <li class="nav-item">
                            <a @click.prevent="setTab('api-access')" class="nav-link" :class="{'active' : tabSet == 'api-access'}" href="#">API ACCESS</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div v-show="tabSet == 'companies'">
                            <div class="row py-3 justify-content-center">
                                <Companies :companies="companies.data" :loading="isLoading" :showNotFound="showNoFound"></Companies>
                            </div>
                        </div>
                        <div v-show="tabSet == 'branches'">
                            <div class="row py-3 justify-content-center">
                                <Branch :branches="branches.data" :loading="isLoading" :showNotFound="showNoFound"></Branch>
                            </div>
                        </div>
                        <div v-show="tabSet == 'billers'">
                            <div class="row py-3 justify-content-center">
                                <BillerCategory :biller_categories="biller_categories.data" :loading="isLoading" :showNotFound="showNoFound"></BillerCategory>
                                <Billers :billers="billers.data" :loading="isLoading" :showNotFound="showNoFound"></Billers>
                            </div>
                        </div>
                        <div v-show="tabSet == 'operation'">
                            <div class="row py-3 justify-content-center">
                                <TransactionType :transaction_types="transaction_types.data" :loading="isLoading" :showNotFound="showNoFound"></TransactionType>
                                <Banks :banks="banks.data" :loading="isLoading" :showNotFound="showNoFound"></Banks>
                            </div>
                        </div>
                        <div v-show="tabSet == 'source-of-fund'">
                            <div class="row py-3">
                                <SourceFunds :source_of_funds="source_of_funds.data" :loading="isLoading" :showNotFound="showNoFound"></SourceFunds>
                            </div>
                        </div>
                        <div v-show="tabSet == 'api-access'">
                            <div class="row py-3 justify-content-center">
                                <APIAccess></APIAccess>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { errorMessage, successMessage, eosGetApi } from '@/utilities.js'
import Banks from "@/components/Admin/Settings/Others/Banks.vue";
import SourceFunds from "@/components/Admin/Settings/Others/SourceFunds.vue";
import BillerCategory from "@/components/Admin/Settings/Others/BillerCategory.vue";
import TransactionType from "@/components/Admin/Settings/Others/TransactionType.vue";
import APIAccess from "@/components/Admin/Settings/Others/APIAccess.vue";
import Companies from "@/components/Admin/Settings/Others/Company.vue";
import Branch from "@/components/Admin/Settings/Others/Branch.vue";
import Billers from "@/components/Admin/Settings/Others/Billers.vue";
import Operation from "@/components/Admin/Settings/Others/Operation.vue";


export default {
    name:"others",
    components:{
        Banks,
        SourceFunds,
        BillerCategory,
        TransactionType,
        APIAccess,
        Companies,
        Branch,
        Billers,
        Operation
    },
    data(){
        return {
            tabSet: "companies",
            token:'',
            companies:{},
            branches:{},
            biller_categories:{},
            billers:{},
            transaction_types:{},
            banks:{},
            source_of_funds:{},
            isLoading:true,
            showNoFound: false,
        }
    },
    methods:{
       async setTab(tab){
            this.tabSet = tab
            this.isLoading = true
            switch (tab) {
                case 'companies':
                    this.companies = {}
                    await axios.get(this.$eosUrl+'/api/outbound/companies', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.companies = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });
                    break;

                case 'branches':
                    this.branches={}
                    await axios.get(this.$eosUrl+'/api/outbound/branches', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.branches = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });
                    break;

                case 'billers':

                    this.biller_categories = {}
                    this.billers = {}

                    await axios.get(this.$eosUrl+'/api/outbound/biller-categories', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.biller_categories = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });

                    
                    await axios.get(this.$eosUrl+'/api/outbound/billers', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.billers = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });
                    break;
                
                case 'operation':
                    
                    this.transaction_types = {}
                    this.banks = {}

                    await axios.get(this.$eosUrl+'/api/outbound/transaction-types', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.transaction_types = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });

                    await axios.get(this.$eosUrl+'/api/outbound/banks', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.banks = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });

                    break;

                case 'source-of-fund':
                    
                    this.source_of_funds = {}
                    await axios.get(this.$eosUrl+'/api/outbound/source-of-funds', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.source_of_funds = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });
                    
                    break;
                    

                default:
                    this.companies = {}
                    await axios.get(this.$eosUrl+'/api/outbound/companies', { 'headers': { 'Authorization': 'Bearer '+ this.token } }).then((data) =>{
                        this.showNoFound = data.data.data.total == 0 ? true : false
                        this.companies = data.data.data;
                        this.isLoading = false;
                    }).catch((e) => {
                        errorMessage('Opps!', e.message, 'top-right')
                    });

                    break;
            }
        },
    },
    created(){
         axios.get('/api/get-inbound-api-token').then((data)=> { this.token = data.data.token }).finally((e)=> { this.setTab('companies')  });
    }
}
</script>
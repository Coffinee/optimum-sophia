<template>
    <!-- <PageHeading></PageHeading> -->
    <div class="row">


<div class="col-lg-12 mb-4">
    <div class="card card-main mb-4">
        <div class="card-header py-3">
            <h6 class="m-0  text-right text-white">RATE MANAGEMENT</h6>
        </div>
        <div class="card-body">
            <div class="col-md-12 py-2 table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th v-if="user.role == 'admin'">Branch</th>
                            <th>FROM CURRENCY</th>
                            <th>TO CURRENCY</th>
                            <th style="width: 220px;">EXCHANGE RATE</th>
                            <th>LAST DATE UPDATED</th>
                            <th>LAST DATE UPDATED BY</th>
                            <th v-if="user.role != 'admin'" style="width:5%" class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <TableLoader :showLoader="isLoading" :colLength="5"></TableLoader>
                        <tr v-if="showNoFound">
                            <td colspan="6"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                        </tr>
                        <tr v-for="item in rates.data"  :key="item.id">
                            <td v-if="user.role == 'admin'">{{item.branch_name != null ?  item.branch_name.name : ''}}</td>
                            <td>{{ item.from_currency.currency }}</td>
                            <td>{{ item.to_currency.currency }}</td>
                            <td style="padding: 2px;">
                                <div class="input-group rate-input" v-show="selectedRow == item.id">
                                    <input type="text"  @keyup.enter="updateRate(item.id, $event)" @keypress="isNumber()" class="form-control" :disabled="disabled" :class="'rate-input-'+item.id"  :value="item.rate">
                                    <button @click.prevent="updateRateButton(item.id)" class="btn" type="button" :disabled="disabled"><i class="bi bi-check-lg"></i></button>
                                </div>
                                 <span v-show="selectedRow != item.id"> {{item.rate}}</span></td>
                            <td>{{  $filters.dateFormat(item.updated_at) }}</td>
                            <td>{{item.last_update_by}} {{item.last_update_by}} - {{ $filters.capitalize(user.role)}}</td>
                            <td v-if="user.role != 'admin'" class="text-center">
                                <a @click.prevent="editRate(item.id)" class="rounded-s-button" href="#" >
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
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
import Form from "vform";
import { Button, HasError, AlertError } from "vform/src/components/bootstrap5";
import { errorMessage, successMessage } from '@/utilities.js'

import LaravelVuePagination from "laravel-vue-pagination";
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
import axios from 'axios';
export default {
    name:"rate-management",
    components: {
        HasError,
        LaravelVuePagination,
        TableLoader,
    },
    data(){
        return {
            isLoading:true,
            rates:[],
            outboundToken:'',
            inboundToken:'',
            user:this.$store.state.auth.user,
            editMood: false,
            selectedRow:null,
            showNoFound: false,
            rate_id:'',
            disabled:true,
            disableMouseLeave: false,
        }
    },
    methods: {
        isNumber: function(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
            evt.preventDefault();
                errorMessage("Opps!", "Please enter number only!", "top-right");
            } else {
            return true;
            }
        },
        setEnabledInput(){
            this.disabled = false
        },
        showInput(id){
            if(id == null) this.disabled = true
            this.selectedRow = id
        },
        editRate(id){
            this.disabled = false
            if(this.selectedRow == id){
                this.showInput('')
            }else{
                this.selectedRow = id
            }
        },
        updateRateButton(id){
           var amount = jQuery('.rate-input-'+id).val()
           Swal.fire({
                title: 'Are you sure to update this exchange rate?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
                }).then(async (result) => {
                if (result.isConfirmed) {
                    this.$Progress.start();
                    await axios.put(this.$eosUrl+'/api/inbound/rates/'+id, { 
                        'amount': amount,
                        'user_id': this.user.id,
                        },{'headers': { 'Authorization': 'Bearer '+ this.outboundToken }}).then((data) =>{
                            this.getRates();
                            successMessage('Updated!', 'Service fee has been updated!', 'top-right')
                            this.showInput('')
                            this.$Progress.finish();
                        }).catch((e) => {
                            errorMessage('Opps!', e.message, 'top-right')
                            this.$Progress.fail();
                        });
                }
                })
        },
        updateRate(id,event){
            var amount = event.target.value
            Swal.fire({
                title: 'Are you sure to update this exchange rate?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
                }).then(async (result) => {
                if (result.isConfirmed) {
                    this.$Progress.start();
                    await axios.put(this.$eosUrl+'/api/inbound/rates/'+id, { 
                        'amount': amount,
                        'user_id': this.user.id,
                        },{'headers': { 'Authorization': 'Bearer '+ this.outboundToken }}).then((data) =>{
                            this.getRates();
                            successMessage('Updated!', 'Service fee has been updated!', 'top-right')
                            this.showInput('')
                            this.$Progress.finish();
                        }).catch((e) => {
                            errorMessage('Opps!', e.message, 'top-right')
                            this.$Progress.fail();
                        });
                }
                })
            
        },
       async getRates(){
            await axios.get(this.$eosUrl+'/api/outbound/rates', { 
                params:{
                    'role': this.user.role,
                    'branch_id': this.user.branch_id,
                },
                'headers': { 'Authorization': 'Bearer '+ this.inboundToken } 
                }).then((data) =>{
                    this.rates = data.data.data;
                }).catch((e) => {
                    errorMessage('Opps!', e.message, 'top-right')
                });
        },
    },
    created(){
        axios.get('/api/get-outbound-api-token').then((data)=> { this.outboundToken = data.data.token })
        axios.get('/api/get-inbound-api-token').then((data)=> { this.inboundToken = data.data.token }).finally(async (e)=> { 
            await axios.get(this.$eosUrl+'/api/outbound/rates', { 
                params:{
                    'role': this.user.role,
                    'branch_id': this.user.branch_id,
                },
                'headers': { 'Authorization': 'Bearer '+ this.inboundToken } 
                }).then((data) =>{
                    this.showNoFound = data.data.data.total == 0 ? true : false
                    this.rates = data.data.data;
                    this.isLoading = false;
                }).catch((e) => {
                    errorMessage('Opps!', e.message, 'top-right')
                });

            });
    }
}
</script>
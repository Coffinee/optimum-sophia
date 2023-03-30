<template>
    <div class="col-md-12">
        <div class="table-holder mb-4">
            <div class="row">
                <div class="col-md-6 h-section">
                    <h3 class="title">API ACCESS</h3>
                    <p class="description">A list of all the API token for API integration and authencation in SOPHIA.</p>
                </div>
                <div class="col-md-6">
                    <form @submit.prevent="updateData()">
                        <div class="row g-1 py-3 px-2 bg-emerald-300">
                            <div class="col-11">
                                <FloatingInput v-model="formAPI.api_token" :uppercased="false" label="API TOKEN" labelbg="rgb(243 244 246 / .8)" :isInvalid="formAPI.errors.has('api_token')" name="api_token"  :required="true" textholder="Enter api token"></FloatingInput>
                                <HasError :form="formAPI" field="api_token" />
                            </div>
                            <div class="col-1 text-end">
                                <button type="submit" :disabled="formAPI.busy || clickEdit" :class="[clickEdit ? 'btn-card-disabled' :'btn-card-add']" > <i class="bi bi-check2-circle"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th> INTENDED FOR</th>
                        <th> API TOKEN</th>
                        <th class="text-center" style="width:10%;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <TableLoader :showLoader="isLoading" :colLength="1"></TableLoader>
                    <tr v-if="showNoFound">
                        <td colspan="3"><img class="d-block mx-auto py-3 animate__animated animate__fadeIn img-fluid no-data-img" src="/images/no-data.png" alt=""></td>
                    </tr>
                    <tr v-for="item in apiData.data"  :key="item.id">
                        <td>{{ item.name }}</td>
                        <td>{{ item.api_token }}</td>
                        <td class="text-center d-flex justify-content-center">
                            <a @click.prevent="editFund(item)" class="rounded-s-button" href="#" >
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import Form from "vform";
import { HasError } from "vform/src/components/bootstrap5";
import { errorMessage, successMessage } from '@/utilities.js'
import TableLoader from "@/components/Misc/Loader/TableLoader.vue";
import FloatingInput from "@/components/Misc/Inputs/FloatingInput.vue";
import LaravelVuePagination from "laravel-vue-pagination";
export default {
    name:"api-access",
    components:{
        FloatingInput,
        TableLoader,
        HasError,
        LaravelVuePagination
    },
    data(){
        return {
            searchFund:'',
            clickEdit:true,
            isLoading:true,
            apiData:{},
            showNoFound: false,
            formAPI: new Form({
                id:'',
                api_token:'',
            }),
        }
    },
    methods:{
        editFund(item){
            this.formAPI.fill(item)
            this.clickEdit = false
        },
        updateData(){
            this.$Progress.start();
            this.formAPI
                .put("/api/api-tokens/" + this.formAPI.id)
                .then((res) => {
                    successMessage('Updated!','API has been updated.','top-right')
                    this.$Progress.finish();
                    this.loadData();
                    this.formAPI.reset();
                    this.clickEdit = true
                })
                .catch(() => {
                    this.$Progress.fail();
                })
        },
        loadData(){
            axios.get('/api/api-tokens')
            .then((data) => {
                this.showNoFound = data.data.total == 0 ? true : false
                this.apiData = data.data;
                this.isLoading = false;
            })
            .catch(() => {
                console.log("err");
            });
        },
    },
    created(){
        this.loadData()
    }
}
</script>